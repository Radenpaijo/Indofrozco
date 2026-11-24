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
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\StockOpname;
use App\Models\QcInspection;
use App\Models\InventoryBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Core KPIs
        $totalItems = Barang::count();
        $totalStockQty = Barang::sum('stok');
        $lowStockCount = Barang::where('stok', '<=', DB::raw('stok_minimum'))->count();
        
        $totalWarehouses = Warehouse::where('status', 'active')->count();
        $totalLocations = Location::where('status', 'active')->count();
        $totalSuppliers = Supplier::count();

        // Monthly & 30-Day Inbound & Outbound Totals
        $monthlyIncomingQty = BarangMasuk::whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->sum('jumlah');

        $monthlyOutgoingQty = BarangKeluar::whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->sum('jumlah');

        $since30Days = now()->subDays(30)->startOfDay();
        $incoming30dQty = BarangMasuk::where('tanggal', '>=', $since30Days)->sum('jumlah');
        $incoming30dCount = BarangMasuk::where('tanggal', '>=', $since30Days)->count();
        $outgoing30dQty = BarangKeluar::where('tanggal', '>=', $since30Days)->sum('jumlah');
        $outgoing30dCount = BarangKeluar::where('tanggal', '>=', $since30Days)->count();

        // 2. Financial & PO Pipeline Value
        $activePOValue = PurchaseOrder::whereIn('status', ['dikirim', 'diterima_sebagian'])->sum('total_harga');

        // 3. Operational Queues
        $pendingProdRequests = PermintaanBarang::where('status', 'pending')->count();
        $pendingPRs = PermintaanPembelian::where('status', 'pending')->count();
        $activePOs = PurchaseOrder::whereIn('status', ['dikirim', 'diterima_sebagian'])->count();
        $totalOpnames = StockOpname::count();
        $lastOpname = StockOpname::orderBy('created_at', 'desc')->first();

        // 4. Stock Alerts & Overview
        $lowStockItems = Barang::with(['category', 'unit', 'location'])
            ->where('stok', '<=', DB::raw('stok_minimum'))
            ->orderBy('stok', 'asc')
            ->take(6)
            ->get();

        $stockOverviewItems = Barang::with(['category', 'unit', 'location.warehouse'])
            ->orderByRaw('CASE WHEN stok <= stok_minimum THEN 0 ELSE 1 END ASC')
            ->orderBy('stok', 'asc')
            ->take(6)
            ->get();

        $allLimitedItems = Barang::where('batas_umur_hari', '>', 0)->get();
        $expiringItems = collect();
        foreach ($allLimitedItems as $barang) {
            $batches = $barang->getExpiringBatches();
            if ($batches->isNotEmpty()) {
                $expiringItems->push([
                    'barang' => $barang,
                    'batches' => $batches,
                    'max_days_left' => $batches->min('days_left'),
                    'status' => $batches->contains('status', 'expired') ? 'expired' : 'warning',
                ]);
            }
        }
        $expiringItems = $expiringItems->sortBy('max_days_left')->take(6)->values();

        // 5. 6-Month Inbound vs Outbound Trend Data for Chart.js
        $trendMonths = [];
        $trendInbound = [];
        $trendOutbound = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $trendMonths[] = $date->format('M Y');

            $inQty = (int) BarangMasuk::whereYear('tanggal', $date->year)
                ->whereMonth('tanggal', $date->month)
                ->sum('jumlah');

            $outQty = (int) BarangKeluar::whereYear('tanggal', $date->year)
                ->whereMonth('tanggal', $date->month)
                ->sum('jumlah');

            $trendInbound[] = $inQty;
            $trendOutbound[] = $outQty;
        }

        // 6. Category Stock Breakdown for Chart.js
        $categoryBreakdown = Category::withSum('barangs as total_stok', 'stok')
            ->withCount('barangs')
            ->get()
            ->map(function ($cat) {
                return [
                    'name' => $cat->name,
                    'stok' => (int) ($cat->total_stok ?? 0),
                    'count' => $cat->barangs_count,
                ];
            });

        // 7. Top 5 Fast-Moving Items (Highest Outbound in last 30 days)
        $fastMovingItems = BarangKeluar::select('barang_id', DB::raw('SUM(jumlah) as total_keluar'))
            ->where('tanggal', '>=', now()->subDays(30))
            ->groupBy('barang_id')
            ->orderByDesc('total_keluar')
            ->with('barang')
            ->take(5)
            ->get();

        // 8. Recent Activities & Audit Logs
        $recentIncoming = BarangMasuk::with(['barang', 'user', 'purchaseOrder.supplier'])
            ->orderBy('tanggal', 'desc')
            ->take(6)
            ->get();

        $recentOutgoing = BarangKeluar::with(['barang', 'user', 'permintaanBarang'])
            ->orderBy('tanggal', 'desc')
            ->take(6)
            ->get();

        $recentAudits = AuditLog::with('user')
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        // 9. Warehouse & Rack Capacity Summary
        $warehousesSummary = Warehouse::withCount('locations')
            ->with(['locations'])
            ->get();

        $locationsSummary = Location::with(['warehouse', 'barangs'])
            ->where('status', 'active')
            ->orderBy('code')
            ->get()
            ->map(function ($loc) {
                $maxCap = (float) ($loc->capacity ?? 0);
                $usedCap = (float) $loc->hitungKapasitasTerpakai();
                $percent = $maxCap > 0 ? min(100, round(($usedCap / $maxCap) * 100)) : null;

                $loc->computed_used = $usedCap;
                $loc->computed_max = $maxCap;
                $loc->computed_percent = $percent;
                return $loc;
            })
            ->sortByDesc(function ($loc) {
                return $loc->computed_percent ?? -1;
            })
            ->values();

        // Aktivitas Hari Ini
        $todayIncomingCount = BarangMasuk::whereDate('tanggal', today())->count();
        $todayOutgoingCount = BarangKeluar::whereDate('tanggal', today())->count();

        // 10. Indikator QC (Inbound & Defrost Susut)
        $qcInboundThisMonth = QcInspection::where('tipe_qc', 'inbound_receiving')
            ->whereMonth('tanggal_cek', now()->month)
            ->whereYear('tanggal_cek', now()->year);
        $qcInboundPass = (clone $qcInboundThisMonth)->where('status_qc', 'pass')->count();
        $qcInboundFail = (clone $qcInboundThisMonth)->where('status_qc', 'fail')->count();
        $qcInboundTotal = $qcInboundPass + $qcInboundFail;

        $qcDefrostThisMonth = QcInspection::where('tipe_qc', 'production_defrost')
            ->whereMonth('tanggal_cek', now()->month)
            ->whereYear('tanggal_cek', now()->year);
        $qcDefrostPass = (clone $qcDefrostThisMonth)->where('status_qc', 'pass')->count();
        $qcDefrostFail = (clone $qcDefrostThisMonth)->where('status_qc', 'fail')->count();
        $totalSusutKg = (clone $qcDefrostThisMonth)->sum('susut_kg');
        $avgSusutPersen = round((float)((clone $qcDefrostThisMonth)->avg('susut_persen') ?: 0), 2);
        
        $pendingDefrostCount = PermintaanBarang::where('status', 'selesai')
            ->whereDoesntHave('qcInspection')
            ->count();

        // 11. Prioritas Pengeluaran FEFO (Karung / Batch dengan Expired Terdekat)
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
                $expDate = \Carbon\Carbon::parse($batch->tanggal_kadaluarsa);
                $daysLeft = (int) now()->startOfDay()->diffInDays($expDate->startOfDay(), false);
                $batch->days_left = $daysLeft;
                $batch->sisa_stok = (float) ($batch->stok_sekarang > 0 ? $batch->stok_sekarang : $batch->stok);
                $batch->urgency = $daysLeft < 0 ? 'expired' : ($daysLeft <= 30 ? 'critical' : ($daysLeft <= 90 ? 'warning' : 'safe'));
                return $batch;
            });

        // 12. Ringkasan Keuangan & Selisih Persediaan (Dibayar vs Dikeluarkan vs Rusak/Susut)
        [$avgPriceByBarang, $globalAvgPrice] = $this->getAverageUnitPrices();
        $financeAll = $this->buildFinancialSummary($avgPriceByBarang, $globalAvgPrice, null);
        $financeMonth = $this->buildFinancialSummary($avgPriceByBarang, $globalAvgPrice, now()->startOfMonth());

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
            'monthlyIncomingQty',
            'monthlyOutgoingQty',
            'incoming30dQty',
            'incoming30dCount',
            'outgoing30dQty',
            'outgoing30dCount',
            'todayIncomingCount',
            'todayOutgoingCount',
            'activePOValue',
            'pendingProdRequests',
            'pendingPRs',
            'activePOs',
            'totalOpnames',
            'lastOpname',
            'lowStockItems',
            'stockOverviewItems',
            'expiringItems',
            'trendMonths',
            'trendInbound',
            'trendOutbound',
            'categoryBreakdown',
            'fastMovingItems',
            'recentIncoming',
            'recentOutgoing',
            'recentAudits',
            'warehousesSummary',
            'locationsSummary',
            'qcInboundPass',
            'qcInboundFail',
            'qcInboundTotal',
            'qcDefrostPass',
            'qcDefrostFail',
            'totalSusutKg',
            'avgSusutPersen',
            'pendingDefrostCount',
            'fefoExpiringBatches',
            'financeAll',
            'financeMonth',
            'financeCurrentStockValue'
        ));
    }

    /**
     * Hitung harga satuan rata-rata tertimbang per barang dari Purchase Order (non-batal) dan Barang Masuk.
     */
    private function getAverageUnitPrices(): array
    {
        $avgPriceByBarang = [];
        $totalWeightedQty = 0.0;
        $totalWeightedVal = 0.0;

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

        // Fallback dari barang_masuk jika barang belum ada di PO
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
     * Ringkasan keuangan: total dibayar (PO/masuk), nilai dikeluarkan, kerugian rusak/opname/susut, & selisih bersih.
     */
    private function buildFinancialSummary(array $avgPriceByBarang, float $globalAvgPrice, ?\Carbon\Carbon $startDate = null): array
    {
        // 1. Total Dibayar (PO non-batal + Barang Masuk manual yang memiliki total_harga)
        $poQuery = PurchaseOrder::where('status', '!=', 'dibatalkan');
        if ($startDate) {
            $poQuery->where(function ($q) use ($startDate) {
                $q->where('tanggal', '>=', $startDate->toDateString())
                  ->orWhere(function ($q2) use ($startDate) {
                      $q2->whereNull('tanggal')->where('created_at', '>=', $startDate);
                  });
            });
        }
        $poCount = (clone $poQuery)->count();
        $totalPaidPo = (float) $poQuery->sum('total_harga');

        $manualInQuery = DB::table('barang_masuk')
            ->whereNull('po_id')
            ->where('total_harga', '>', 0);
        if ($startDate) {
            $manualInQuery->where('tanggal', '>=', $startDate);
        }
        $totalPaidManual = (float) $manualInQuery->sum('total_harga');
        $totalPaid = $totalPaidPo + $totalPaidManual;

        // 2. Nilai Barang Dikeluarkan
        $outQuery = DB::table('barang_keluar')
            ->select('barang_id', DB::raw('SUM(jumlah) as qty'))
            ->groupBy('barang_id');
        if ($startDate) {
            $outQuery->where('tanggal', '>=', $startDate);
        }
        $issuedValue = 0.0;
        $issuedQty = 0.0;
        foreach ($outQuery->get() as $row) {
            $qty = (float) $row->qty;
            $issuedQty += $qty;
            $issuedValue += $qty * ($avgPriceByBarang[$row->barang_id] ?? 0.0);
        }

        // 3a. Kerugian Rusak / Hilang dari Stock Adjustment (negatif)
        $adjQuery = DB::table('stock_adjustment_details as d')
            ->join('stock_adjustments as sa', 'sa.id', '=', 'd.stock_adjustment_id')
            ->where('d.stok_adjustment', '<', 0)
            ->select('d.barang_id', DB::raw('SUM(ABS(d.stok_adjustment)) as qty'))
            ->groupBy('d.barang_id');
        if ($startDate) {
            $adjQuery->where('sa.tanggal', '>=', $startDate->toDateString());
        }
        $lossAdjValue = 0.0;
        $lossAdjQty = 0.0;
        foreach ($adjQuery->get() as $row) {
            $qty = (float) $row->qty;
            $lossAdjQty += $qty;
            $lossAdjValue += $qty * ($avgPriceByBarang[$row->barang_id] ?? 0.0);
        }

        // 3b. Kerugian Selisih Minus dari Stock Opname
        $opnameQuery = DB::table('stock_opname_details as d')
            ->join('stock_opnames as so', 'so.id', '=', 'd.stock_opname_id')
            ->where('d.selisih', '<', 0)
            ->select('d.barang_id', DB::raw('SUM(ABS(d.selisih)) as qty'))
            ->groupBy('d.barang_id');
        if ($startDate) {
            $opnameQuery->where('so.tanggal', '>=', $startDate->toDateString());
        }
        $lossOpnameValue = 0.0;
        $lossOpnameQty = 0.0;
        foreach ($opnameQuery->get() as $row) {
            $qty = (float) $row->qty;
            $lossOpnameQty += $qty;
            $lossOpnameValue += $qty * ($avgPriceByBarang[$row->barang_id] ?? 0.0);
        }

        // 3c. Kerugian Susut Defrost dari QC Inspection
        $qcQuery = QcInspection::with('reference')
            ->where('tipe_qc', 'production_defrost')
            ->where('susut_kg', '>', 0);
        if ($startDate) {
            $qcQuery->where('tanggal_cek', '>=', $startDate->toDateString());
        }
        $lossSusutValue = 0.0;
        $lossSusutQty = 0.0;
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
        $totalLossQty = $lossAdjQty + $lossOpnameQty + $lossSusutQty;
        $netVariance = $totalPaid - $issuedValue - $totalLossValue;
        $lossPercent = $totalPaid > 0 ? round(($totalLossValue / $totalPaid) * 100, 2) : 0.0;

        return [
            'po_count' => $poCount,
            'total_paid' => $totalPaid,
            'issued_value' => $issuedValue,
            'issued_qty' => $issuedQty,
            'loss_adj_value' => $lossAdjValue,
            'loss_adj_qty' => $lossAdjQty,
            'loss_opname_value' => $lossOpnameValue,
            'loss_opname_qty' => $lossOpnameQty,
            'loss_susut_value' => $lossSusutValue,
            'loss_susut_qty' => $lossSusutQty,
            'total_loss_value' => $totalLossValue,
            'total_loss_qty' => $totalLossQty,
            'loss_percent' => $lossPercent,
            'net_variance' => $netVariance,
        ];
    }
}

