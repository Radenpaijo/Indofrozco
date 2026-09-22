<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Warehouse;
use App\Models\Barang;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::with('warehouse')->latest()->paginate(15);

        // Tambah kapasitas terpakai secara dinamis
        $locations->getCollection()->transform(function ($lokasi) {
            $lokasi->used_capacity = $lokasi->hitungKapasitasTerpakai();
            return $lokasi;
        });

        return view('locations.index', compact('locations'));
    }

    public function create()
    {
        $warehouses = Warehouse::where('status', 'active')->get();
        $nextCode = Location::buatKodeOtomatis();

        return view('locations.create', compact('warehouses', 'nextCode'));
    }

    public function store(Request $permintaan)
    {
        if (!$permintaan->filled('code')) {
            $permintaan->merge(['code' => Location::buatKodeOtomatis()]);
        }

        $permintaan->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'code' => 'required|string|max:50|unique:locations,code',
            'name' => 'required|string|max:255',
            'capacity' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        Location::create($permintaan->all());

        return redirect()->route('locations.index')->with('success', 'Lokasi/Rak berhasil ditambahkan.');
    }

    public function edit(Location $location)
    {
        $warehouses = Warehouse::where('status', 'active')->get();
        $location->used_capacity = $location->hitungKapasitasTerpakai();

        return view('locations.edit', compact('location', 'warehouses'));
    }

    public function update(Request $permintaan, Location $location)
    {
        $permintaan->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'code' => 'required|string|max:50|unique:locations,code,' . $location->id,
            'name' => 'required|string|max:255',
            'capacity' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        if ($permintaan->filled('capacity') && $permintaan->capacity > 0) {
            $kapasitasTerpakai = $location->hitungKapasitasTerpakai();
            if ($permintaan->capacity < $kapasitasTerpakai) {
                return back()->withInput()->withErrors([
                    'capacity' => "Kapasitas tidak boleh lebih kecil dari total stok terisi saat ini ({$kapasitasTerpakai})."
                ]);
            }
        }

        $location->update($permintaan->all());

        return redirect()->route('locations.index')->with('success', 'Lokasi/Rak berhasil diperbarui.');
    }

    public function destroy(Location $location)
    {
        $kapasitasTerpakai = $location->hitungKapasitasTerpakai();
        if ($kapasitasTerpakai > 0) {
            return redirect()->back()->with('error', 'Lokasi/Rak tidak dapat dihapus karena masih menyimpan stok barang.');
        }

        $location->delete();
        return redirect()->route('locations.index')->with('success', 'Lokasi/Rak berhasil dihapus.');
    }
}
