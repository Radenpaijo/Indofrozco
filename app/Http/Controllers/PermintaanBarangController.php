<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\PermintaanBarang;
use App\Models\PermintaanBarangDetail;
use App\Models\BarangKeluar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class PermintaanBarangController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = PermintaanBarang::with(['user', 'details.barang']);

        // Staf Produksi can only see their own requests
        if ($user->role === 'produksi') {
            $query->where('user_id', $user->id);
        }

        $permintaans = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('permintaan_barang.index', compact('permintaans'));
    }

    public function create()
    {
        $barangs = Barang::where('is_active', true)->get();
        return view('permintaan_barang.create', compact('barangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barangs,id',
            'items.*.jumlah' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            // Generate No Permintaan: REQ-YYYYMMDD-XXXX
            $datePrefix = 'REQ-' . date('Ymd');
            $lastReq = PermintaanBarang::where('no_permintaan', 'like', "{$datePrefix}%")
                ->orderBy('no_permintaan', 'desc')
                ->first();

            $nextNumber = 1;
            if ($lastReq) {
                $lastNum = (int) substr($lastReq->no_permintaan, -4);
                $nextNumber = $lastNum + 1;
            }
            $noPermintaan = $datePrefix . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            $permintaan = PermintaanBarang::create([
                'user_id' => auth()->id(),
                'no_permintaan' => $noPermintaan,
                'status' => 'pending',
                'tanggal' => now(),
            ]);

            foreach ($request->items as $item) {
                PermintaanBarangDetail::create([
                    'permintaan_id' => $permintaan->id,
                    'barang_id' => $item['barang_id'],
                    'jumlah' => $item['jumlah'],
                ]);
            }

            DB::commit();
            return redirect()->route('permintaan-barang.index')->with('success', 'Permintaan barang berhasil diajukan dengan nomor: ' . $noPermintaan);

        } catch (Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal mengajukan permintaan: ' . $e->getMessage());
        }
    }

    public function show(PermintaanBarang $permintaan)
    {
        $permintaan->load(['user', 'details.barang', 'barangKeluars.barang']);
        return view('permintaan_barang.show', compact('permintaan'));
    }

    public function approve(Request $request, PermintaanBarang $permintaan)
    {
        if ($permintaan->status !== 'pending') {
            return back()->with('error', 'Permintaan ini sudah diproses sebelumnya.');
        }

        try {
            DB::beginTransaction();

            // Update status to diproses
            $permintaan->update(['status' => 'selesai']);

            // Create BarangKeluar for each item (this triggers stock checks in observer)
            foreach ($permintaan->details as $detail) {
                BarangKeluar::create([
                    'permintaan_id' => $permintaan->id,
                    'barang_id' => $detail->barang_id,
                    'jumlah' => $detail->jumlah,
                    'user_id' => auth()->id(), // Staf Gudang who approves
                    'tanggal' => now(),
                ]);
            }

            DB::commit();
            return redirect()->route('permintaan-barang.show', $permintaan->id)->with('success', 'Permintaan disetujui dan stok telah dikurangi.');

        } catch (Exception $e) {
            DB::rollBack();
            // Reset status back to pending
            $permintaan->update(['status' => 'pending']);
            return redirect()->route('permintaan-barang.show', $permintaan->id)->with('error', 'Gagal menyetujui permintaan: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, PermintaanBarang $permintaan)
    {
        if ($permintaan->status !== 'pending') {
            return back()->with('error', 'Permintaan ini sudah diproses sebelumnya.');
        }

        $permintaan->update(['status' => 'ditolak']);

        return redirect()->route('permintaan-barang.show', $permintaan->id)->with('success', 'Permintaan telah ditolak.');
    }
}
