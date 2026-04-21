<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function index()
    {
        $totalDoctors        = Doctor::count();
        $totalAppointments   = Appointment::count();
        $pendingAppointments = Appointment::where('status', 'pending')->count();

        return view('admin.home', compact('totalDoctors', 'totalAppointments', 'pendingAppointments'));
    }

    // ─── Doctors ─────────────────────────────────────────────────────

    public function addview()
    {
        return view('admin.add_doctor');
    }

    public function upload_doctor(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'number'     => 'required|string|max:20',
            'speciality' => 'required|string|max:255',
            'room'       => 'nullable|string|max:100',
            'location'   => 'nullable|string|max:255',
            'bio'        => 'nullable|string',
            'file'       => 'nullable|image|max:2048',
        ]);

        $imagename = null;
        if ($request->hasFile('file')) {
            $image     = $request->file('file');
            $imagename = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('doctorimage'), $imagename);
        }

        Doctor::create([
            'name'       => $request->name,
            'number'     => $request->number,
            'speciality' => $request->speciality,
            'room'       => $request->room,
            'location'   => $request->location,
            'bio'        => $request->bio,
            'image'      => $imagename,
            'status'     => 'active',
        ]);

        return redirect()->route('admin.view_doctor')->with('message', 'Doctor added successfully.');
    }

    public function view_doctor()
    {
        $data = Doctor::all();
        return view('admin.view_doctor', compact('data'));
    }

    public function show_doctor($id)
    {
        $data = Doctor::findOrFail($id);
        return view('admin.update_doctor', compact('data'));
    }

    public function edit_doctor(Request $request, $id)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'number'     => 'required|string|max:20',
            'speciality' => 'required|string|max:255',
            'room'       => 'nullable|string|max:100',
            'location'   => 'nullable|string|max:255',
            'file'       => 'nullable|image|max:2048',
        ]);

        $doctor = Doctor::findOrFail($id);

        if ($request->hasFile('file')) {
            $image     = $request->file('file');
            $imagename = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('doctorimage'), $imagename);
            $doctor->image = $imagename;
        }

        $doctor->update([
            'name'       => $request->name,
            'number'     => $request->number,
            'speciality' => $request->speciality,
            'room'       => $request->room,
            'location'   => $request->location,
        ]);

        return redirect()->route('admin.view_doctor')->with('message', 'Doctor updated successfully.');
    }

    public function delete_doctor($id)
    {
        Doctor::findOrFail($id)->delete();
        return redirect()->route('admin.view_doctor')->with('message', 'Doctor deleted successfully.');
    }

    // ─── Appointments ─────────────────────────────────────────────────

    public function add_appointment()
    {
        $doctors = Doctor::where('status', 'active')->get();
        return view('admin.add_appointment', compact('doctors'));
    }

    public function show_appointment()
    {
        $data = Appointment::with(['doctor', 'patient'])->latest()->get();
        return view('admin.show_appointment', compact('data'));
    }

    public function update_appoint($id)
    {
        $appointment = Appointment::with(['doctor', 'patient'])->findOrFail($id);
        $doctors     = Doctor::where('status', 'active')->get();
        return view('admin.update_appoint', compact('appointment', 'doctors'));
    }

    public function appointment_update(Request $request, $id)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'number'       => 'required|string|max:15',
            'email'        => 'required|email',
            'doctor_id'    => 'required|exists:doctors,id',
            'scheduled_at' => 'required|date',
            'message'      => 'nullable|string',
            'status'       => 'required|in:pending,confirmed,completed,cancelled,no_show',
        ]);

        $appointment = Appointment::findOrFail($id);
        $appointment->update([
            'name'         => $request->name,
            'number'       => $request->number,
            'email'        => $request->email,
            'doctor_id'    => $request->doctor_id,
            'scheduled_at' => $request->scheduled_at,
            'message'      => $request->message,
            'status'       => $request->status,
        ]);
        app(\App\Services\NotificationService::class)->appointmentStatusChanged($appointment);
        return redirect()->route('admin.appointments')->with('success', 'Appointment updated successfully.');
    }

    public function delete_appoint($id)
    {
        Appointment::findOrFail($id)->delete();
        return redirect()->route('admin.appointments')->with('message', 'Appointment deleted successfully.');
    }

    // ─── Users ────────────────────────────────────────────────────────

    public function view_users()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        return view('admin.view_users', compact('users'));
    }

    public function add_user_view()
    {
        return view('admin.add_user');
    }

    public function store_user(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'role'       => 'required|in:admin,doctor,patient,pharmacist,lab_technician,receptionist,nurse,radiologist,physiotherapist,billing_officer,medical_records_officer',
            'password'   => 'required|string|min:8|confirmed',
            'image'      => 'nullable|image|max:2048',
            // doctor-specific
            'number'     => 'required_if:role,doctor|nullable|string|max:20',
            'speciality' => 'required_if:role,doctor|nullable|string|max:255',
            'room'       => 'nullable|string|max:100',
            'location'   => 'nullable|string|max:255',
            'bio'        => 'nullable|string',
            'file'       => 'nullable|image|max:2048',
        ]);

        // Handle user profile image
        $userImageName = null;
        if ($request->hasFile('image')) {
            $img           = $request->file('image');
            $userImageName = time() . '.' . $img->getClientOriginalExtension();
            $img->move(public_path('userimage'), $userImageName);
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'role'     => $request->role,
            'password' => Hash::make($request->password),
            'image'    => $userImageName,
        ]);

        // Auto-create doctor profile when role is doctor
        if ($request->role === 'doctor') {
            $doctorImageName = null;
            if ($request->hasFile('file')) {
                $image           = $request->file('file');
                $doctorImageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('doctorimage'), $doctorImageName);
            }

            Doctor::create([
                'user_id'    => $user->id,
                'name'       => $request->name,
                'number'     => $request->number,
                'speciality' => $request->speciality,
                'room'       => $request->room,
                'location'   => $request->location,
                'bio'        => $request->bio,
                'image'      => $doctorImageName,
                'status'     => 'active',
            ]);
        }

        return redirect()->route('admin.view_users')->with('message', 'User created successfully.');
    }

    public function edit_user_view($id)
    {
        $user = User::findOrFail($id);
        return view('admin.edit_user', compact('user'));
    }

    public function update_user(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $id,
            'role'     => 'required|in:admin,doctor,patient,pharmacist,lab_technician,receptionist,nurse,radiologist,physiotherapist,billing_officer,medical_records_officer',
            'password' => 'nullable|string|min:8|confirmed',
            'image'    => 'nullable|image|max:2048',
        ]);

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
            'role'  => $request->role,
        ];

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($user->image && file_exists(public_path('userimage/' . $user->image))) {
                unlink(public_path('userimage/' . $user->image));
            }
            $img       = $request->file('image');
            $imagename = time() . '.' . $img->getClientOriginalExtension();
            $img->move(public_path('userimage'), $imagename);
            $data['image'] = $imagename;
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.view_users')->with('message', 'User updated successfully.');
    }

    public function delete_user($id)
    {
        User::findOrFail($id)->delete();
        return redirect()->route('admin.view_users')->with('message', 'User deleted successfully.');
    }

    public function profile()
    {
        $user = auth()->user();
        return view('admin.profile', compact('user'));
    }

    public function update_profile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'image'    => 'nullable|image|max:2048',
        ]);

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
        ];

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($user->image && file_exists(public_path('userimage/' . $user->image))) {
                unlink(public_path('userimage/' . $user->image));
            }
            $img       = $request->file('image');
            $imagename = time() . '.' . $img->getClientOriginalExtension();
            $img->move(public_path('userimage'), $imagename);
            $data['image'] = $imagename;
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('profile')->with('message', 'Profile updated successfully.');
    }
}
