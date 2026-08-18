<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use App\Models\Doctor;

class PatientController extends Controller
{
    public function index()
    {
        $doctors = Doctor::where('status', 'active')->get();
        $appoint = Appointment::with('doctor')
            ->where('user_id', Auth::id())
            ->latest('scheduled_at')
            ->get();

        $patient = \App\Models\Patient::where('user_id', Auth::id())->first();

        $upcomingAppointment = Appointment::with('doctor')
            ->where('user_id', Auth::id())
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at')
            ->first();

        $upcomingCount = Appointment::where('user_id', Auth::id())
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('scheduled_at', '>=', now())
            ->count();

        $totalVisits = $patient
            ? \App\Models\Encounter::where('patient_id', $patient->id)->count()
            : 0;

        $activePrescriptions = $patient
            ? \App\Models\Prescription::where('patient_id', $patient->id)
            ->where('status', 'pending')
            ->count()
            : 0;

        $pendingClaims = $patient
            ? \App\Models\InsuranceClaim::where('patient_id', $patient->id)
            ->whereIn('status', ['pending', 'submitted'])
            ->count()
            : 0;

        return view('patient.home', compact(
            'doctors',
            'appoint',
            'upcomingAppointment',
            'upcomingCount',
            'totalVisits',
            'activePrescriptions',
            'pendingClaims'
        ));
    }

    public function addview()
    {
        $doctors = Doctor::where('status', 'active')->get();
        return view('patient.add_appointment', compact('doctors'));
    }

    public function my_appointment()
    {
        $appoint = Appointment::with('doctor')
            ->where('user_id', Auth::id())
            ->latest('scheduled_at')
            ->get();

        return view('patient.my_appointment', compact('appoint'));
    }

    public function cancel_appoint($id)
    {
        $appointment = Appointment::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $appointment->update(['status' => 'cancelled']);
app(\App\Services\NotificationService::class)->appointmentStatusChanged($appointment);
        return redirect()->back()->with('message', 'Appointment cancelled successfully.');
    }

    public function my_claims(){
        $patient =\App\Models\Patient:: where('user_id', Auth::id())->first();
        $claims = $patient
          ?\App\Models\InsuranceClaim:: with(['sale.medicine'])
            ->where('patient_id', $patient->id)
            ->latest()
            ->get()
            : collect();
        return view('patient.claims', compact('claims'));
    }
    
    public function my_prescriptions()
    {
        $patient = \App\Models\Patient::where('user_id', Auth::id())->first();

        $prescriptions = $patient
            ? \App\Models\Prescription::with(['medicine', 'doctor', 'encounter'])
            ->where('patient_id', $patient->id)
            ->latest()
            ->get()
            : collect();

        return view('patient.prescriptions', compact('prescriptions'));
    }
}
