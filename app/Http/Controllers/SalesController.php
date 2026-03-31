<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Sale;
use App\Models\Medicine;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function create()
    {
        $inventories = Inventory::all();
        $medicines = Medicine::all();
        return view('pharmacist.add_sales', compact('inventories', 'medicines'));
    }

    public function add_sale(Request $request)
    {
        // Validate incoming request data
        $validated = $request->validate([
            'medicine_id' => 'required|exists:medicines,id',
            'quantity_sold' => 'required|integer|min:1',
            'user_id' => 'required|exists:users,id',
        ]);

        // Get the inventory entry for the selected medicine
        $inventory = Inventory::where('medicine_id', $validated['medicine_id'])->firstOrFail();

        // Ensure that the current stock is sufficient for the sale
        if ($inventory->current_stock < $validated['quantity_sold']) {
            return response()->json(['error' => 'Insufficient stock'], 400);
        }

        // Calculate the total price of the sale
        $total_price = $inventory->price * $validated['quantity_sold'];

        // Record the sale
        Sale::create([
            'medicine_id' => $validated['medicine_id'],
            'quantity_sold' => $validated['quantity_sold'],
            'total_price' => $total_price,
            'user_id' => $validated['user_id'],
        ]);

        // Update the inventory stock
        $inventory->decrement('current_stock', $validated['quantity_sold']);

        return response()->json(['success' => 'Sale recorded successfully']);
    }
}

