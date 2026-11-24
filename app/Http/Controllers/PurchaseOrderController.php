<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Supplier;
use App\Models\PermintaanPembelian;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseOrder::with(['supplier', 'user', 'permintaanPembelian']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_po', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function ($sq) use ($search) {
                      $sq->where('nama_supplier', 'like', "%{$search}%");
                  });
            });
        }

        $counts = [
            'semua' => (clone $query)->count(),
            'dikirim' => (clone $query)->where('status', 'dikirim')->count(),
            'diterima_sebagian' => (clone $query)->where('status', 'diterima_sebagian')->count(),
            'selesai' => (clone $query)->where('status', 'selesai')->count(),
            'dibatalkan' => (clone $query)->where('status', 'dibatalkan')->count(),
        ];

        if ($request->filled('status') && in_array($request->status, ['dikirim', 'diterima_sebagian', 'selesai', 'dibatalkan'])) {
            $query->where('status', $request->status);
        }

        $pos = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('purchase_orders.index', compact('pos', 'counts'));
    }

    public function create(Request $request)
    {
        $suppliers = Supplier::where('is_active', true)->get();
        $barangs = Barang::where('is_active', true)->get();
        
        $linkedPR = null;
        if ($request->filled('pr_id')) {
            $linkedPR = PermintaanPembelian::with('details.barang')
                ->where('status', 'pending')
                ->find($request->pr_id);
        }

        return view('purchase_orders.create', compact('suppliers', 'barangs', 'linkedPR'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'pp_id' => 'nullable|exists:permintaan_pembelian,id',
            'syarat_pembayaran' => 'nullable|string|max:100',
            'catatan' => 'nullable|string',
            'pajak_persen' => 'nullable|numeric|min:0|max:100',
            'diskon' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barangs,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.harga_satuan' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Generate No PO: PO-YYYYMMDD-XXXX
            $datePrefix = 'PO-' . date('Ymd');
            $lastPO = PurchaseOrder::where('no_po', 'like', "{$datePrefix}%")
                ->orderBy('no_po', 'desc')
                ->first();

            $nextNumber = 1;
            if ($lastPO) {
                $lastNum = (int) substr($lastPO->no_po, -4);
                $nextNumber = $lastNum + 1;
            }
            $noPO = $datePrefix . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            // Hitung Subtotal Item
            $calculatedSubtotal = 0;
            $itemsData = [];

            foreach ($request->items as $item) {
                $barang = Barang::find($item['barang_id']);
                $itemSubtotal = (float)$item['jumlah'] * (float)$item['harga_satuan'];
                $calculatedSubtotal += $itemSubtotal;

                $itemsData[] = [
                    'barang_id' => $item['barang_id'],
                    'jumlah' => $item['jumlah'],
                    'harga_satuan' => $item['harga_satuan'],
                    'subtotal' => $itemSubtotal,
                    'snapshot_nama_barang' => $barang->nama_barang,
                ];
            }

            // Hitung Pajak & Total
            $diskon = (float)($request->diskon ?? 0);
            $subtotalAfterDiskon = max(0, $calculatedSubtotal - $diskon);
            $pajakPersen = (float)($request->pajak_persen ?? 11);
            $pajakNominal = ($subtotalAfterDiskon * $pajakPersen) / 100;
            $totalHarga = $subtotalAfterDiskon + $pajakNominal;

            $po = PurchaseOrder::create([
                'pp_id' => $request->pp_id,
                'supplier_id' => $request->supplier_id,
                'user_id' => auth()->id(),
                'no_po' => $noPO,
                'status' => 'dikirim',
                'tanggal' => now(),
                'subtotal' => $calculatedSubtotal,
                'diskon' => $diskon,
                'pajak_persen' => $pajakPersen,
                'pajak_nominal' => $pajakNominal,
                'total_harga' => $totalHarga,
                'syarat_pembayaran' => $request->syarat_pembayaran,
                'catatan' => $request->catatan,
            ]);

            foreach ($itemsData as $itemDetail) {
                $itemDetail['po_id'] = $po->id;
                PurchaseOrderDetail::create($itemDetail);
            }

            // If linked to a PR, update the PR status to di_po
            if ($request->filled('pp_id')) {
                PermintaanPembelian::where('id', $request->pp_id)->update(['status' => 'di_po']);
            }

            DB::commit();
            return redirect()->route('purchase-orders.index')->with('success', 'Purchase Order (PO) berhasil dibuat: ' . $noPO);

        } catch (Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('PO Store Error: ' . $e->getMessage());
            $errorMsg = str_contains($e->getMessage(), 'SQLSTATE')
                ? 'Terjadi kendala pada sistem database saat menyimpan data.'
                : $e->getMessage();
            return back()->withInput()->with('error', 'Gagal membuat PO: ' . $errorMsg);
        }
    }

    public function show(PurchaseOrder $po)
    {
        $po->load(['supplier', 'user', 'permintaanPembelian', 'details.barang', 'barangMasuks.barang']);
        return view('purchase_orders.show', compact('po'));
    }

    public function print(PurchaseOrder $po)
    {
        $po->load(['supplier', 'user', 'permintaanPembelian', 'details.barang']);
        return view('purchase_orders.print', compact('po'));
    }

    public function cancel(PurchaseOrder $po)
    {
        if (!in_array($po->status, ['dikirim', 'diterima_sebagian'])) {
            return back()->with('error', 'PO ini tidak bisa dibatalkan.');
        }

        try {
            DB::beginTransaction();

            $po->update(['status' => 'dibatalkan']);

            if ($po->pp_id) {
                $po->permintaanPembelian()->update(['status' => 'pending']);
            }

            DB::commit();
            return redirect()->route('purchase-orders.show', $po->id)->with('success', 'PO berhasil dibatalkan.');

        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('purchase-orders.show', $po->id)->with('error', 'Gagal membatalkan PO: ' . $e->getMessage());
        }
    }
}
