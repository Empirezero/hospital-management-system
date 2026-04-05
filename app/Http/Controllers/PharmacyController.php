<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Medicine;
use App\Models\Prescription;

class PharmacyController extends Controller
{
    // ─── Medicines ────────────────────────────────────────────────────

    public function view_medicine()
    {
        return view('pharmacist.upload_medicine');
    }

    public function show_medicine()
    {
        $medicines = Medicine::latest()->get();
        return view('pharmacist.show_medicine', compact('medicines'));
    }

    public function upload_medicine(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'quantity'    => 'required|integer|min:0',
            'expiry_date' => 'required|date|after:today',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string',
        ]);

        $imagename = null;
        if ($request->hasFile('image')) {
            $image     = $request->file('image');
            $imagename = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('medicineimage'), $imagename);
        }

        Medicine::create([
            'name'        => $request->name,
            'price'       => $request->price,
            'stock'       => $request->quantity,
            'expiry_date' => $request->expiry_date,
            'description' => $request->description,
            'image'       => $imagename,
        ]);

        return redirect()->route('pharmacist.home')->with('message', 'Medicine added successfully.');
    }

    public function edit_medicine($id)
    {
        $medicine = Medicine::findOrFail($id);
        return view('pharmacist.edit_medicine', compact('medicine'));
    }

    public function update_medicine(Request $request, $id)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'quantity'    => 'required|integer|min:0',
            'expiry_date' => 'required|date',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string',
        ]);

        $medicine = Medicine::findOrFail($id);

        if ($request->hasFile('image')) {
            $image     = $request->file('image');
            $imagename = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('medicineimage'), $imagename);
            $medicine->image = $imagename;
        }

        $medicine->update([
            'name'        => $request->name,
            'price'       => $request->price,
            'stock'       => $request->quantity,
            'expiry_date' => $request->expiry_date,
            'description' => $request->description,
        ]);

        return redirect()->route('pharmacist.medicines')->with('message', 'Medicine updated successfully.');
    }

    public function delete_medicine($id)
    {
        Medicine::findOrFail($id)->delete();
        return redirect()->route('pharmacist.medicines')->with('message', 'Medicine deleted successfully.');
    }

    // ─── Prescriptions ────────────────────────────────────────────────

    public function pending_prescriptions()
    {
        $prescriptions = Prescription::where('status', 'pending')
                            ->with(['medicine', 'doctor', 'encounter.appointment'])
                            ->latest()
                            ->get();
        return view('pharmacist.prescriptions', compact('prescriptions'));
    }

    public function dispense($id)
    {
        $prescription = Prescription::findOrFail($id);

        // Deduct stock from medicine inventory
        $medicine = Medicine::findOrFail($prescription->medicine_id);
        if ($medicine->stock < 1) {
            return redirect()->back()->with('error', 'Insufficient stock for ' . $medicine->name);
        }

        $medicine->decrement('stock');
        $prescription->update(['status' => 'dispensed']);

        return redirect()->route('pharmacy.prescriptions')
                         ->with('message', 'Prescription dispensed and stock updated.');
    }

    public function cancel_prescription($id)
    {
        $prescription = Prescription::findOrFail($id);
        $prescription->update(['status' => 'cancelled']);
        return redirect()->route('pharmacy.prescriptions')
                         ->with('message', 'Prescription cancelled.');
    }

    public function all_prescriptions()
    {
        $prescriptions = Prescription::with(['medicine', 'doctor', 'encounter.appointment'])
                            ->latest()
                            ->get();
        return view('pharmacist.all_prescriptions', compact('prescriptions'));
    }
}