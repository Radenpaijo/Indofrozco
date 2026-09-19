<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\PermintaanPembelian;
use App\Models\PermintaanPembelianDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class PermintaanPembelianController extends Controller
{
    public function index()
    {
        $prs = PermintaanPembelian::with(['user', 'details.barang'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('permintaan_pembelian.index', compact('prs'));
    }

    public function create()
    {
        $barangs = Barang::where('is_active', true)->get();
        return view('permintaan_pembelian.create', compact('barangs'));
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

            // Generate No PR: PR-YYYYMMDD-XXXX
            $datePrefix = 'PR-' . date('Ymd');
            $lastPR = PermintaanPembelian::where('no_pr', 'like', "{$datePrefix}%")
                ->orderBy('no_pr', 'desc')
                ->first();

            $nextNumber = 1;
            if ($lastPR) {
                $lastNum = (int) substr($lastPR->no_pr, -4);
                $nextNumber = $lastNum + 1;
            }
            $noPR = $datePrefix . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            $pr = PermintaanPembelian::create([
                'user_id' => auth()->id(),
                'no_pr' => $noPR,
                'status' => 'pending',
                'tanggal' => now(),
            ]);

            foreach ($request->items as $item) {
                PermintaanPembelianDetail::create([
                    'pp_id' => $pr->id,
                    'barang_id' => $item['barang_id'],
                    'jumlah' => $item['jumlah'],
                ]);
            }

            DB::commit();
            return redirect()->route('permintaan-pembelian.index')->with('success', 'Permintaan pembelian (PR) berhasil dibuat: ' . $noPR);

        } catch (Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal membuat PR: ' . $e->getMessage());
        }
    }

    public function show(PermintaanPembelian $pr)
    {
        $pr->load(['user', 'details.barang', 'purchaseOrders']);
        return view('permintaan_pembelian.show', compact('pr'));
    }

    public function cancel(PermintaanPembelian $pr)
    {
        if ($pr->status !== 'pending') {
            return back()->with('error', 'PR ini sudah diproses dan tidak bisa dibatalkan.');
        }

        $pr->update(['status' => 'dibatalkan']);
        return redirect()->route('permintaan-pembelian.show', $pr->id)->with('success', 'PR berhasil dibatalkan.');
    }
}
