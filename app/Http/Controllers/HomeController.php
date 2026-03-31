<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Corrected to use Auth facade
use App\Models\User;
use App\Models\Doctor;
use App\Models\Appointment;

class HomeController extends Controller
{
    public function redirect()
    {
        if (Auth::check()) { // Use Auth::check() to verify if the user is authenticated
            switch (Auth::user()->role) {
                case 'patient':
                    return view('patient.home'); 
                case 'admin':
                    return view('admin.home'); 
                case 'doctor':
                    return view('doctor.home');
                case 'pharmacist':
                    return view('pharmacist.home');
                case 'receptionist':
                    return view('receptionist.home');
                case 'accountant':
                    return view('accountant.home');
                case 'nurse':
                    return view('admin.nurse');
                case 'lab-technician':
                    return view('lab-technician.home');
                default:
                    return redirect()->back(); 
            }
        } else {
            return redirect()->back();
        }

    }
 
    public function appointment(Request $request)
    {
        $data = new Appointment;

        $data->name = $request->name;
        $data->email = $request->email;
        $data->date = $request->date;
        $data->number = $request->number;
        $data->message = $request->message;
        $data->doctor = $request->doctor;
        $data->status = 'pending';

        if (Auth::id()) {
            $data->user_id = Auth::user()->id;
        }

        $data->save();

        return redirect()->back()->with('message', 'Appointment Sent successfully. We will be in touch.');
    }
   
}
