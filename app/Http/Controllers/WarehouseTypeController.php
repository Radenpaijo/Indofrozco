<?php

namespace App\Http\Controllers;

use App\Models\WarehouseType;
use Illuminate\Http\Request;

class WarehouseTypeController extends Controller
{
    public function index(Request $permintaan)
    {
        $query = WarehouseType::withCount('warehouses');

        if ($permintaan->filled('search')) {
            $pencarian = $permintaan->search;
            $query->where(function ($q) use ($pencarian) {
                $q->where('name', 'like', "%{$pencarian}%")
                    ->orWhere('code', 'like', "%{$pencarian}%");
            });
        }

        if ($permintaan->filled('status')) {
            $query->where('status', $permintaan->status);
        }

        $types = $query->latest()->paginate(15)->withQueryString();

        return view('warehouse_types.index', compact('types'));
    }

    public function create()
    {
        $nextCode = WarehouseType::buatKodeOtomatis();
        return view('warehouse_types.create', compact('nextCode'));
    }

    public function store(Request $permintaan)
    {
        if (!$permintaan->filled('code')) {
            $permintaan->merge(['code' => WarehouseType::buatKodeOtomatis()]);
        }

        $permintaan->validate([
            'code' => 'required|string|max:50|unique:warehouse_types,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        WarehouseType::create($permintaan->all());

        return redirect()->route('warehouse-types.index')->with('success', 'Tipe Gudang berhasil ditambahkan.');
    }

    public function edit(WarehouseType $warehouseType)
    {
        return view('warehouse_types.edit', compact('warehouseType'));
    }

    public function update(Request $permintaan, WarehouseType $warehouseType)
    {
        $permintaan->merge(['code' => $warehouseType->code]);

        $permintaan->validate([
            'code' => 'required|string|max:50|unique:warehouse_types,code,' . $warehouseType->id,
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',
        ]);

        $warehouseType->update($permintaan->all());

        return redirect()->route('warehouse-types.index')->with('success', 'Tipe Gudang berhasil diperbarui.');
    }

    public function destroy(WarehouseType $warehouseType)
    {
        if ($warehouseType->warehouses()->count() > 0) {
            return redirect()->back()->with('error', 'Tipe gudang tidak dapat dihapus karena sedang digunakan oleh data gudang.');
        }

        $warehouseType->delete();
        return redirect()->route('warehouse-types.index')->with('success', 'Tipe Gudang berhasil dihapus.');
    }
}
