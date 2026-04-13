<?php

namespace App\Http\Controllers;

use App\Models\InsuranceClaim;
use App\Models\Sale;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class InsuranceClaimController extends Controller
{
    // ── Pharmacist ──────────────────────────────────────────────

    public function index()
    {
        $claims = InsuranceClaim::with(['patient.user', 'sale.medicine', 'submittedBy'])
            ->latest()
            ->get();

        $totalClaimed  = $claims->sum('claimed_amount');
        $totalApproved = $claims->sum('approved_amount');
        $totalPaid     = $claims->where('status', 'paid')->sum('approved_amount');
        $totalPending  = $claims->whereIn('status', ['submitted', 'under_review'])->count();

        return view('pharmacist.claims.index', compact(
            'claims',
            'totalClaimed',
            'totalApproved',
            'totalPaid',
            'totalPending'
        ));
    }

    public function create()
    {
        // Only insurance sales not yet claimed
        $sales = Sale::where('payment_method', 'insurance')
            ->whereDoesntHave('insuranceClaim')
            ->with(['patient.user', 'medicine'])
            ->latest()
            ->get();

        $patients = Patient::with('user')->get();

        return view('pharmacist.claims.create', compact('sales', 'patients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sale_id'       => 'required|exists:sales,id|unique:insurance_claims,sale_id',
            'patient_id'    => 'required|exists:patients,id',
            'insurer_name'  => 'required|string|max:255',
            'policy_number' => 'required|string|max:100',
            'member_number' => 'nullable|string|max:100',
            'claimed_amount' => 'required|numeric|min:0',
            'patient_copay' => 'nullable|numeric|min:0',
            'due_date'      => 'nullable|date',
            'notes'         => 'nullable|string',
        ]);

        InsuranceClaim::create([
            ...$validated,
            'submitted_by'  => Auth::id(),
            'status'        => 'draft',
            'patient_copay' => $validated['patient_copay'] ?? 0,
        ]);

        return redirect()->route('pharmacist.claims.index')
            ->with('message', 'Claim created successfully.');
    }

    public function submit($id)
    {
        $claim = InsuranceClaim::findOrFail($id);

        if ($claim->status !== 'draft') {
            return redirect()->back()->with('error', 'Only draft claims can be submitted.');
        }

        $claim->update([
            'status'       => 'submitted',
            'submitted_at' => Carbon::today(),
        ]);

        // Update the linked sale payment status
        $claim->sale->update(['payment_status' => 'billed']);

        return redirect()->route('pharmacist.claims.index')
            ->with('message', 'Claim submitted to insurer.');
    }

    public function show($id)
    {
        $claim = InsuranceClaim::with([
            'patient.user',
            'sale.medicine',
            'sale.prescription',
            'submittedBy',
            'reviewedBy'
        ])->findOrFail($id);

        return view('pharmacist.claims.show', compact('claim'));
    }

    // ── Admin ────────────────────────────────────────────────────

    public function admin_index()
    {
        $claims = InsuranceClaim::with(['patient.user', 'sale.medicine', 'submittedBy'])
            ->latest()
            ->get();

        $totalClaimed  = $claims->sum('claimed_amount');
        $totalApproved = $claims->sum('approved_amount');
        $totalPaid     = $claims->where('status', 'paid')->sum('approved_amount');
        $totalPending  = $claims->whereIn('status', ['submitted', 'under_review'])->count();
        $totalRejected = $claims->where('status', 'rejected')->count();

        return view('admin.claims.index', compact(
            'claims',
            'totalClaimed',
            'totalApproved',
            'totalPaid',
            'totalPending',
            'totalRejected'
        ));
    }

    public function update_status(Request $request, $id)
    {
        $claim = InsuranceClaim::findOrFail($id);

        $validated = $request->validate([
            'status'           => 'required|in:under_review,approved,partial,rejected,paid,appealed',
            'approved_amount'  => 'nullable|numeric|min:0',
            'patient_copay'    => 'nullable|numeric|min:0',
            'rejection_reason' => 'nullable|string',
            'payment_reference' => 'nullable|string|max:100',
            'payment_date'     => 'nullable|date',
            'response_date'    => 'nullable|date',
            'notes'            => 'nullable|string',
        ]);

        $claim->update([
            ...$validated,
            'reviewed_by' => Auth::id(),
        ]);

        // If paid, update the linked sale
        if ($validated['status'] === 'paid') {
            $claim->sale->update(['payment_status' => 'paid']);
        }

        // If rejected, flag sale as pending (patient may need to pay)
        if ($validated['status'] === 'rejected') {
            $claim->sale->update(['payment_status' => 'pending']);
        }

        // After $claim->update([...])
        app(\App\Services\NotificationService::class)->claimStatusChanged($claim);

        return redirect()->route('admin.claims.index')
            ->with('message', 'Claim status updated.');
    }
}
