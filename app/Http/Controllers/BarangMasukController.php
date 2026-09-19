<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\PurchaseOrder;
use App\Models\BarangMasuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class BarangMasukController extends Controller
{
    public function index()
    {
        $barangMasuks = BarangMasuk::with(['barang', 'user', 'purchaseOrder'])
            ->orderBy('tanggal', 'desc')
            ->paginate(10);

        return view('barang_masuk.index', compact('barangMasuks'));
    }

    public function create(Request $request)
    {
        $barangs = Barang::where('is_active', true)->get();
        
        // Fetch active POs for receipt linking
        $activePOs = PurchaseOrder::whereIn('status', ['dikirim', 'diterima_sebagian'])
            ->with(['supplier', 'details.barang'])
            ->get();

        $selectedPO = null;
        if ($request->filled('po_id')) {
            $selectedPO = PurchaseOrder::with(['supplier', 'details.barang'])->find($request->po_id);
            if ($selectedPO && !in_array($selectedPO->status, ['dikirim', 'diterima_sebagian'])) {
                return redirect()->route('barang-masuk.create')->with('error', 'Purchase Order ini sudah selesai atau dibatalkan.');
            }
        }

        return view('barang_masuk.create', compact('barangs', 'activePOs', 'selectedPO'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'po_id' => 'nullable|exists:purchase_orders,id',
            'no_surat_jalan' => 'required|string|max:100',
            'tanggal' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barangs,id',
            'items.*.jumlah' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            // Generate No Masuk: IN-YYYYMMDD-XXXX
            $datePrefix = 'IN-' . date('Ymd');
            $lastIn = BarangMasuk::where('no_masuk', 'like', "{$datePrefix}%")
                ->orderBy('no_masuk', 'desc')
                ->first();

            $nextNumber = 1;
            if ($lastIn) {
                $lastNum = (int) substr($lastIn->no_masuk, -4);
                $nextNumber = $lastNum + 1;
            }
            $noMasuk = $datePrefix . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            // Record each item
            foreach ($request->items as $item) {
                // If it is fresh shrimp, the observer will auto-create a corresponding BarangKeluar to production.
                BarangMasuk::create([
                    'no_masuk' => $noMasuk,
                    'po_id' => $request->po_id,
                    'barang_id' => $item['barang_id'],
                    'jumlah' => $item['jumlah'],
                    'no_surat_jalan' => $request->no_surat_jalan,
                    'user_id' => auth()->id(),
                    'tanggal' => $request->tanggal,
                ]);
            }

            // Recalculate PO status if linked
            if ($request->filled('po_id')) {
                $po = PurchaseOrder::with('details')->find($request->po_id);
                $allCompleted = true;
                $anyReceived = false;

                foreach ($po->details as $detail) {
                    $totalReceived = BarangMasuk::where('po_id', $po->id)
                        ->where('barang_id', $detail->barang_id)
                        ->sum('jumlah');

                    if ($totalReceived < $detail->jumlah) {
                        $allCompleted = false;
                    }
                    if ($totalReceived > 0) {
                        $anyReceived = true;
                    }
                }

                if ($allCompleted) {
                    $po->update(['status' => 'selesai']);
                } elseif ($anyReceived) {
                    $po->update(['status' => 'diterima_sebagian']);
                }
            }

            DB::commit();
            return redirect()->route('barang-masuk.index')->with('success', 'Penerimaan barang berhasil dicatat dengan nomor: ' . $noMasuk);

        } catch (Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal mencatat penerimaan barang: ' . $e->getMessage());
        }
    }
}
