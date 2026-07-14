<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    // Maps each role to its own header/sidebar view pair.
    // Update the right-hand values to match whatever files actually exist
    // for each role — only admin and receptionist are confirmed so far.
    private array $layouts = [
        'admin'                   => ['header' => 'admin.header',        'sidebar' => 'admin.menusidebar'],
        'receptionist'            => ['header' => 'receptionist.header', 'sidebar' => 'receptionist.sidebar'],
        'doctor'                  => ['header' => 'doctor.header',       'sidebar' => 'doctor.sidebar'],
        'patient'                 => ['header' => 'patient.header',      'sidebar' => 'patient.sidebar'],
        'pharmacist'              => ['header' => 'pharmacist.header',   'sidebar' => 'pharmacist.sidebar'],
        'lab_technician'          => ['header' => 'lab.header',          'sidebar' => 'lab.sidebar'],
        'nurse'                   => ['header' => 'nurse.header',        'sidebar' => 'nurse.sidebar'],
        'radiologist'             => ['header' => 'radiology.header',    'sidebar' => 'radiology.sidebar'],
        'physiotherapist'         => ['header' => 'physio.header',       'sidebar' => 'physio.sidebar'],
        'billing_officer'         => ['header' => 'billing.header',      'sidebar' => 'billing.sidebar'],
        'accountant'              => ['header' => 'accountant.header',   'sidebar' => 'accountant.sidebar'],
        'medical_records_officer' => ['header' => 'records.header',      'sidebar' => 'records.sidebar'],
    ];

    public function profile()
    {
        $user   = auth()->user();
        $layout = $this->layouts[$user->role] ?? $this->layouts['admin']; // safe fallback

        return view('profile.index', compact('user') + [
            'header'  => $layout['header'],
            'sidebar' => $layout['sidebar'],
        ]);
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
