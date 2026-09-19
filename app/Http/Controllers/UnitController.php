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
        return view('units.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:units,code',
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',
        ]);

        Unit::create($request->all());

        return redirect()->route('units.index')->with('success', 'Satuan barang berhasil ditambahkan.');
    }

    public function edit(Unit $unit)
    {
        return view('units.edit', compact('unit'));
    }

    public function update(Request $request, Unit $unit)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:units,code,' . $unit->id,
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',
        ]);

        $unit->update($request->all());

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
