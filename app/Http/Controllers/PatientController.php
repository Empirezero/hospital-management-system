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

        return view('patient.add_appointment', compact('doctors', 'appoint'));
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

        return redirect()->back()->with('message', 'Appointment cancelled successfully.');
    }
}
