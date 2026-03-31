<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Doctor;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;

class PatientController extends Controller
{
    public function addview()
    {
        return view('patient.add_appointment');
    }

    public function index()

    {
        // Fetch doctors from the database
        $doctors = Doctor::all();
  
        // Pass the doctors' data to the view
        return view('patient.add_appointment', compact('doctors'));
    }
    public function my_appointment(){
       if(Auth::id()){

            $userid = Auth::user()->id;
            $appoint = Appointment::where('user_id',$userid)->get();
            return view('patient.my_appointment', compact('appoint'));
       }
       else{
        return redirect()->back();
       }
    }
    public function cancel_appoint($id){
        $data = Appointment::find($id);
        $data->delete();
        return redirect()->back();
    }
}
