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
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = PermintaanBarang::with(['user', 'details.barang', 'qcInspection']);

        // Staf Produksi can only see their own requests
        if ($user->role === 'produksi') {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_permintaan', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $counts = [
            'semua' => (clone $query)->count(),
            'pending' => (clone $query)->where('status', 'pending')->count(),
            'selesai' => (clone $query)->where('status', 'selesai')->count(),
            'ditolak' => (clone $query)->where('status', 'ditolak')->count(),
        ];

        if ($request->filled('status') && in_array($request->status, ['pending', 'selesai', 'ditolak'])) {
            $query->where('status', $request->status);
        }

        $permintaans = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('permintaan_barang.index', compact('permintaans', 'counts'));
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
        $permintaan->load([
            'user',
            'details.barang.unit',
            'details.barang.location.warehouse',
            'details.barang.batches' => function ($q) {
                $q->where('stok', '>', 0)
                  ->where('status', 'active')
                  ->orderByRaw('COALESCE(tanggal_kadaluarsa, "9999-12-31") ASC')
                  ->orderBy('tanggal_masuk', 'asc')
                  ->orderBy('id', 'asc');
            },
            'barangKeluars.barang',
            'barangKeluars.inventoryTransactions.batch',
            'barangKeluars.user',
            'qcInspection.user',
        ]);

        return view('permintaan_barang.show', compact('permintaan'));
    }

    public function print(PermintaanBarang $permintaan)
    {
        $permintaan->load([
            'user',
            'details.barang.unit',
            'details.barang.location.warehouse',
            'details.barang.batches' => function ($q) {
                $q->where('stok', '>', 0)
                  ->where('status', 'active')
                  ->orderByRaw('COALESCE(tanggal_kadaluarsa, "9999-12-31") ASC')
                  ->orderBy('tanggal_masuk', 'asc')
                  ->orderBy('id', 'asc');
            },
            'barangKeluars.barang',
            'barangKeluars.inventoryTransactions.batch',
            'barangKeluars.user',
            'qcInspection.user',
        ]);

        return view('permintaan_barang.print', compact('permintaan'));
    }

    public function verifyQc(Request $request, PermintaanBarang $permintaan)
    {
        $request->validate([
            'berat_riil_diterima' => 'required|numeric|min:0.01',
            'suhu' => 'required|numeric',
            'status_qc' => 'required|in:pass,fail',
            'tindakan_koreksi' => 'nullable|string|max:255',
            'catatan' => 'nullable|string|max:255',
        ]);

        $beratKeluar = (float) $permintaan->barangKeluars->sum('jumlah');
        $beratRiil = (float) $request->berat_riil_diterima;
        $susutKg = max(0, $beratKeluar - $beratRiil);
        $susutPersen = $beratKeluar > 0 ? ($susutKg / $beratKeluar) * 100 : 0;

        // Buat atau perbarui catatan QC Defrost
        \App\Models\QcInspection::updateOrCreate(
            [
                'reference_type' => PermintaanBarang::class,
                'reference_id' => $permintaan->id,
            ],
            [
                'user_id' => auth()->id(),
                'tipe_qc' => 'production_defrost',
                'nomor_dokumen' => 'FRM.JFD.01.10',
                'tanggal_cek' => now()->toDateString(),
                'jam_cek' => now()->format('H:i:s'),
                'kondisi_aroma' => $request->boolean('kondisi_aroma', true),
                'kondisi_tekstur' => $request->boolean('kondisi_tekstur', true),
                'kondisi_penampakan' => $request->boolean('kondisi_penampakan', true),
                'suhu' => $request->suhu,
                'berat_keluar_gudang' => $beratKeluar,
                'berat_riil_diterima' => $beratRiil,
                'susut_kg' => round($susutKg, 2),
                'susut_persen' => round($susutPersen, 2),
                'status_qc' => $request->status_qc,
                'tindakan_koreksi' => $request->tindakan_koreksi,
                'catatan' => $request->catatan,
            ]
        );

        $permintaan->update([
            'status_qc' => $request->status_qc,
        ]);

        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'QC_DEFROST_VERIFICATION',
            'module' => 'PRODUKSI',
            'record_id' => $permintaan->id,
            'new_values' => [
                'no_permintaan' => $permintaan->no_permintaan,
                'berat_keluar' => $beratKeluar,
                'berat_riil' => $beratRiil,
                'susut_kg' => round($susutKg, 2),
                'susut_persen' => round($susutPersen, 2),
                'status_qc' => $request->status_qc,
            ],
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('permintaan-barang.show', $permintaan->id)
            ->with('success', 'Verifikasi QC Defrost & susut berat (FRM.JFD.01.10) berhasil disimpan!');
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
