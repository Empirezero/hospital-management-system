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
        $patient = Auth::user()->patient;
        $doctors = Doctor::where('status', 'active')->get();
        $appoint = $patient
            ? Appointment::with('doctor')
            ->where('patient_id', $patient->id)
            ->latest('scheduled_at')
            ->get()
            : collect();

        return view('patient.add_appointment', compact('doctors', 'appoint'));
    }

    public function addview()
    {
        $doctors = Doctor::where('status', 'active')->get();
        return view('patient.add_appointment', compact('doctors'));
    }

    public function my_appointment()
    {
        $patient = Auth::user()->patient;
        $appoint = $patient
            ? Appointment::with('doctor')
            ->where('patient_id', $patient->id)
            ->latest('scheduled_at')
            ->get()
            : collect();

        return view('patient.my_appointment', compact('appoint'));
    }

    public function cancel_appoint($id)
    {
        $patient     = Auth::user()->patient;
        $appointment = Appointment::where('id', $id)
            ->where('patient_id', $patient->id)
            ->firstOrFail();

        $appointment->update(['status' => 'cancelled']);

        return redirect()->back()->with('message', 'Appointment cancelled successfully.');
    }
}
