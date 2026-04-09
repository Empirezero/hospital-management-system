<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Sale;
use App\Models\Medicine;
use App\Models\Patient;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesController extends Controller
{
    public function create()
    {
        $medicines     = Medicine::where('stock', '>', 0)->get();
        $prescriptions = Prescription::where('status', 'pending')
            ->with(['patient', 'medicine', 'doctor'])
            ->get();
        $patients      = Patient::with('user')->get();

        return view('pharmacist.add_sales', compact('medicines', 'prescriptions', 'patients'));
    }

    public function add_sale(Request $request)
    {
        $validated = $request->validate([
            'medicine_id'       => 'required|exists:medicines,id',
            'quantity_sold'     => 'required|integer|min:1',
            'sale_type'         => 'required|in:prescription,otc,insurance',
            'payment_method'    => 'required|in:cash,mpesa,insurance,credit,billed',
            'payment_reference' => 'nullable|string|max:100',
            'prescription_id'   => 'nullable|exists:prescriptions,id',
            'patient_id'        => 'nullable|exists:patients,id',
            'billed_to'         => 'required_if:payment_method,billed|nullable|string|max:255',
            'bill_due_date'     => 'required_if:payment_method,billed|nullable|date|after:today',
        ]);

        $inventory = Inventory::where('medicine_id', $validated['medicine_id'])
            ->latest()->firstOrFail();

        if ($inventory->current_stock < $validated['quantity_sold']) {
            return redirect()->back()
                ->with('error', 'Insufficient stock. Available: ' . $inventory->current_stock);
        }

        $total_price = $inventory->price * $validated['quantity_sold'];

        // Determine payment status
        $payment_status = match ($validated['payment_method']) {
            'billed'    => 'billed',
            'insurance' => 'pending',
            default     => 'paid',
        };

        Sale::create([
            'medicine_id'       => $validated['medicine_id'],
            'prescription_id'   => $validated['prescription_id'] ?? null,
            'patient_id'        => $validated['patient_id'] ?? null,
            'quantity_sold'     => $validated['quantity_sold'],
            'total_price'       => $total_price,
            'sale_type'         => $validated['sale_type'],
            'payment_method'    => $validated['payment_method'],
            'payment_reference' => $validated['payment_reference'] ?? null,
            'payment_status'    => $payment_status,
            'billed_to'         => $validated['billed_to'] ?? null,
            'bill_due_date'     => $validated['bill_due_date'] ?? null,
            'user_id'           => Auth::id(),
        ]);

        // Deduct stock
        $inventory->decrement('current_stock', $validated['quantity_sold']);
        Medicine::findOrFail($validated['medicine_id'])->decrement('stock', $validated['quantity_sold']);

        // Mark prescription as dispensed if linked
        if (!empty($validated['prescription_id'])) {
            Prescription::findOrFail($validated['prescription_id'])
                ->update(['status' => 'dispensed']);
        }

        return redirect()->back()
            ->with('message', 'Sale recorded successfully. Total: Ksh ' . number_format($total_price, 2));
    }

    public function view_sales()
    {
        $sales        = Sale::with(['medicine', 'patient', 'prescription', 'dispensedBy'])
            ->latest()->get();
        $totalRevenue = Sale::paid()->sum('total_price');
        $totalBilled  = Sale::billed()->sum('total_price');
        $totalPending = Sale::pending()->sum('total_price');
        $todaySales   = Sale::today()->sum('total_price');

        return view('pharmacist.view_sales', compact(
            'sales',
            'totalRevenue',
            'totalBilled',
            'totalPending',
            'todaySales'
        ));
    }

    public function edit($id)
    {
        $sale          = Sale::findOrFail($id);
        $medicines     = Medicine::where('stock', '>', 0)->get();
        $prescriptions = \App\Models\Prescription::where('status', 'pending')
            ->with(['patient', 'medicine', 'doctor'])
            ->get();

        return view('pharmacist.edit_sales', compact('sale', 'medicines', 'prescriptions'));
    }

    public function update(Request $request, $id)
    {
        $sale = Sale::findOrFail($id);

        $validated = $request->validate([
            'medicine_id'       => 'required|exists:medicines,id',
            'quantity_sold'     => 'required|integer|min:1',
            'sale_type'         => 'required|in:prescription,otc,insurance',
            'payment_method'    => 'required|in:cash,mpesa,insurance,credit',
            'payment_reference' => 'nullable|string|max:100',
            'payment_status'    => 'required|in:paid,pending,billed',
            'prescription_id'   => 'nullable|exists:prescriptions,id',
            'billed_to'         => 'nullable|string|max:255',
            'bill_due_date'     => 'nullable|date',
        ]);

        $inventory = Inventory::where('medicine_id', $validated['medicine_id'])
            ->latest()->firstOrFail();

        // Restore original stock, check new quantity
        $stockAfterRestore = $inventory->current_stock + $sale->quantity_sold;
        if ($stockAfterRestore < $validated['quantity_sold']) {
            return redirect()->back()
                ->with('error', 'Insufficient stock. Available: ' . $stockAfterRestore);
        }

        $total_price = $inventory->price * $validated['quantity_sold'];
        $qtyDiff     = $validated['quantity_sold'] - $sale->quantity_sold;

        $sale->update([
            'medicine_id'       => $validated['medicine_id'],
            'prescription_id'   => $validated['prescription_id'] ?? null,
            'quantity_sold'     => $validated['quantity_sold'],
            'total_price'       => $total_price,
            'sale_type'         => $validated['sale_type'],
            'payment_method'    => $validated['payment_method'],
            'payment_reference' => $validated['payment_reference'] ?? null,
            'payment_status'    => $validated['payment_status'],
            'billed_to'         => $validated['billed_to'] ?? null,
            'bill_due_date'     => $validated['bill_due_date'] ?? null,
        ]);

        // Adjust stock by the difference
        $inventory->increment('current_stock', -$qtyDiff);
        Medicine::findOrFail($validated['medicine_id'])->decrement('stock', $qtyDiff);

        return redirect()->route('pharmacist.sales')
            ->with('message', 'Sale updated. Total: Ksh ' . number_format($total_price, 2));
    }
}
