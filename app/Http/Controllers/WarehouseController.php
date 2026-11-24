<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\WarehouseType;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index()
    {
        $warehouses = Warehouse::with(['warehouseType'])->withCount('locations')->latest()->paginate(15);
        return view('warehouses.index', compact('warehouses'));
    }

    public function create()
    {
        $types = WarehouseType::where('status', 'active')->get();
        $nextCode = Warehouse::buatKodeOtomatis();
        return view('warehouses.create', compact('types', 'nextCode'));
    }

    public function store(Request $permintaan)
    {
        if (!$permintaan->filled('code')) {
            $permintaan->merge(['code' => Warehouse::buatKodeOtomatis()]);
        }

        $permintaan->validate([
            'code' => 'required|string|max:50|unique:warehouses,code',
            'name' => 'required|string|max:255',
            'warehouse_type_id' => 'required|exists:warehouse_types,id',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',
        ]);

        Warehouse::create($permintaan->all());

        return redirect()->route('warehouses.index')->with('success', 'Gudang berhasil ditambahkan.');
    }

    public function edit(Warehouse $warehouse)
    {
        $types = WarehouseType::where('status', 'active')->get();
        return view('warehouses.edit', compact('warehouse', 'types'));
    }

    public function update(Request $permintaan, Warehouse $warehouse)
    {
        $permintaan->merge(['code' => $warehouse->code]);

        $permintaan->validate([
            'code' => 'required|string|max:50|unique:warehouses,code,' . $warehouse->id,
            'name' => 'required|string|max:255',
            'warehouse_type_id' => 'required|exists:warehouse_types,id',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',
        ]);

        $warehouse->update($permintaan->all());

        return redirect()->route('warehouses.index')->with('success', 'Gudang berhasil diperbarui.');
    }

    public function destroy(Warehouse $warehouse)
    {
        if ($warehouse->locations()->count() > 0) {
            return redirect()->back()->with('error', 'Gudang tidak dapat dihapus karena masih memiliki lokasi/rak.');
        }

        $warehouse->delete();
        return redirect()->route('warehouses.index')->with('success', 'Gudang berhasil dihapus.');
    }
}
