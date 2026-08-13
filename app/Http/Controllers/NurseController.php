<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Admission;
use App\Models\Ward;
use App\Models\Bed;
use App\Models\Encounter;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class NurseController extends Controller
{
    public function index()
    {
        $stats = [
            'total_admissions'    => Admission::where('status', 'admitted')->count(),
            'admissions_today'    => Admission::whereDate('admitted_at', Carbon::today())->count(),
            'discharges_today'    => Admission::whereDate('discharged_at', Carbon::today())->count(),
            'available_beds'      => Bed::where('status', 'available')->count(),
            'occupied_beds'       => Bed::where('status', 'occupied')->count(),
            'pending_appointments' => Appointment::where('status', 'pending')->count(),
        ];

        $recentAdmissions = Admission::with(['bed', 'ward', 'doctor'])
            ->where('status', 'admitted')
            ->latest()
            ->take(5)
            ->get();

        $wards = Ward::with(['beds'])
            ->where('is_active', true)
            ->get();

        return view('nurse.home', compact('stats', 'recentAdmissions', 'wards'));
    }
    public function admissions()
    {
        $admissions = Admission::with(['bed', 'ward', 'doctor'])
            ->where('status', 'admitted')
            ->latest()
            ->get();

        return view('nurse.admissions', compact('admissions'));
    }

    public function appointments()
    {
        $appointments = Appointment::with('doctor')
            ->whereIn('status', ['pending', 'confirmed'])
            ->latest('scheduled_at')
            ->get();

        return view('nurse.appointments', compact('appointments'));
    }
}
