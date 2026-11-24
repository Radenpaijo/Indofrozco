<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\PurchaseOrder;
use App\Models\BarangMasuk;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class BarangMasukController extends Controller
{
    public function index()
    {
        $barangMasuks = BarangMasuk::with(['barang', 'user', 'purchaseOrder', 'location.warehouse', 'qcInspection'])
            ->orderBy('tanggal', 'desc')
            ->paginate(10);

        return view('barang_masuk.index', compact('barangMasuks'));
    }

    public function create(Request $request)
    {
        $barangs = Barang::with('location')->where('is_active', true)->get();
        $locations = Location::with('warehouse')->where('status', 'active')->get();
        
        // Fetch active POs for receipt linking
        $activePOs = PurchaseOrder::whereIn('status', ['dikirim', 'diterima_sebagian'])
            ->with(['supplier', 'details.barang.unit', 'details.barang.location'])
            ->get();

        $selectedPO = null;
        if ($request->filled('po_id')) {
            $selectedPO = PurchaseOrder::with(['supplier', 'details.barang.unit', 'details.barang.location'])->find($request->po_id);
            if ($selectedPO && !in_array($selectedPO->status, ['dikirim', 'diterima_sebagian'])) {
                return redirect()->route('barang-masuk.create')->with('error', 'Purchase Order ini sudah selesai atau dibatalkan.');
            }
        }

        return view('barang_masuk.create', compact('barangs', 'locations', 'activePOs', 'selectedPO'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'po_id' => 'nullable|exists:purchase_orders,id',
            'no_surat_jalan' => 'required|string|max:100',
            'tanggal' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barangs,id',
            'items.*.location_id' => 'nullable|exists:locations,id',
            'items.*.jumlah' => 'required|numeric|min:0.01',
            'items.*.jumlah_pack' => 'nullable|integer|min:1',
            'items.*.batch_number' => 'nullable|string|max:100',
            'items.*.tanggal_kadaluarsa' => 'nullable|date',
            'qc_suhu' => 'nullable|numeric',
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
                $barang = Barang::find($item['barang_id']);
                $targetLocationId = !empty($item['location_id']) ? $item['location_id'] : ($barang ? $barang->location_id : null);

                $barangMasuk = BarangMasuk::create([
                    'no_masuk' => $noMasuk,
                    'po_id' => $request->po_id,
                    'barang_id' => $item['barang_id'],
                    'location_id' => $targetLocationId,
                    'jumlah' => $item['jumlah'],
                    'jumlah_pack' => !empty($item['jumlah_pack']) ? $item['jumlah_pack'] : null,
                    'batch_number' => !empty($item['batch_number']) ? $item['batch_number'] : null,
                    'tanggal_kadaluarsa' => !empty($item['tanggal_kadaluarsa']) ? $item['tanggal_kadaluarsa'] : null,
                    'no_surat_jalan' => $request->no_surat_jalan,
                    'user_id' => auth()->id(),
                    'tanggal' => $request->tanggal,
                ]);

                // Simpan QC Inbound (FRM.JFD.01.01) jika diaktifkan
                if ($request->has('qc_aktif')) {
                    \App\Models\QcInspection::create([
                        'reference_type' => BarangMasuk::class,
                        'reference_id' => $barangMasuk->id,
                        'user_id' => auth()->id(),
                        'tipe_qc' => 'inbound_receiving',
                        'nomor_dokumen' => 'FRM.JFD.01.01',
                        'tanggal_cek' => $request->tanggal,
                        'jam_cek' => now()->format('H:i:s'),
                        'kebersihan_kendaraan' => $request->boolean('qc_kebersihan_kendaraan', true),
                        'kemasan_utuh' => $request->boolean('qc_kemasan_utuh', true),
                        'expire_date_aman' => $request->boolean('qc_expire_date_aman', true),
                        'bebas_benda_asing' => $request->boolean('qc_bebas_benda_asing', true),
                        'kondisi_aroma' => $request->boolean('qc_kondisi_aroma', true),
                        'kondisi_tekstur' => $request->boolean('qc_kondisi_tekstur', true),
                        'kondisi_penampakan' => $request->boolean('qc_kondisi_penampakan', true),
                        'suhu' => $request->filled('qc_suhu') ? $request->qc_suhu : null,
                        'status_qc' => $request->input('qc_status', 'pass'),
                        'tindakan_koreksi' => $request->input('qc_tindakan_koreksi'),
                        'catatan' => $request->input('qc_catatan'),
                    ]);
                }
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
            \Illuminate\Support\Facades\Log::error('Gagal mencatat penerimaan barang: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return back()->withInput()->with('error', 'Gagal mencatat penerimaan barang: ' . $e->getMessage());
        }
    }
}
