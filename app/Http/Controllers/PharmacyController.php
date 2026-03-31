<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Medicine;

class PharmacyController extends Controller
{
    public function view_medicine()
    {
        return view('pharmacist.upload_medicine');
    }

    public function upload_medicine(Request $request)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'expiry_date' => 'required|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string',
        ]);

        // Create a new medicine entry
        $medicine = new Medicine;
        $medicine->name = $request->input('name');
        $medicine->price = $request->input('price');
        $medicine->stock = $request->input('quantity');
        $medicine->expiry_date = $request->input('expiry_date');
        $medicine->description = $request->input('description');

        // Handle the image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image'); // Corrected to access 'image' file
            $imagename = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('doctorimage'), $imagename); // Corrected to use public_path
            $medicine->image = $imagename; // Assign image name to medicine
        }

        // Save the medicine record
        $medicine->save();

        return redirect()->back()->with('message', 'Medicine added successfully!');
    }

    public function show_medicine()
    {
        $medicines = Medicine::all();
        return view('pharmacist.show_medicine', compact('medicines'));
    }
     public function delete_medicine($id){
        $medicine = Medicine::find($id);
        $medicine ->delete();
        return redirect()-> back();
     }
     public function edit_medicine($id){
        $medicine = Medicine::find($id);
        return view('pharmacist.edit_medicine',compact('medicine'));
     }

    public function update_medicine(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'expiry_date' => 'required|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string',
        ]);

        // Find the existing medicine entry by ID
        $medicine = Medicine::find($id);
        if (!$medicine) {
            return redirect()->back()->with('error', 'Medicine not found!');
        }

        // Update the medicine fields
        $medicine->name = $request->input('name');
        $medicine->price = $request->input('price');
        $medicine->stock = $request->input('quantity');
        $medicine->expiry_date = $request->input('expiry_date');
        $medicine->description = $request->input('description');

        // Handle the image upload if a new image is provided
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagename = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('doctorimage'), $imagename);
            $medicine->image = $imagename; // Update image path in the database
        }

        // Save the updated medicine record
        $medicine->save();

        return redirect()->back()->with('message', 'Medicine updated successfully!');
    }

}
