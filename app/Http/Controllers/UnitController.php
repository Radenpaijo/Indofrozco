<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index()
    {
        $units = Unit::withCount('barangs')->latest()->paginate(15);
        return view('units.index', compact('units'));
    }

    public function create()
    {
        $kodeOtomatis = Unit::buatKodeOtomatis();
        return view('units.create', compact('kodeOtomatis'));
    }

    public function store(Request $permintaan)
    {
        if (!$permintaan->filled('code')) {
            $permintaan->merge(['code' => Unit::buatKodeOtomatis()]);
        }

        $permintaan->validate([
            'code' => 'required|string|max:50|unique:units,code',
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',
        ]);

        Unit::create($permintaan->all());

        return redirect()->route('units.index')->with('success', 'Satuan barang berhasil ditambahkan.');
    }

    public function edit(Unit $unit)
    {
        return view('units.edit', compact('unit'));
    }

    public function update(Request $permintaan, Unit $unit)
    {
        $permintaan->validate([
            'code' => 'required|string|max:50|unique:units,code,' . $unit->id,
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',
        ]);

        $unit->update($permintaan->all());

        return redirect()->route('units.index')->with('success', 'Satuan barang berhasil diperbarui.');
    }

    public function destroy(Unit $unit)
    {
        if ($unit->barangs()->count() > 0) {
            return redirect()->back()->with('error', 'Satuan tidak dapat dihapus karena masih digunakan oleh barang.');
        }

        $unit->delete();
        return redirect()->route('units.index')->with('success', 'Satuan barang berhasil dihapus.');
    }
}
