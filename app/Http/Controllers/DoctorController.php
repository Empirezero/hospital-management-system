<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;

class DoctorController extends Controller
{
    public function doctor_appointment()

    {

        if (Auth::id()) {

            $userid = Auth::user()->id;
            $appoint = Appointment::where('doctor', $userid)->get();
            return view('doctor.show_appointment', compact('appoint'));
        } else {
            return redirect()->back();
        }
      
    }


    public function update($id)
    {
        $appointment = Appointment::findOrFail($id);
        return view('doctor.update_appointment', compact('appointment'));
    }



    public function update_appointment(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'number' => 'required|string|max:15',
            'email' => 'required|email',
            'doctor' => 'required|string|max:255',
            'date' => 'required|date',
            'message' => 'required|string',
            'status' => 'required|string|in:Pending,Approved,Canceled',
        ]);

        $appointment = Appointment::find($id);
        $appointment->name = $request->input('name');
        $appointment->number = $request->input('number');
        $appointment->email = $request->input('email');
        $appointment->doctor = $request->input('doctor');
        $appointment->date = $request->input('date');
        $appointment->message = $request->input('message');
        $appointment->status = $request->input('status');
        $appointment->save();

        return redirect()->back()->with('success', 'Appointment updated successfully.');
    }

  
}
