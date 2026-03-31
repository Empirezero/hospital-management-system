<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{

    public function addview()
    {
        return view('admin.add_doctor');
    }

    public function index()
    {
        return view('admin.index');
    }

 public function upload_doctor(Request $request)
    {
        $doctor = new Doctor;
        $image = $request->file;
        $imagename = time() . '.' . $image->getClientOriginalExtension();
        $request->file->move('doctorimage', $imagename);
        $doctor->image = $imagename;

        $doctor->name = $request->name;
        $doctor->number = $request->number;
        $doctor->room = $request->room;
        $doctor->location = $request->location;
        $doctor->speciality = $request->speciality;

        $doctor->save();

        return redirect()->back()->with('message', 'Doctor Added Successfully');
    }
   

    public
    function add_appointment(){
        // Fetch doctors from the database
        $doctors = Doctor::all();
 
     return view('admin.add_appointment',compact('doctors'));
    }


    public function show_appointment()
    {

        if (Auth::id()) {
            if (Auth::user()->role == "admin") {

                $data = Appointment::all();

                return view('admin.show_appointment', compact('data'));
            } else {

                return redirect('login');
            }
        }
    }
    public function delete_appoint($id)
    {
        $data = Appointment::find($id);
        $data->delete();
        return redirect()->back();
    }

    public function update_appoint($id){
        // Retrieve the appointment by ID
        $appointment = Appointment::findOrFail($id);

        // Pass the appointment data to the view
        return view('admin.update_appoint', compact('appointment'));
    
    }

    public function appointment_update(Request $request, $id)
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




    public function view_doctor()
    {
        if (Auth::id()) {
            if (Auth::user()->role == "admin") {
                $data = Doctor::all();

                return view('admin.view_doctor', compact('data'));
            } else {

                return redirect()->back();
            }
        } else {
            return redirect('login');
        }
    }

    public function delete_doctor($id)
    {
        $data = Doctor::find($id);
        $data->delete();

        return redirect()->back();
    }

    public function show_doctor($id)
    {

        $data = doctor::find($id);
       return view('admin.update_doctor',compact('data'));
    }

    public function edit_doctor(Request $request, $id)
    {
        $doctor = Doctor::find($id);
        $doctor->name = $request->name;
        $doctor->number = $request->number;
        $doctor->speciality = $request->speciality;
        $doctor->room = $request->room;
        $doctor->location = $request->location;

        $image = $request->file;

        if ($image) {
            $imagename = time() . '.' . $image->getClientOriginalExtension();
            $request->file->move('doctorimage', $imagename);
            $doctor->image = $imagename;
        }

        $doctor->save();

        return redirect('view_doctor')->with('message', 'Doctor details updated successfully');
    }
}
