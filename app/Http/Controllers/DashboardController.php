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

        // Monthly Inbound & Outbound Totals
        $monthlyIncomingQty = BarangMasuk::whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->sum('jumlah');

        $monthlyOutgoingQty = BarangKeluar::whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->sum('jumlah');

        // 2. Financial & PO Pipeline Value
        $activePOValue = PurchaseOrder::whereIn('status', ['dikirim', 'diterima_sebagian'])->sum('total_harga');

        // 3. Operational Queues
        $pendingProdRequests = PermintaanBarang::where('status', 'pending')->count();
        $pendingPRs = PermintaanPembelian::where('status', 'pending')->count();
        $activePOs = PurchaseOrder::whereIn('status', ['dikirim', 'diterima_sebagian'])->count();
        $totalOpnames = StockOpname::count();
        $lastOpname = StockOpname::orderBy('created_at', 'desc')->first();

        // 4. Stock Alerts & Expiry Warnings
        $lowStockItems = Barang::with(['category', 'unit', 'location'])
            ->where('stok', '<=', DB::raw('stok_minimum'))
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

        $recentOutgoing = BarangKeluar::with(['barang', 'user'])
            ->orderBy('tanggal', 'desc')
            ->take(6)
            ->get();

        $recentAudits = AuditLog::with('user')
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        // 9. Warehouse Storage Summary
        $warehousesSummary = Warehouse::withCount('locations')
            ->with(['locations'])
            ->get();

        return view('dashboard', compact(
            'totalItems',
            'totalStockQty',
            'lowStockCount',
            'totalWarehouses',
            'totalLocations',
            'totalSuppliers',
            'monthlyIncomingQty',
            'monthlyOutgoingQty',
            'activePOValue',
            'pendingProdRequests',
            'pendingPRs',
            'activePOs',
            'totalOpnames',
            'lastOpname',
            'lowStockItems',
            'expiringItems',
            'trendMonths',
            'trendInbound',
            'trendOutbound',
            'categoryBreakdown',
            'fastMovingItems',
            'recentIncoming',
            'recentOutgoing',
            'recentAudits',
            'warehousesSummary'
        ));
    }
}

