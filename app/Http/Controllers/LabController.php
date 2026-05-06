<?php

namespace App\Http\Controllers;

use App\Models\LabTest;
use App\Models\LabRequest;
use App\Models\Doctor;
use App\Models\Appointment;
use App\Models\Encounter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LabController extends Controller
{
    // ═══════════════════════════════════════════════════════════════
    // ADMIN — View only, no clinical details
    // ═══════════════════════════════════════════════════════════════
    public function admin_index()
    {
        $labTests     = LabTest::latest()->get();
        $totalTests   = LabRequest::count();
        $pending      = LabRequest::whereIn('status', ['requested', 'in_progress'])->count();
        $completed    = LabRequest::where('status', 'completed')->count();
        return view('admin.lab.index', compact('labTests', 'totalTests', 'pending', 'completed'));
    }
    public function admin_requests()
    {
        $requests = LabRequest::with(['labTest', 'doctor'])
            ->latest()
            ->get();
        return view('admin.lab.requests', compact('requests'));
    }
    // ═══════════════════════════════════════════════════════════════
    // LAB TECHNICIAN — Manages tests, processes queue, uploads results
    // ═══════════════════════════════════════════════════════════════
    public function lab_home()
    {
        $totalTests      = LabRequest::count();
        $pendingRequests = LabRequest::whereIn('status', ['requested', 'in_progress'])->count();
        $completedToday  = LabRequest::where('status', 'completed')
            ->whereDate('completed_at', Carbon::today())
            ->count();
        $totalCompleted  = LabRequest::where('status', 'completed')->count();
        $labTests        = LabTest::latest()->get();
        return view('lab.home', compact(
            'totalTests',
            'pendingRequests',
            'completedToday',
            'totalCompleted',
            'labTests'
        ));
    }
    public function create_test()
    {
        return view('lab.create_test');
    }
    public function store_test(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'code'        => 'required|string|max:50|unique:lab_tests,code',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
        ]);
        LabTest::create([
            'name'        => $request->name,
            'code'        => strtoupper($request->code),
            'description' => $request->description,
            'price'       => $request->price,
            'is_active'   => true,
        ]);
        return redirect()->route('lab.home')
            ->with('message', 'Lab test added successfully.');
    }
    public function delete_test($id)
    {
        LabTest::findOrFail($id)->delete();
        return redirect()->route('lab.home')
            ->with('message', 'Lab test deleted successfully.');
    }
    public function lab_queue()
    {
        $requests = LabRequest::with(['labTest', 'doctor'])
            ->whereIn('status', ['requested', 'in_progress'])
            ->latest()
            ->get();
        return view('lab.queue', compact('requests'));
    }
    public function upload_result($id)
    {
        $labRequest = LabRequest::with(['labTest', 'doctor'])->findOrFail($id);
        return view('lab.upload_result', compact('labRequest'));
    }
    public function store_result(Request $request, $id)
    {
        $request->validate([
            'result_file'  => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'result_notes' => 'nullable|string',
            'status'       => 'required|in:in_progress,completed',
        ]);
        $labRequest  = LabRequest::findOrFail($id);
        $filename    = time() . '_' . preg_replace('/\s+/', '_', $labRequest->patient_name)
            . '.' . $request->file('result_file')->getClientOriginalExtension();
        $destination = public_path('labresults');
        if (!file_exists($destination)) {
            mkdir($destination, 0775, true);
        }
        $request->file('result_file')->move($destination, $filename);
        $labRequest->update([
            'result_file'  => $filename,
            'result_notes' => $request->result_notes,
            'status'       => $request->status,
            'completed_at' => $request->status === 'completed' ? Carbon::now() : null,
        ]);
        return redirect()->route('lab.queue')
            ->with('message', 'Result uploaded successfully.');
    }
    public function lab_completed()
    {
        $requests = LabRequest::with(['labTest', 'doctor'])
            ->where('status', 'completed')
            ->latest()
            ->get();
        return view('lab.completed', compact('requests'));
    }
    // ═══════════════════════════════════════════════════════════════
    // DOCTOR — Requests tests, reviews results, releases to patient
    // ═══════════════════════════════════════════════════════════════
    public function request_form()
    {
        $labTests     = LabTest::where('is_active', true)->get();
        $doctor       = Doctor::where('user_id', Auth::id())->firstOrFail();
        $appointments = Appointment::where('doctor_id', $doctor->id)
            ->whereIn('status', ['confirmed', 'pending'])
            ->latest()
            ->get();
        $encounters   = Encounter::where('doctor_id', $doctor->id)
            ->where('status', 'open')
            ->with('appointment')
            ->latest()
            ->get();
        return view('doctor.lab.request', compact('labTests', 'appointments', 'encounters'));
    }
    public function store_request(Request $request)
    {
        $request->validate([
            'lab_test_id'    => 'required|exists:lab_tests,id',
            'source'         => 'required|in:appointment,encounter',
            'appointment_id' => 'nullable|exists:appointments,id',
            'encounter_id'   => 'nullable|exists:encounters,id',
            'patient_name'   => 'required|string|max:255',
            'patient_email'  => 'nullable|email',
            'patient_phone'  => 'nullable|string|max:20',
            'notes'          => 'nullable|string',
        ]);

        $doctor = Doctor::where('user_id', Auth::id())->firstOrFail();

        // Get user_id from appointment if available
        $appointment = $request->appointment_id
            ? Appointment::find($request->appointment_id)
            : null;

        // FIX: capture the created model so it can be passed to BillingIntegrationService
        $labRequest = LabRequest::create([
            'lab_test_id'    => $request->lab_test_id,
            'doctor_id'      => $doctor->id,
            'user_id'        => $appointment?->user_id ?? null,
            'appointment_id' => $request->source === 'appointment' ? $request->appointment_id : null,
            'encounter_id'   => $request->source === 'encounter'   ? $request->encounter_id   : null,
            'patient_name'   => $request->patient_name,
            'patient_email'  => $request->patient_email,
            'patient_phone'  => $request->patient_phone,
            'notes'          => $request->notes,
            'status'         => 'requested',
            'requested_at'   => Carbon::now(),
        ]);

        app(\App\Services\Billing\BillingIntegrationService::class)->onLabRequestCreated($labRequest);

        return redirect()->route('doctor.lab.requests')
            ->with('message', 'Lab test requested successfully.');
    }
    public function doctor_requests()
    {
        $doctor   = Doctor::where('user_id', Auth::id())->firstOrFail();
        $requests = LabRequest::with(['labTest', 'appointment', 'encounter'])
            ->where('doctor_id', $doctor->id)
            ->latest()
            ->get();
        return view('doctor.lab.requests', compact('requests'));
    }
    public function release_to_patient($id)
    {
        $doctor     = Doctor::where('user_id', Auth::id())->firstOrFail();
        $labRequest = LabRequest::where('id', $id)
            ->where('doctor_id', $doctor->id)
            ->firstOrFail();
        if (!$labRequest->result_file) {
            return redirect()->back()
                ->with('error', 'Cannot release — result not uploaded yet.');
        }
        $labRequest->update([
            'released_to_patient' => true,
            'released_at'         => Carbon::now(),
        ]);
        app(\App\Services\NotificationService::class)->labResultReleased($labRequest);

        return redirect()->route('doctor.lab.requests')
            ->with('message', 'Result released to patient successfully.');
    }
    // ═══════════════════════════════════════════════════════════════
    // SHARED — View result (role-based access)
    // ═══════════════════════════════════════════════════════════════
    public function view_result($id)
    {
        $labRequest = LabRequest::with(['labTest', 'doctor'])->findOrFail($id);
        $user       = Auth::user();
        // Patient — only if released and their own result
        if ($user->role === 'patient') {
            if (!$labRequest->released_to_patient) {
                return redirect()->back()
                    ->with('error', 'Result not available yet. Please consult your doctor.');
            }
            if ($labRequest->user_id !== $user->id) {
                abort(403, 'Unauthorized.');
            }
        }
        // Admin — sees test name, cost, status only (no clinical details)
        $isAdmin = $user->role === 'admin';
        return view('shared.lab_result', compact('labRequest', 'isAdmin'));
    }
    // ═══════════════════════════════════════════════════════════════
    // PATIENT — View their released results only
    // ═══════════════════════════════════════════════════════════════
    public function patient_results()
    {
        $user     = Auth::user();
        $requests = LabRequest::with(['labTest', 'doctor'])
            ->where('released_to_patient', true)
            ->where('user_id', $user->id)
            ->latest()
            ->get();
        return view('patient.lab_results', compact('requests'));
    }
}
