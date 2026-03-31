<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Doctor;

class FrontendController extends Controller
{
    public function index()

    {
        // Fetch doctors from the database
        $doctors = Doctor::all();
        //return view('frontend.home.index');
        // Pass the doctors' data to the view
        return view('frontend.layouts.master', compact('doctors'));
    }

}



