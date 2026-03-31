<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    public function add_inventory()
    {
        $medicines = Medicine::all();
        return view('pharmacist.add_inventory', compact('medicines'));
    }

    public function inventory(Request $request)
    {
        // Ensure the user is logged in
        if (!Auth::check()) {
            return redirect()->back()->with('error', 'You must be logged in to add inventory.');
        }

        // Validate input
        $request->validate([
            'medicine_id' => 'required|exists:medicines,id',
            'stock_added' => 'required|integer|min:1',
            'stock_date' => 'required|date',
            'expiry_date' => 'nullable|date',
            'price' => 'required|numeric',
        ]);

        // Find the medicine to update the stock
        $medicine = Medicine::findOrFail($request->medicine_id);

        // Create new inventory record
        Inventory::create([
            'medicine_id'   => $request->medicine_id,
            'stock_added'   => $request->stock_added,
            'current_stock' => $medicine->current_stock + $request->stock_added, // This should be the new current stock
            'stock_date'    => $request->stock_date,
            'price'         => $request->price,
            'expiry_date'   => $request->expiry_date,
            'user_id'       => Auth::id(), // Use Auth::id() to get the authenticated user's ID
        ]);

        // Update the current stock of the medicine
        $medicine->current_stock += $request->stock_added;
        $medicine->save();

        // Redirect with success message
        return redirect()->back()->with('message', 'Inventory added successfully.');
    }

    public function getCurrentStock($medicineId)
    {
        // Fetch the latest inventory record for the medicine
        $currentStock = Inventory::where('medicine_id', $medicineId)->latest()->value('current_stock') ?? 0;

        // Return response as JSON
        return response()->json(['current_stock' => $currentStock]);
    }

    public function view_inventory()
    {
        $inventories = Inventory::all(); // Ensure the correct case is used here
        return view('pharmacist.view_inventory', compact('inventories'));
    }

    public function delete_inventory($id)
    {
        $inventory = Inventory::find($id); // Ensure correct case

        if ($inventory) {
            $inventory->delete();
            return redirect()->back()->with('message', 'Inventory deleted successfully.');
        }

        return redirect()->back()->with('error', 'Inventory item not found.');
    }

    public function edit($id)
    {
        $inventory = Inventory::findOrFail($id); // Fetch the inventory item
        $medicines = Medicine::all(); // Fetch all medicines for the dropdown

        return view('pharmacist.edit_inventory', compact('inventory', 'medicines')); // Return the view with data
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'medicine_id' => 'required|exists:medicines,id',
            'stock_added' => 'required|integer|min:1',
            'current_stock' => 'required|integer|min:0',
            'price' => 'required|numeric',
            'stock_date' => 'required|date',
            'expiry_date' => 'nullable|date|after_or_equal:stock_date',
        ]);

        // Find the inventory item
        $inventory = Inventory::findOrFail($id);

        // Update the inventory item
        $inventory->update([
            'medicine_id' => $request->medicine_id,
            'stock_added' => $request->stock_added,
            'current_stock' => $request->current_stock,
            'stock_date' => $request->stock_date,
            'expiry_date' => $request->expiry_date,
            'price' => $request->price,
        ]);

        // Flash message and redirect
        return redirect()->back()->with('message', 'Inventory updated successfully!');
    }


    /*public function getStockPrice($medicine_id)
    {
        $inventory = Inventory::where('medicine_id', $medicine_id)->first();
        if ($inventory) {
            return response()->json(['price' => $inventory->price]);
        } else {
            return response()->json(['error' => 'Medicine not found'], 404);
        }
    }*/

}
