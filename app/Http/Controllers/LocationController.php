<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::with('warehouse')->latest()->paginate(15);
        return view('locations.index', compact('locations'));
    }

    public function create()
    {
        $warehouses = Warehouse::where('status', 'active')->get();
        return view('locations.create', compact('warehouses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'code' => 'required|string|max:50|unique:locations,code',
            'name' => 'required|string|max:255',
            'capacity' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        Location::create($request->all());

        return redirect()->route('locations.index')->with('success', 'Lokasi/Rak berhasil ditambahkan.');
    }

    public function edit(Location $location)
    {
        $warehouses = Warehouse::where('status', 'active')->get();
        return view('locations.edit', compact('location', 'warehouses'));
    }

    public function update(Request $request, Location $location)
    {
        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'code' => 'required|string|max:50|unique:locations,code,' . $location->id,
            'name' => 'required|string|max:255',
            'capacity' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $location->update($request->all());

        return redirect()->route('locations.index')->with('success', 'Lokasi/Rak berhasil diperbarui.');
    }

    public function destroy(Location $location)
    {
        $location->delete();
        return redirect()->route('locations.index')->with('success', 'Lokasi/Rak berhasil dihapus.');
    }
}
