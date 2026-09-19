<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        $query = Barang::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('kode_barang', 'like', "%{$search}%")
                  ->orWhere('kode_rak', 'like', "%{$search}%");
            });
        }

        if ($request->filled('lokasi')) {
            $query->where('lokasi_gudang', $request->lokasi);
        }

        if ($request->filled('status')) {
            if ($request->status === 'low_stock') {
                $query->where('stok', '<=', \DB::raw('stok_minimum'));
            } elseif ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $barangs = $query->orderBy('kode_barang', 'asc')->paginate(10)->withQueryString();

        return view('barangs.index', compact('barangs'));
    }

    public function create()
    {
        return view('barangs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_barang' => 'required|unique:barangs,kode_barang',
            'nama_barang' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
            'stok' => 'nullable|integer|min:0',
            'stok_minimum' => 'required|integer|min:0',
            'batas_umur_hari' => 'required|integer|min:0',
            'lokasi_gudang' => 'required|in:Gudang Kering,Cold Storage',
            'kode_rak' => 'nullable|string|max:50',
        ]);

        $data = $request->all();
        $data['stok'] = $request->stok ?? 0;
        $data['is_active'] = $request->has('is_active') ? true : true;

        // $jumlah_barang = Barang::count();
        // $nama_barang = "BRG - " . $jumlah_barang + 1;

        $barang = Barang::create($data);

        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'CREATE_ITEM',
            'module' => 'MASTER_BARANG',
            'record_id' => $barang->id,
            'new_values' => $data,
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('barangs.index')->with('success', 'Barang berhasil ditambahkan!');
    }

    public function edit(Barang $barang)
    {
        return view('barangs.edit', compact('barang'));
    }

    public function update(Request $request, Barang $barang)
    {
        $request->validate([
            'kode_barang' => 'required|unique:barangs,kode_barang,' . $barang->id,
            'nama_barang' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
            'stok' => 'required|integer|min:0',
            'stok_minimum' => 'required|integer|min:0',
            'batas_umur_hari' => 'required|integer|min:0',
            'lokasi_gudang' => 'required|in:Gudang Kering,Cold Storage',
            'kode_rak' => 'nullable|string|max:50',
        ]);

        $oldValues = $barang->toArray();
        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        $barang->update($data);

        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'UPDATE_ITEM',
            'module' => 'MASTER_BARANG',
            'record_id' => $barang->id,
            'old_values' => $oldValues,
            'new_values' => $barang->fresh()->toArray(),
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('barangs.index')->with('success', 'Barang berhasil diperbarui!');
    }

    public function destroy(Barang $barang)
    {
        // Check if there are related transactions
        if ($barang->barangMasuks()->exists() || $barang->barangKeluars()->exists()) {
            // Soft deactivate instead of hard delete to preserve historical integrity
            $barang->update(['is_active' => false]);

            \App\Models\AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'DEACTIVATE_ITEM',
                'module' => 'MASTER_BARANG',
                'record_id' => $barang->id,
                'new_values' => ['is_active' => false],
                'ip_address' => request()->ip(),
            ]);

            return redirect()->route('barangs.index')->with('warning', 'Barang memiliki riwayat transaksi. Status dinonaktifkan alih-alih dihapus.');
        }

        $barangId = $barang->id;
        $barang->delete();

        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'DELETE_ITEM',
            'module' => 'MASTER_BARANG',
            'record_id' => $barangId,
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('barangs.index')->with('success', 'Barang berhasil dihapus!');
    }

    public function show(Barang $barang)
    {
        $barangMasuk = $barang->barangMasuks()->with('user')->orderBy('tanggal', 'desc')->get();
        $barangKeluar = $barang->barangKeluars()->with('user')->orderBy('tanggal', 'desc')->get();
        $expiringBatches = $barang->getExpiringBatches();

        return view('barangs.show', compact('barang', 'barangMasuk', 'barangKeluar', 'expiringBatches'));
    }
}
