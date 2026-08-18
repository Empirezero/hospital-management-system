<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use App\Models\Doctor;

class HomeController extends Controller
{
    public function redirect()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        return match ($user->role) {
            'admin'                   => redirect()->route('admin.home'),
            'doctor'                  => redirect()->route('doctor.home'),
            'patient'                 => redirect()->route('patient.home'),
            'pharmacist'              => redirect()->route('pharmacist.home'),
            'lab_technician'          => redirect()->route('lab.home'),
            'receptionist'            => redirect()->route('receptionist.home'),
            'nurse'                   => redirect()->route('nurse.home'),
            'radiologist'             => redirect()->route('radiology.home'),
            'physiotherapist'         => redirect()->route('physio.home'),
            'billing_officer'         => redirect()->route('billing.home'),
            'accountant'              => redirect()->route('accountant.home'),
            'medical_records_officer' => redirect()->route('records.home'),
            default                   => redirect()->route('login'),
        };
    }

    public function appointment(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|max:255',
            'date'      => 'required|date|after:today',
            'time_slot' => 'required|date_format:H:i',
            'number'    => 'required|string|max:20',
            'message'   => 'nullable|string|max:1000',
            'doctor'    => 'required|exists:doctors,id',
        ]);

        $scheduledAt = \Carbon\Carbon::parse($validated['date'] . ' ' . $validated['time_slot']);

        $patientId = null;
        if (Auth::check() && Auth::user()->isPatient()) {
            $patientId = Auth::user()->patient?->id;
        }

        $appointment = Appointment::create([
            'name'         => $validated['name'],
            'email'        => $validated['email'],
            'scheduled_at' => $scheduledAt,
            'number'       => $validated['number'],
            'message'      => $validated['message'] ?? null,
            'doctor_id'    => $validated['doctor'],
            'patient_id'   => $patientId,
            'user_id'      => Auth::id(),
            'status'       => 'pending',
        ]);

        app(\App\Services\NotificationService::class)->appointmentBooked($appointment);
        return redirect()->back()->with('message', 'Appointment sent successfully. We will be in touch shortly.');
    }
}



