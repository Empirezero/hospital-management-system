<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Patient;

class ReceptionistController extends Controller
{
    public function index()
    {
        $totalPatients    = Patient::count();
        $registeredToday  = Patient::whereDate('created_at', today())->count();

        return view('receptionist.home', compact('totalPatients', 'registeredToday'));
    }

    // ─── Patients ─────────────────────────────────────────────────────

    public function add_patient_view()
    {
        return view('receptionist.add_patient');
    }

    public function store_patient(Request $request)
    {
        $request->validate([
            'name'                    => 'required|string|max:255',
            'email'                   => 'required|email|unique:users,email',
            'password'                => 'required|string|min:8|confirmed',
            'phone'                   => 'required|string|max:20',
            'date_of_birth'           => 'required|date|before:today',
            'gender'                  => 'required|in:male,female,other',
            'blood_group'             => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'address'                 => 'nullable|string',
            'emergency_contact_name'  => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'allergies'               => 'nullable|string',
            'chronic_conditions'      => 'nullable|string',
            'image'
        ]);

        $patient = DB::transaction(function () use ($request) {
            $imagename = null;
            if ($request->hasFile('image')) {
                $img       = $request->file('image');
                $imagename = time() . '.' . $img->getClientOriginalExtension();
                $img->move(public_path('userimage'), $imagename);
            }

            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => 'patient',
                'image'    => $imagename,
            ]);
            // User::booted() already created a bare Patient row — refresh to pick it up
            $user->refresh();

            $user->patient->update([
                'date_of_birth'           => $request->date_of_birth,
                'gender'                  => $request->gender,
                'blood_group'             => $request->blood_group,
                'phone'                   => $request->phone,
                'address'                 => $request->address,
                'emergency_contact_name'  => $request->emergency_contact_name,
                'emergency_contact_phone' => $request->emergency_contact_phone,
                'allergies'               => $request->allergies,
                'chronic_conditions'      => $request->chronic_conditions,
            ]);

            return $user->patient;
        });

        return redirect()->route('receptionist.show_patient', $patient->id)
            ->with('message', "Patient registered — number {$patient->patient_number}");
    }

    public function view_patients(Request $request)
    {
        $search = $request->query('search');

        $patients = Patient::with('user')
            ->when($search, function ($query, $search) {
                $query->where('patient_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhere('phone', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('receptionist.view_patients', compact('patients', 'search'));
    }

    public function show_patient($id)
    {
        $patient = Patient::with('user')->findOrFail($id);
        return view('receptionist.show_patient', compact('patient'));
    }
    // ─── Edit ─────────────────────────────────────────────────────────

    public function edit_patient_view($id)
    {
        $patient = Patient::with('user')->findOrFail($id);
        return view('receptionist.edit_patient', compact('patient'));
    }

    public function update_patient(Request $request, $id)
    {
        $patient = Patient::with('user')->findOrFail($id);

        $request->validate([
            'name'                    => 'required|string|max:255',
            'email'                   => 'required|email|unique:users,email,' . $patient->user->id,
            'password'                => 'nullable|string|min:8|confirmed',
            'phone'                   => 'required|string|max:20',
            'date_of_birth'           => 'required|date|before:today',
            'gender'                  => 'required|in:male,female,other',
            'blood_group'             => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'address'                 => 'nullable|string',
            'emergency_contact_name'  => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'allergies'               => 'nullable|string',
            'chronic_conditions'      => 'nullable|string',
            'image'                   => 'nullable|image|max:2048',
        ]);

        DB::transaction(
            function () use ($request, $patient) {
                $userData = [
                    'name'  => $request->name,
                    'email' => $request->email,
                ];

                if ($request->filled('password')) {
                    $userData['password'] = Hash::make($request->password);
                }

                if ($request->hasFile('image')) {
                    if ($patient->user->image && file_exists(public_path('userimage/' . $patient->user->image))) {
                        unlink(public_path('userimage/' . $patient->user->image));
                    }
                    $img       = $request->file('image');
                    $imagename = time() . '.' . $img->getClientOriginalExtension();
                    $img->move(public_path('userimage'), $imagename);
                    $userData['image'] = $imagename;
                }

                $patient->user->update($userData);

                $patient->update([
                    'date_of_birth'           => $request->date_of_birth,
                    'gender'                  => $request->gender,
                    'blood_group'             => $request->blood_group,
                    'phone'                   => $request->phone,
                    'address'                 => $request->address,
                    'emergency_contact_name'  => $request->emergency_contact_name,
                    'emergency_contact_phone' => $request->emergency_contact_phone,
                    'allergies'               => $request->allergies,
                    'chronic_conditions'      => $request->chronic_conditions,
                ]);
            }
        );

        return redirect()->route('receptionist.show_patient', $patient->id)
            ->with('message', 'Patient details updated successfully.');
    }
   
}
