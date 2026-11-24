<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangKeluar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class BarangKeluarController extends Controller
{
    public function index()
    {
        $barangKeluars = BarangKeluar::with(['barang', 'user', 'permintaanBarang', 'inventoryTransactions.batch'])
            ->orderBy('tanggal', 'desc')
            ->paginate(10);

        return view('barang_keluar.index', compact('barangKeluars'));
    }

    public function create()
    {
        $barangs = Barang::where('is_active', true)->where('stok', '>', 0)->get();
        return view('barang_keluar.create', compact('barangs'));
    }

    public function store(Request $request)
    {
        $barang = Barang::find($request->barang_id);
        $maxStock = $barang ? $barang->stok : 0;

        $request->validate([
            'barang_id' => 'required|exists:barangs,id',
            'jumlah' => 'required|numeric|min:0.01|max:' . $maxStock,
            'tanggal' => 'required|date',
        ], [
            'barang_id.required' => 'Pilih barang yang akan dikeluarkan.',
            'jumlah.required' => 'Jumlah pengeluaran barang wajib diisi.',
            'jumlah.min' => 'Jumlah pengeluaran minimal 0.01.',
            'jumlah.max' => "Jumlah melebihi stok yang tersedia di gudang ({$maxStock}).",
            'tanggal.required' => 'Tanggal pengeluaran wajib diisi.',
        ]);

        try {
            DB::beginTransaction();

            // Create BarangKeluar (triggers validation and stock decrement in observer)
            BarangKeluar::create([
                'permintaan_id' => null, // Manual release
                'barang_id' => $request->barang_id,
                'jumlah' => $request->jumlah,
                'user_id' => auth()->id(),
                'tanggal' => $request->tanggal,
            ]);

            DB::commit();
            return redirect()->route('barang-keluar.index')->with('success', 'Pengeluaran barang berhasil dicatat.');

        } catch (Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal mencatat pengeluaran barang: ' . $e->getMessage());
        }
    }
}
