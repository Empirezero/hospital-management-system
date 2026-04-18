<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\User;
use App\Models\Medicine;
use App\Models\Prescription;
use App\Models\LabRequest;
use App\Models\Encounter;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReportController extends Controller
{
    // ═══════════════════════════════════════════════════════════════
    // ADMIN DASHBOARD
    // ═══════════════════════════════════════════════════════════════

    public function admin_dashboard()
    {
        // ── Key Stats ──────────────────────────────────────────────
        $stats = [
            'total_doctors'       => Doctor::count(),
            'total_patients'      => User::where('role', 'patient')->count(),
            'total_appointments'  => Appointment::count(),
            'total_lab_requests'  => LabRequest::count(),
            'total_prescriptions' => Prescription::count(),
            'pending_appointments' => Appointment::where('status', 'pending')->count(),
            'pending_lab'         => LabRequest::whereIn('status', ['requested', 'in_progress'])->count(),
            'low_stock_medicines' => Medicine::where('stock', '<', 10)->count(),
        ];

        // ── Appointments by Status (Pie Chart) ─────────────────────
        $appointmentStatuses = Appointment::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // ── Appointments per Month (Line Chart) ────────────────────
        $appointmentsPerMonth = Appointment::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        $monthlyAppointments = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyAppointments[] = $appointmentsPerMonth[$i] ?? 0;
        }

        // ── Lab Requests by Status (Doughnut Chart) ────────────────
        $labStatuses = LabRequest::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // ── Prescriptions per Month (Bar Chart) ────────────────────
        $prescriptionsPerMonth = Prescription::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        $monthlyPrescriptions = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyPrescriptions[] = $prescriptionsPerMonth[$i] ?? 0;
        }

        // ── Top 5 Doctors by Appointments (Horizontal Bar) ─────────
        $topDoctors = Doctor::withCount('appointments')
            ->orderBy('appointments_count', 'desc')
            ->take(5)
            ->get();

        // ── Users by Role (Pie Chart) ──────────────────────────────
        $usersByRole = User::selectRaw('role, COUNT(*) as count')
            ->groupBy('role')
            ->pluck('count', 'role')
            ->toArray();

        // ── Recent Activity ────────────────────────────────────────
        $recentAppointments = Appointment::with('doctor')
            ->latest()
            ->take(5)
            ->get();

        $recentLabRequests = LabRequest::with(['labTest', 'doctor'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.reports.dashboard', compact(
            'stats',
            'appointmentStatuses',
            'monthlyAppointments',
            'labStatuses',
            'monthlyPrescriptions',
            'topDoctors',
            'usersByRole',
            'recentAppointments',
            'recentLabRequests'
        ));
    }

    // ═══════════════════════════════════════════════════════════════
    // DOCTOR DASHBOARD
    // ═══════════════════════════════════════════════════════════════

    public function doctor_dashboard()
    {
        $doctor = \App\Models\Doctor::where('user_id', Auth::id())->firstOrFail();

        // ── Key Stats ──────────────────────────────────────────────
        $stats = [
            'total_appointments'  => Appointment::where('doctor_id', $doctor->id)->count(),
            'pending'             => Appointment::where('doctor_id', $doctor->id)->where('status', 'pending')->count(),
            'confirmed'           => Appointment::where('doctor_id', $doctor->id)->where('status', 'confirmed')->count(),
            'completed'           => Appointment::where('doctor_id', $doctor->id)->where('status', 'completed')->count(),
            'total_encounters'    => Encounter::where('doctor_id', $doctor->id)->count(),
            'open_encounters'     => Encounter::where('doctor_id', $doctor->id)->where('status', 'open')->count(),
            'total_prescriptions' => Prescription::where('doctor_id', $doctor->id)->count(),
            'total_lab_requests'  => LabRequest::where('doctor_id', $doctor->id)->count(),
        ];

        // ── Appointments by Status (Doughnut Chart) ────────────────
        $appointmentStatuses = Appointment::where('doctor_id', $doctor->id)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // ── Appointments per Month (Line Chart) ────────────────────
        $appointmentsPerMonth = Appointment::where('doctor_id', $doctor->id)
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        $monthlyAppointments = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyAppointments[] = $appointmentsPerMonth[$i] ?? 0;
        }

        // ── Lab Requests by Status (Bar Chart) ────────────────────
        $labStatuses = LabRequest::where('doctor_id', $doctor->id)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // ── Prescriptions per Month (Bar Chart) ────────────────────
        $prescriptionsPerMonth = Prescription::where('doctor_id', $doctor->id)
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        $monthlyPrescriptions = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyPrescriptions[] = $prescriptionsPerMonth[$i] ?? 0;
        }

        // ── Recent Appointments ────────────────────────────────────
        $recentAppointments = Appointment::where('doctor_id', $doctor->id)
            ->latest()
            ->take(5)
            ->get();

        return view('doctor.reports.dashboard', compact(
            'stats',
            'appointmentStatuses',
            'monthlyAppointments',
            'labStatuses',
            'monthlyPrescriptions',
            'recentAppointments'
        ));
    }

    // ═══════════════════════════════════════════════════════════════
    // PHARMACIST DASHBOARD
    // ═══════════════════════════════════════════════════════════════

    public function pharmacist_dashboard()
    {
        // ── Key Stats ──────────────────────────────────────────────
        $stats = [
            'total_medicines'      => Medicine::count(),
            'low_stock'            => Medicine::where('stock', '<', 10)->count(),
            'out_of_stock'         => Medicine::where('stock', 0)->count(),
            'expiring_soon'        => Medicine::where('expiry_date', '<=', Carbon::now()->addDays(30))
                ->where('expiry_date', '>=', Carbon::now())
                ->count(),
            'total_prescriptions'  => Prescription::count(),
            'pending_prescriptions' => Prescription::where('status', 'pending')->count(),
            'dispensed_today'      => Prescription::where('status', 'dispensed')
                ->whereDate('updated_at', Carbon::today())
                ->count(),
        ];

        // ── Prescriptions by Status (Doughnut Chart) ───────────────
        $prescriptionStatuses = Prescription::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // ── Prescriptions per Month (Line Chart) ───────────────────
        $prescriptionsPerMonth = Prescription::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        $monthlyPrescriptions = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyPrescriptions[] = $prescriptionsPerMonth[$i] ?? 0;
        }

        // ── Top 5 Most Prescribed Medicines (Bar Chart) ────────────
        $topMedicines = Medicine::withCount('prescriptions')
            ->orderBy('prescriptions_count', 'desc')
            ->take(5)
            ->get();

        // ── Stock Levels (Horizontal Bar Chart) ────────────────────
        $stockLevels = Medicine::orderBy('stock', 'asc')
            ->take(10)
            ->get();

        // ── Expiring Medicines ─────────────────────────────────────
        $expiringMedicines = Medicine::where('expiry_date', '<=', Carbon::now()->addDays(60))
            ->where('expiry_date', '>=', Carbon::now())
            ->orderBy('expiry_date')
            ->take(5)
            ->get();

        return view('pharmacist.reports.dashboard', compact(
            'stats',
            'prescriptionStatuses',
            'monthlyPrescriptions',
            'topMedicines',
            'stockLevels',
            'expiringMedicines'
        ));
    }

    // ═══════════════════════════════════════════════════════════════
    // LAB TECHNICIAN DASHBOARD
    // ═══════════════════════════════════════════════════════════════

    public function lab_dashboard()
    {
        // ── Key Stats ──────────────────────────────────────────────
        $stats = [
            'total_requests'   => LabRequest::count(),
            'pending'          => LabRequest::whereIn('status', ['requested', 'in_progress'])->count(),
            'completed'        => LabRequest::where('status', 'completed')->count(),
            'completed_today'  => LabRequest::where('status', 'completed')
                ->whereDate('completed_at', Carbon::today())
                ->count(),
            'released'         => LabRequest::where('released_to_patient', true)->count(),
            'not_released'     => LabRequest::where('status', 'completed')
                ->where('released_to_patient', false)
                ->count(),
        ];

        // ── Requests by Status (Doughnut Chart) ───────────────────
        $requestStatuses = LabRequest::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // ── Requests per Month (Line Chart) ───────────────────────
        $requestsPerMonth = LabRequest::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        $monthlyRequests = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyRequests[] = $requestsPerMonth[$i] ?? 0;
        }

        // ── Top 5 Most Requested Tests (Bar Chart) ────────────────
        $topTests = \App\Models\LabTest::withCount('requests')
            ->orderBy('requests_count', 'desc')
            ->take(5)
            ->get();

        // ── Recent Requests ────────────────────────────────────────
        $recentRequests = LabRequest::with(['labTest', 'doctor'])
            ->latest()
            ->take(5)
            ->get();

        return view('lab.reports.dashboard', compact(
            'stats',
            'requestStatuses',
            'monthlyRequests',
            'topTests',
            'recentRequests'
        ));
    }

    // ═══════════════════════════════════════════════════════════════
    // PATIENT DASHBOARD
    // ═══════════════════════════════════════════════════════════════

    public function patient_dashboard()
    {
        $user = Auth::user();

        // ── Key Stats ──────────────────────────────────────────────
        $stats = [
            'total_appointments'  => Appointment::where('user_id', $user->id)->count(),
            'pending'             => Appointment::where('user_id', $user->id)->where('status', 'pending')->count(),
            'completed'           => Appointment::where('user_id', $user->id)->where('status', 'completed')->count(),
            'cancelled'           => Appointment::where('user_id', $user->id)->where('status', 'cancelled')->count(),
            'lab_results'         => LabRequest::where('user_id', $user->id)->where('released_to_patient', true)->count(),
            'prescriptions'       => Prescription::where('patient_id', $user->patient?->id)->count(),
        ];

        // ── Appointments by Status (Doughnut Chart) ────────────────
        $appointmentStatuses = Appointment::where('user_id', $user->id)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // ── Appointments per Month (Line Chart) ────────────────────
        $appointmentsPerMonth = Appointment::where('user_id', $user->id)
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        $monthlyAppointments = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyAppointments[] = $appointmentsPerMonth[$i] ?? 0;
        }

        // ── Recent Appointments ────────────────────────────────────
        $recentAppointments = Appointment::where('user_id', $user->id)
            ->with('doctor')
            ->latest()
            ->take(5)
            ->get();

        // ── Recent Lab Results ─────────────────────────────────────
        $recentLabResults = LabRequest::where('user_id', $user->id)
            ->where('released_to_patient', true)
            ->with(['labTest', 'doctor'])
            ->latest()
            ->take(5)
            ->get();

        return view('patient.reports.dashboard', compact(
            'stats',
            'appointmentStatuses',
            'monthlyAppointments',
            'recentAppointments',
            'recentLabResults'
        ));
    }
}
