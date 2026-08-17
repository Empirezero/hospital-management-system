<?php

namespace App\Http\Controllers;

use App\Models\Ward;
use App\Models\Bed;
use App\Models\Admission;
use App\Models\Doctor;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BedController extends Controller
{
    // ═══════════════════════════════════════════════════════════════
    // WARD MANAGEMENT — Admin only
    // ═══════════════════════════════════════════════════════════════

    public function wards()
    {
        $wards = Ward::withCount([
            'beds',
            'beds as available_beds_count' => fn($q) => $q->where('status', 'available'),
            'beds as occupied_beds_count'  => fn($q) => $q->where('status', 'occupied'),
        ])->get();

        return view('admin.beds.wards', compact('wards'));
    }

    public function create_ward()
    {
        return view('admin.beds.create_ward');
    }

    public function store_ward(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'type'        => 'required|in:general,icu,emergency,private',
            'total_beds'  => 'required|integer|min:1',
            'description' => 'nullable|string',
        ]);

        $ward = Ward::create([
            'name'        => $request->name,
            'type'        => $request->type,
            'total_beds'  => $request->total_beds,
            'description' => $request->description,
            'is_active'   => true,
        ]);

        // Auto-create beds
        for ($i = 1; $i <= $request->total_beds; $i++) {
            Bed::create([
                'ward_id'    => $ward->id,
                'bed_number' => strtoupper(substr($ward->type, 0, 1)) . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'status'     => 'available',
            ]);
        }

        return redirect()->route('admin.beds.wards')
            ->with('message', 'Ward created with ' . $request->total_beds . ' beds.');
    }

    public function delete_ward($id)
    {
        Ward::findOrFail($id)->delete();
        return redirect()->route('admin.beds.wards')
            ->with('message', 'Ward deleted.');
    }

    // ═══════════════════════════════════════════════════════════════
    // HELPER — Get role-based redirect route
    // ═══════════════════════════════════════════════════════════════

    private function redirectAfterAction()
    {
        return redirect()->route('beds.admissions')
            ->with('message', 'Action completed successfully.');
    }

    // ═══════════════════════════════════════════════════════════════
    // BED MANAGEMENT — Admin, Doctor, Nurse
    // ═══════════════════════════════════════════════════════════════

    public function beds($ward_id = null)
    {
        $wards = Ward::where('is_active', true)->get();
        $beds  = Bed::with(['ward', 'currentAdmission'])
            ->when($ward_id, fn($q) => $q->where('ward_id', $ward_id))
            ->orderBy('ward_id')
            ->orderBy('bed_number')
            ->get();

        $selectedWard = $ward_id ? Ward::find($ward_id) : null;

        return view('shared.beds.beds', compact('beds', 'wards', 'selectedWard'));
    }

    public function update_bed_status(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:available,occupied,maintenance',
        ]);

        Bed::findOrFail($id)->update(['status' => $request->status]);

        return redirect()->back()->with('message', 'Bed status updated.');
    }

    // ═══════════════════════════════════════════════════════════════
    // ADMISSIONS — Admin, Doctor, Nurse
    // ═══════════════════════════════════════════════════════════════

    public function admissions()
    {
        $admissions = Admission::with(['bed', 'ward', 'doctor'])
            ->latest()
            ->get();
        return view('shared.beds.admissions', compact('admissions'));
    }

    public function admit_form($bed_id = null)
    {
        $wards        = Ward::where('is_active', true)->with('beds')->get();
        $doctors      = Doctor::where('status', 'active')->get();
        $appointments = Appointment::whereIn('status', ['confirmed', 'pending'])->latest()->get();
        $bedId        = old('bed_id', $bed_id);
        $selectedBed  = $bedId ? Bed::with('ward')->find($bedId) : null;

        return view('shared.beds.admit', compact('wards', 'doctors', 'appointments', 'selectedBed'));
    }

    public function store_admission(Request $request)
    {
        $request->validate([
            'bed_id'         => 'required|exists:beds,id',
            'ward_id'        => 'required|exists:wards,id',
            'doctor_id'      => 'required|exists:doctors,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'patient_name'   => 'required|string|max:255',
            'patient_email'  => 'nullable|email',
            'patient_phone'  => 'nullable|string|max:20',
            'reason'         => 'nullable|string',
            'notes'          => 'nullable|string',
        ]);

        $bed = Bed::findOrFail($request->bed_id);

        if ($bed->status !== 'available') {
            return redirect()->back()
                ->withInput()
                ->with('error', 'This bed is not available.');
        }

        Admission::create([
            'bed_id'         => $request->bed_id,
            'ward_id'        => $request->ward_id,
            'doctor_id'      => $request->doctor_id,
            'appointment_id' => $request->appointment_id,
            'patient_name'   => $request->patient_name,
            'patient_email'  => $request->patient_email,
            'patient_phone'  => $request->patient_phone,
            'reason'         => $request->reason,
            'notes'          => $request->notes,
            'status'         => 'admitted',
            'admitted_at'    => Carbon::now(),
        ]);

        $bed->update(['status' => 'occupied']);

        return redirect()->route('beds.admissions')
            ->with('message', 'Patient admitted successfully.');
    }

    public function discharge($id)
    {
        $admission = Admission::with('bed')->findOrFail($id);

        if ($admission->status !== 'admitted') {
            return redirect()->back()
                ->with('error', 'Patient is not currently admitted.');
        }

        $admission->update([
            'status'        => 'discharged',
            'discharged_at' => Carbon::now(),
        ]);

        $admission->bed->update(['status' => 'available']);

        return redirect()->route('beds.admissions')
            ->with('message', 'Patient discharged successfully.');
    }

    public function admission_detail($id)
    {
        $admission = Admission::with(['bed', 'ward', 'doctor', 'appointment'])
            ->findOrFail($id);
        return view('shared.beds.admission_detail', compact('admission'));
    }

    // ═══════════════════════════════════════════════════════════════
    // BED OVERVIEW — Quick visual summary
    // ═══════════════════════════════════════════════════════════════

    public function overview()
    {
        $wards = Ward::with(['beds'])->where('is_active', true)->get();

        $stats = [
            'total_beds'       => Bed::count(),
            'available'        => Bed::where('status', 'available')->count(),
            'occupied'         => Bed::where('status', 'occupied')->count(),
            'maintenance'      => Bed::where('status', 'maintenance')->count(),
            'admitted_today'   => Admission::whereDate('admitted_at', Carbon::today())->count(),
            'discharged_today' => Admission::whereDate('discharged_at', Carbon::today())->count(),
        ];

        return view('shared.beds.overview', compact('wards', 'stats'));
    }
}
