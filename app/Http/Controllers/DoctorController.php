<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Encounter;
use App\Models\Prescription;
use App\Models\Medicine;

class DoctorController extends Controller
{
    public function index()
    {
        $doctor = Doctor::where('user_id', Auth::id())->firstOrFail();
        $totalAppointments     = Appointment::where('doctor_id', $doctor->id)->count();
        $pendingAppointments   = Appointment::where('doctor_id', $doctor->id)->where('status', 'pending')->count();
        $confirmedAppointments = Appointment::where('doctor_id', $doctor->id)->where('status', 'confirmed')->count();
        $completedAppointments = Appointment::where('doctor_id', $doctor->id)->where('status', 'completed')->count();

        return view('doctor.home', compact('doctor', 'totalAppointments', 'pendingAppointments', 'confirmedAppointments', 'completedAppointments'));
    }

    public function doctor_appointment()
    {
        $doctor  = Doctor::where('user_id', Auth::id())->firstOrFail();
        $appoint = Appointment::with('patient')
            ->where('doctor_id', $doctor->id)
            ->latest('scheduled_at')
            ->get();

        return view('doctor.show_appointment', compact('appoint'));
    }

    public function update($id)
    {
        $appointment = Appointment::with(['patient', 'doctor'])->findOrFail($id);
        return view('doctor.update_appointment', compact('appointment'));
    }

    public function update_appointment(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled,no_show',
            'notes'  => 'nullable|string|max:1000',
        ]);

        $appointment = Appointment::findOrFail($id);
        $appointment->update([
            'status' => $request->status,
            'notes'  => $request->notes,
        ]);

        app(\App\Services\NotificationService::class)->appointmentStatusChanged($appointment);
        return redirect('/doctor_appointment')->with('success', 'Appointment updated successfully.');
    }

    // ─── Encounters ───────────────────────────────────────────────────

    public function encounters()
    {
        $doctor     = Doctor::where('user_id', Auth::id())->firstOrFail();
        $encounters = Encounter::where('doctor_id', $doctor->id)
            ->with(['appointment', 'prescriptions.medicine'])
            ->latest()
            ->get();
        return view('doctor.encounters', compact('encounters'));
    }

    public function create_encounter($appointment_id)
    {
        $appointment = Appointment::findOrFail($appointment_id);

        // Check if encounter already exists for this appointment
        $existing = Encounter::where('appointment_id', $appointment_id)->first();
        if ($existing) {
            return redirect()->route('doctor.prescriptions.create', $existing->id)
                ->with('message', 'Encounter already exists for this appointment.');
        }

        return view('doctor.create_encounter', compact('appointment'));
    }

    public function store_encounter(Request $request)
    {
        $request->validate([
            'appointment_id'    => 'required|exists:appointments,id',
            'chief_complaint'   => 'required|string',
            'examination_notes' => 'nullable|string',
            'treatment_plan'    => 'nullable|string',
            'visit_type'        => 'required|in:outpatient,inpatient,emergency,follow_up',
        ]);

        $doctor      = Doctor::where('user_id', Auth::id())->firstOrFail();
        $appointment = Appointment::findOrFail($request->appointment_id);

        $encounter = Encounter::create([
            'patient_id'        => $appointment->patient_id ?? null,
            'doctor_id'         => $doctor->id,
            'appointment_id'    => $appointment->id,
            'chief_complaint'   => $request->chief_complaint,
            'examination_notes' => $request->examination_notes,
            'treatment_plan'    => $request->treatment_plan,
            'visit_type'        => $request->visit_type,
            'status'            => 'open',
        ]);
        app(\App\Services\Billing\BillingIntegrationService::class)->onEncounterCreated($encounter);

        return redirect()->route('doctor.prescriptions.create', $encounter->id)
            ->with('message', 'Encounter created. Now add prescriptions.');
    }

    public function close_encounter($encounter_id)
    {
        $doctor    = Doctor::where('user_id', Auth::id())->firstOrFail();
        $encounter = Encounter::where('id', $encounter_id)
            ->where('doctor_id', $doctor->id)
            ->firstOrFail();
        $encounter->update(['status' => 'closed']);

      // Mark appointment as completed
  if ($encounter->appointment_id) {
    $appointment = Appointment::find($encounter->appointment_id);
    if ($appointment) {
        $appointment->update(['status' => 'completed']);
        app(\App\Services\NotificationService::class)->appointmentStatusChanged($appointment);
    }
 }

        
        return redirect()->route('doctor.encounters')
            ->with('message', 'Encounter closed and appointment marked as completed.');
    }

    // ─── Prescriptions ────────────────────────────────────────────────

    public function create_prescription($encounter_id)
    {
        $doctor    = Doctor::where('user_id', Auth::id())->firstOrFail();
        $encounter = Encounter::where('id', $encounter_id)
            ->where('doctor_id', $doctor->id)
            ->with(['appointment', 'prescriptions.medicine'])
            ->firstOrFail();
        $medicines = Medicine::all();
        return view('doctor.create_prescription', compact('encounter', 'medicines'));
    }

    public function store_prescription(Request $request, $encounter_id)
    {
        $request->validate([
            'medicine_id'   => 'required|exists:medicines,id',
            'dosage'        => 'required|string|max:255',
            'frequency'     => 'required|string|max:255',
            'duration_days' => 'required|integer|min:1',
            'instructions'  => 'nullable|string',
        ]);

        $doctor    = Doctor::where('user_id', Auth::id())->firstOrFail();
        $encounter = Encounter::where('id', $encounter_id)
            ->where('doctor_id', $doctor->id)
            ->firstOrFail();

        // Prevent adding to closed encounter
        if ($encounter->status === 'closed') {
            return redirect()->back()->with('error', 'Cannot add prescriptions to a closed encounter.');
        }

        $prescription = Prescription::create([
            'encounter_id'  => $encounter->id,
            'medicine_id'   => $request->medicine_id,
            'patient_id'    => $encounter->patient_id ?? null,
            'doctor_id'     => $doctor->id,
            'dosage'        => $request->dosage,
            'frequency'     => $request->frequency,
            'duration_days' => $request->duration_days,
            'instructions'  => $request->instructions,
            'status'        => 'pending',
        ]);
        app(\App\Services\Billing\BillingIntegrationService::class)->onPrescriptionCreated($prescription);
        app(\App\Services\NotificationService::class)->prescriptionReady($prescription);
        return redirect()->route('doctor.prescriptions.create', $encounter_id)
            ->with('message', 'Medicine added to prescription.');
    }
}
