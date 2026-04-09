<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    public function Add_inventory()
    {
        $medicines = Medicine::all();
        return view('pharmacist.add_inventory', compact('medicines'));
    }

    public function view_inventory()
    {
        $inventories = Inventory::with('medicine')->latest()->get();
        $lowStock = Medicine:: where('stock', '<=', 10)->where('stock', '>', 0)->get();
        $outOfStock = Medicine:: where('stock', '=', 0)->get();
        return view('pharmacist.view_inventory', compact('inventories'));
    }

    public function inventory(Request $request)
    {
        $request->validate([
            'medicine_id' => 'required|exists:medicines,id',
            'stock_added' => 'required|integer|min:1',
            'stock_date'  => 'required|date',
            'expiry_date' => 'nullable|date|after:today',
            'price'       => 'required|numeric|min:0',
        ]);

        $medicine      = Medicine::findOrFail($request->medicine_id);
        $newStock      = $medicine->stock + $request->stock_added;

        Inventory::create([
            'medicine_id'   => $request->medicine_id,
            'stock_added'   => $request->stock_added,
            'current_stock' => $newStock,
            'stock_date'    => $request->stock_date,
            'expiry_date'   => $request->expiry_date,
            'price'         => $request->price,
            'user_id'       => Auth::id(),
        ]);

        // Keep medicine stock in sync
        $medicine->update(['stock' => $newStock]);

        return redirect()->route('pharmacist.inventory')->with('message', 'Inventory added successfully.');
    }

    public function edit($id)
    {
        $inventory = Inventory::findOrFail($id);
        $medicines = Medicine::all();
        return view('pharmacist.edit_inventory', compact('inventory', 'medicines'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'medicine_id'   => 'required|exists:medicines,id',
            'stock_added'   => 'required|integer|min:1',
            'current_stock' => 'required|integer|min:0',
            'price'         => 'required|numeric|min:0',
            'stock_date'    => 'required|date',
            'expiry_date'   => 'nullable|date|after_or_equal:stock_date',
        ]);

        Inventory::findOrFail($id)->update([
            'medicine_id'   => $request->medicine_id,
            'stock_added'   => $request->stock_added,
            'current_stock' => $request->current_stock,
            'stock_date'    => $request->stock_date,
            'expiry_date'   => $request->expiry_date,
            'price'         => $request->price,
        ]);

        return redirect()->route('pharmacist.inventory')->with('message', 'Inventory updated successfully.');
    }

    public function delete_inventory($id)
    {
        Inventory::findOrFail($id)->delete();
        return redirect()->route('pharmacist.inventory')->with('message', 'Inventory deleted successfully.');
    }

    public function getCurrentStock($medicineId)
    {
        $currentStock = Inventory::where('medicine_id', $medicineId)
            ->latest()
            ->value('current_stock') ?? 0;

        return response()->json(['current_stock' => $currentStock]);
    }

    public function getStockPrice($medicine_id)
    {
        $inventory = Inventory::where('medicine_id', $medicine_id)->latest()->first();
        $medicine  = Medicine::find($medicine_id);

        return response()->json([
            'current_stock' => $inventory ? $inventory->current_stock : ($medicine->stock ?? 0),
            'price'         => $inventory ? $inventory->price : 0,
        ]);
    }
    }
