<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use App\Models\PurchaseOrder;
use App\Models\PermintaanBarang;
use App\Models\PermintaanPembelian;
use App\Models\Warehouse;
use App\Models\Location;
use App\Models\Supplier;
use App\Models\StockOpname;
use App\Models\QcInspection;
use App\Models\InventoryBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * DashboardController
 * 
 * Pengendali utama tampilan Dasbor Operasional WMS INDOFROZ.
 * Mengagregasi metrik KPI stok, antrean dokumen, kapasitas rak gudang,
 * prioritas pengeluaran FEFO, tren mutasi barang, serta rekonsiliasi keuangan.
 */
class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dasbor operasional terpadu.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // ---------------------------------------------------------------------
        // 1. KPI UTAMA PERSSEDIAAN (Ringkasan Fisik)
        // ---------------------------------------------------------------------
        $totalItems     = Barang::count();
        $totalStockQty  = Barang::sum('stok');
        $lowStockCount  = Barang::where('stok', '<=', DB::raw('stok_minimum'))->count();
        
        $totalWarehouses = Warehouse::where('status', 'active')->count();
        $totalLocations  = Location::where('status', 'active')->count();
        $totalSuppliers  = Supplier::count();

        // Volume Mutasi 30 Hari Terakhir
        $since30Days      = now()->subDays(30)->startOfDay();
        $incoming30dQty   = BarangMasuk::where('tanggal', '>=', $since30Days)->sum('jumlah');
        $incoming30dCount = BarangMasuk::where('tanggal', '>=', $since30Days)->count();
        $outgoing30dQty   = BarangKeluar::where('tanggal', '>=', $since30Days)->sum('jumlah');
        $outgoing30dCount = BarangKeluar::where('tanggal', '>=', $since30Days)->count();

        // ---------------------------------------------------------------------
        // 2. FINANCIAL PIPELINE & ANTREAN DOKUMEN OPERASIONAL
        // ---------------------------------------------------------------------
        $activePOValue       = PurchaseOrder::whereIn('status', ['dikirim', 'diterima_sebagian'])->sum('total_harga');
        $activePOs           = PurchaseOrder::whereIn('status', ['dikirim', 'diterima_sebagian'])->count();
        $pendingPRs          = PermintaanPembelian::where('status', 'pending')->count();
        $pendingProdRequests = PermintaanBarang::where('status', 'pending')->count();
        $pendingDefrostCount = PermintaanBarang::where('status', 'selesai')
            ->whereDoesntHave('qcInspection')
            ->count();
        $lastOpname          = StockOpname::orderBy('created_at', 'desc')->first();

        // Indikator Lolos QC Penerimaan Inbound (Bulan Berjalan)
        $qcInboundThisMonth = QcInspection::where('tipe_qc', 'inbound_receiving')
            ->whereMonth('tanggal_cek', now()->month)
            ->whereYear('tanggal_cek', now()->year);
        $qcInboundPass      = (clone $qcInboundThisMonth)->where('status_qc', 'pass')->count();
        $qcInboundTotal     = (clone $qcInboundThisMonth)->count();

        // ---------------------------------------------------------------------
        // 3. OVERVIEW KONDISI STOK BARANG (Prioritas yang Menipis / Habis)
        // ---------------------------------------------------------------------
        $stockOverviewItems = Barang::with(['category', 'unit', 'location.warehouse'])
            ->orderByRaw('CASE WHEN stok <= stok_minimum THEN 0 ELSE 1 END ASC')
            ->orderBy('stok', 'asc')
            ->take(6)
            ->get();

        // ---------------------------------------------------------------------
        // 4. KAPASITAS RAK & GUDANG (Persentase Utilisasi Ruang)
        // ---------------------------------------------------------------------
        $locationsSummary = Location::with(['warehouse', 'barangs'])
            ->where('status', 'active')
            ->orderBy('code')
            ->get()
            ->map(function ($loc) {
                $maxCap  = (float) ($loc->capacity ?? 0);
                $usedCap = (float) $loc->hitungKapasitasTerpakai();
                $percent = $maxCap > 0 ? min(100, round(($usedCap / $maxCap) * 100)) : null;

                $loc->computed_used    = $usedCap;
                $loc->computed_max     = $maxCap;
                $loc->computed_percent = $percent;
                return $loc;
            })
            ->sortByDesc(function ($loc) {
                return $loc->computed_percent ?? -1;
            })
            ->values();

        // ---------------------------------------------------------------------
        // 5. TREN PERGERAKAN BARANG 6 BULAN TERAKHIR (Data untuk Chart.js)
        // ---------------------------------------------------------------------
        $trendMonths   = [];
        $trendInbound  = [];
        $trendOutbound = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $trendMonths[] = $date->format('M Y');

            $trendInbound[] = (int) BarangMasuk::whereYear('tanggal', $date->year)
                ->whereMonth('tanggal', $date->month)
                ->sum('jumlah');

            $trendOutbound[] = (int) BarangKeluar::whereYear('tanggal', $date->year)
                ->whereMonth('tanggal', $date->month)
                ->sum('jumlah');
        }

        // ---------------------------------------------------------------------
        // 6. PRIORITAS PENGELUARAN FEFO (Batch Expired Terdekat)
        // ---------------------------------------------------------------------
        $fefoExpiringBatches = InventoryBatch::with(['barang.location.warehouse', 'location'])
            ->where('status', 'active')
            ->where(function ($q) {
                $q->where('stok_sekarang', '>', 0)->orWhere('stok', '>', 0);
            })
            ->whereNotNull('tanggal_kadaluarsa')
            ->orderBy('tanggal_kadaluarsa', 'asc')
            ->take(6)
            ->get()
            ->map(function ($batch) {
                $expDate          = \Carbon\Carbon::parse($batch->tanggal_kadaluarsa);
                $daysLeft         = (int) now()->startOfDay()->diffInDays($expDate->startOfDay(), false);
                $batch->days_left = $daysLeft;
                $batch->sisa_stok = (float) ($batch->stok_sekarang > 0 ? $batch->stok_sekarang : $batch->stok);
                $batch->urgency   = $daysLeft < 0 ? 'expired' : ($daysLeft <= 30 ? 'critical' : ($daysLeft <= 90 ? 'warning' : 'safe'));
                return $batch;
            });

        // ---------------------------------------------------------------------
        // 7. TRANSAKSI TERAKHIR (Barang Masuk & Keluar)
        // ---------------------------------------------------------------------
        $recentIncoming = BarangMasuk::with(['barang', 'user', 'purchaseOrder.supplier'])
            ->orderBy('tanggal', 'desc')
            ->take(5)
            ->get();

        $recentOutgoing = BarangKeluar::with(['barang', 'user', 'permintaanBarang'])
            ->orderBy('tanggal', 'desc')
            ->take(5)
            ->get();

        // ---------------------------------------------------------------------
        // 8. REKONSILIASI KEUANGAN & SELISIH NILAI PERSEDIAAN
        // ---------------------------------------------------------------------
        [$avgPriceByBarang, $globalAvgPrice] = $this->getAverageUnitPrices();
        $financeAll   = $this->buildFinancialSummary($avgPriceByBarang, $globalAvgPrice, null);
        $financeMonth = $this->buildFinancialSummary($avgPriceByBarang, $globalAvgPrice, now()->startOfMonth());

        // Valuasi total nilai stok fisik saat ini
        $financeCurrentStockValue = 0.0;
        foreach (Barang::select('id', 'stok')->where('stok', '>', 0)->get() as $b) {
            $financeCurrentStockValue += (float) $b->stok * ($avgPriceByBarang[$b->id] ?? 0.0);
        }

        return view('dashboard', compact(
            'totalItems',
            'totalStockQty',
            'lowStockCount',
            'totalWarehouses',
            'totalLocations',
            'totalSuppliers',
            'incoming30dQty',
            'incoming30dCount',
            'outgoing30dQty',
            'outgoing30dCount',
            'activePOValue',
            'pendingProdRequests',
            'pendingDefrostCount',
            'pendingPRs',
            'activePOs',
            'lastOpname',
            'qcInboundPass',
            'qcInboundTotal',
            'stockOverviewItems',
            'locationsSummary',
            'trendMonths',
            'trendInbound',
            'trendOutbound',
            'fefoExpiringBatches',
            'recentIncoming',
            'recentOutgoing',
            'financeAll',
            'financeMonth',
            'financeCurrentStockValue'
        ));
    }

    /**
     * Hitung harga satuan rata-rata tertimbang per barang dari Purchase Order (non-batal) dan Barang Masuk.
     *
     * @return array{0: array<int, float>, 1: float} [Daftar Harga Satuan per Barang ID, Harga Rata-rata Global]
     */
    private function getAverageUnitPrices(): array
    {
        $avgPriceByBarang = [];
        $totalWeightedQty = 0.0;
        $totalWeightedVal = 0.0;

        // Ambil data harga dari detail Purchase Order yang sah
        $poRows = DB::table('purchase_order_detail as d')
            ->join('purchase_orders as po', 'po.id', '=', 'd.po_id')
            ->where('po.status', '!=', 'dibatalkan')
            ->select(
                'd.barang_id',
                DB::raw('SUM(d.jumlah) as total_qty'),
                DB::raw('SUM(CASE WHEN d.subtotal > 0 THEN d.subtotal ELSE d.jumlah * d.harga_satuan END) as total_val')
            )
            ->groupBy('d.barang_id')
            ->get();

        foreach ($poRows as $row) {
            $qty = (float) $row->total_qty;
            $val = (float) $row->total_val;
            if ($qty > 0 && $val > 0) {
                $avgPriceByBarang[$row->barang_id] = $val / $qty;
                $totalWeightedQty += $qty;
                $totalWeightedVal += $val;
            }
        }

        // Fallback dari barang_masuk jika barang belum pernah dibeli lewat PO formal
        $bmRows = DB::table('barang_masuk')
            ->where('harga_satuan', '>', 0)
            ->select(
                'barang_id',
                DB::raw('SUM(jumlah) as total_qty'),
                DB::raw('SUM(CASE WHEN total_harga > 0 THEN total_harga ELSE jumlah * harga_satuan END) as total_val')
            )
            ->groupBy('barang_id')
            ->get();

        foreach ($bmRows as $row) {
            if (!isset($avgPriceByBarang[$row->barang_id])) {
                $qty = (float) $row->total_qty;
                $val = (float) $row->total_val;
                if ($qty > 0 && $val > 0) {
                    $avgPriceByBarang[$row->barang_id] = $val / $qty;
                    $totalWeightedQty += $qty;
                    $totalWeightedVal += $val;
                }
            }
        }

        $globalAvgPrice = $totalWeightedQty > 0 ? ($totalWeightedVal / $totalWeightedQty) : 0.0;

        return [$avgPriceByBarang, $globalAvgPrice];
    }

    /**
     * Menyusun ringkasan analisis keuangan: total modal dibayar, nilai barang keluar,
     * kerugian fisik (penyesuaian minus, opname minus, susut defrost), dan selisih bersih.
     *
     * @param array<int, float> $avgPriceByBarang Harga satuan rata-rata tertimbang
     * @param float $globalAvgPrice Harga satuan global fallback
     * @param \Carbon\Carbon|null $startDate Batas awal periode (null = semua waktu)
     * @return array
     */
    private function buildFinancialSummary(array $avgPriceByBarang, float $globalAvgPrice, ?\Carbon\Carbon $startDate = null): array
    {
        // 1. Total Dibayar (PO non-batal + Barang Masuk manual yang bernilai)
        $poQuery = PurchaseOrder::where('status', '!=', 'dibatalkan');
        if ($startDate) {
            $poQuery->where(function ($q) use ($startDate) {
                $q->where('tanggal', '>=', $startDate->toDateString())
                  ->orWhere(function ($q2) use ($startDate) {
                      $q2->whereNull('tanggal')->where('created_at', '>=', $startDate);
                  });
            });
        }
        $poCount     = (clone $poQuery)->count();
        $totalPaidPo = (float) $poQuery->sum('total_harga');

        $manualInQuery = DB::table('barang_masuk')
            ->whereNull('po_id')
            ->where('total_harga', '>', 0);
        if ($startDate) {
            $manualInQuery->where('tanggal', '>=', $startDate);
        }
        $totalPaidManual = (float) $manualInQuery->sum('total_harga');
        $totalPaid       = $totalPaidPo + $totalPaidManual;

        // 2. Nilai Barang Dikeluarkan (COGS / Nilai Konsumsi Produksi)
        $outQuery = DB::table('barang_keluar')
            ->select('barang_id', DB::raw('SUM(jumlah) as qty'))
            ->groupBy('barang_id');
        if ($startDate) {
            $outQuery->where('tanggal', '>=', $startDate);
        }
        $issuedValue = 0.0;
        $issuedQty   = 0.0;
        foreach ($outQuery->get() as $row) {
            $qty = (float) $row->qty;
            $issuedQty   += $qty;
            $issuedValue += $qty * ($avgPriceByBarang[$row->barang_id] ?? 0.0);
        }

        // 3a. Kerugian Rusak / Kadaluarsa dari Penyesuaian Stok Negatif
        $adjQuery = DB::table('stock_adjustment_details as d')
            ->join('stock_adjustments as sa', 'sa.id', '=', 'd.stock_adjustment_id')
            ->where('d.stok_adjustment', '<', 0)
            ->select('d.barang_id', DB::raw('SUM(ABS(d.stok_adjustment)) as qty'))
            ->groupBy('d.barang_id');
        if ($startDate) {
            $adjQuery->where('sa.tanggal', '>=', $startDate->toDateString());
        }
        $lossAdjValue = 0.0;
        $lossAdjQty   = 0.0;
        foreach ($adjQuery->get() as $row) {
            $qty = (float) $row->qty;
            $lossAdjQty   += $qty;
            $lossAdjValue += $qty * ($avgPriceByBarang[$row->barang_id] ?? 0.0);
        }

        // 3b. Kerugian Selisih Minus dari Stock Opname Fisik
        $opnameQuery = DB::table('stock_opname_details as d')
            ->join('stock_opnames as so', 'so.id', '=', 'd.stock_opname_id')
            ->where('d.selisih', '<', 0)
            ->select('d.barang_id', DB::raw('SUM(ABS(d.selisih)) as qty'))
            ->groupBy('d.barang_id');
        if ($startDate) {
            $opnameQuery->where('so.tanggal', '>=', $startDate->toDateString());
        }
        $lossOpnameValue = 0.0;
        $lossOpnameQty   = 0.0;
        foreach ($opnameQuery->get() as $row) {
            $qty = (float) $row->qty;
            $lossOpnameQty   += $qty;
            $lossOpnameValue += $qty * ($avgPriceByBarang[$row->barang_id] ?? 0.0);
        }

        // 3c. Kerugian Fisik Susut Bobot Defrost dari QC Inspection
        $qcQuery = QcInspection::with('reference')
            ->where('tipe_qc', 'production_defrost')
            ->where('susut_kg', '>', 0);
        if ($startDate) {
            $qcQuery->where('tanggal_cek', '>=', $startDate->toDateString());
        }
        $lossSusutValue = 0.0;
        $lossSusutQty   = 0.0;
        foreach ($qcQuery->get() as $qc) {
            $susut = (float) $qc->susut_kg;
            $lossSusutQty += $susut;

            $unitPrice = $globalAvgPrice;
            if ($qc->reference instanceof PermintaanBarang) {
                $qc->reference->loadMissing('details');
                $refQty = 0.0;
                $refVal = 0.0;
                foreach ($qc->reference->details as $det) {
                    $p = $avgPriceByBarang[$det->barang_id] ?? 0.0;
                    if ($p > 0) {
                        $refQty += (float) $det->jumlah;
                        $refVal += (float) $det->jumlah * $p;
                    }
                }
                if ($refQty > 0) {
                    $unitPrice = $refVal / $refQty;
                }
            }
            $lossSusutValue += $susut * $unitPrice;
        }

        $totalLossValue = $lossAdjValue + $lossOpnameValue + $lossSusutValue;
        $totalLossQty   = $lossAdjQty + $lossOpnameQty + $lossSusutQty;
        $netVariance    = $totalPaid - $issuedValue - $totalLossValue;
        $lossPercent    = $totalPaid > 0 ? round(($totalLossValue / $totalPaid) * 100, 2) : 0.0;

        return [
            'po_count'          => $poCount,
            'total_paid'        => $totalPaid,
            'issued_value'      => $issuedValue,
            'issued_qty'        => $issuedQty,
            'loss_adj_value'    => $lossAdjValue,
            'loss_adj_qty'      => $lossAdjQty,
            'loss_opname_value' => $lossOpnameValue,
            'loss_opname_qty'   => $lossOpnameQty,
            'loss_susut_value'  => $lossSusutValue,
            'loss_susut_qty'    => $lossSusutQty,
            'total_loss_value'  => $totalLossValue,
            'total_loss_qty'    => $totalLossQty,
            'loss_percent'      => $lossPercent,
            'net_variance'      => $netVariance,
        ];
    }
}
