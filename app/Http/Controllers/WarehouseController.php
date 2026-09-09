<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\WarehouseLocation;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index()
    {
        $warehouses = Warehouse::with(['locations' => function ($q) {
            $q->withCount('archives');
        }])->get();

        return view('master.warehouses', compact('warehouses'));
    }

    public function storeWarehouse(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:warehouses,code',
            'name' => 'required|string|max:100',
            'address' => 'nullable|string',
        ]);

        Warehouse::create($validated);

        return redirect()->route('master.warehouses')
            ->with('success', "Gudang {$validated['name']} berhasil ditambahkan.");
    }

    public function updateWarehouse(Request $request, Warehouse $warehouse)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:warehouses,code,' . $warehouse->id,
            'name' => 'required|string|max:100',
            'address' => 'nullable|string',
        ]);

        $warehouse->update($validated);

        return redirect()->route('master.warehouses')
            ->with('success', "Data gudang {$warehouse->name} berhasil diperbarui.");
    }

    public function destroyWarehouse(Warehouse $warehouse)
    {
        if ($warehouse->locations()->count() > 0) {
            return back()->with('error', "Gudang {$warehouse->name} tidak dapat dihapus karena masih memiliki lokasi rak aktif.");
        }

        $warehouse->delete();

        return redirect()->route('master.warehouses')
            ->with('success', "Gudang {$warehouse->name} berhasil dihapus.");
    }

    public function storeLocation(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'rack_code' => 'required|string|max:20',
            'shelf_code' => 'required|string|max:20',
            'box_capacity' => 'required|integer|min:1|max:1000',
        ]);

        WarehouseLocation::create($validated);

        return redirect()->route('master.warehouses')
            ->with('success', 'Lokasi penyimpanan rak/baris berhasil ditambahkan.');
    }

    public function destroyLocation(WarehouseLocation $location)
    {
        if ($location->current_box_count > 0) {
            return back()->with('error', 'Lokasi rak ini tidak dapat dihapus karena masih menampung box arsip aktif.');
        }

        $location->delete();

        return redirect()->route('master.warehouses')
            ->with('success', 'Lokasi rak berhasil dihapus.');
    }
}
