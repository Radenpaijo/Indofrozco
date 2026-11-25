<?php

namespace App\Services;

use App\Models\Barang;
use App\Models\InventoryBatch;
use App\Models\PermintaanBarang;
use App\Models\PermintaanPembelian;
use App\Models\PurchaseOrder;
use Carbon\Carbon;

class NotificationService
{
    /**
     * Mengambil seluruh notifikasi operasional sistem secara real-time
     */
    public static function getNotifications(): array
    {
        if (!auth()->check()) {
            return [
                'items' => [],
                'count' => 0,
                'counts_by_type' => [
                    'warning' => 0,
                    'danger' => 0,
                    'info' => 0,
                    'success' => 0,
                ]
            ];
        }

        Carbon::setLocale('id');
        $notifications = [];

        // 1. Peringatan Stok Minimum (Low Stock Alert)
        try {
            $lowStocks = Barang::where('is_active', true)
                ->whereColumn('stok', '<=', 'stok_minimum')
                ->orderBy('stok', 'asc')
                ->take(6)
                ->get();

            foreach ($lowStocks as $item) {
                $isZero = (float)$item->stok <= 0;
                $notifications[] = [
                    'id' => 'low-stock-' . $item->id,
                    'type' => 'warning',
                    'category' => 'Stok Menipis',
                    'title' => $item->nama_barang,
                    'message' => "Sisa stok {$item->stok} {$item->satuan} • Batas minimum {$item->stok_minimum} {$item->satuan}",
                    'url' => route('barangs.show', $item->id),
                    'badge' => $isZero ? 'Habis (0)' : 'Perlu Restock',
                    'badge_class' => $isZero 
                        ? 'bg-rose-50 text-rose-700 border border-rose-200' 
                        : 'bg-amber-50 text-amber-800 border border-amber-200',
                    'icon' => 'warning',
                    'time' => 'Kritis',
                ];
            }
        } catch (\Throwable $e) {
            // Ignore if table not yet migrated
        }

        // 2. Batch Mendekati / Lewat Kedaluwarsa (Near Expiry < 30 hari atau sudah lewat)
        try {
            $nearExpiryBatches = InventoryBatch::with('barang')
                ->whereNotNull('tanggal_kadaluarsa')
                ->where(function($q) {
                    $q->where('stok', '>', 0)->orWhere('stok_sekarang', '>', 0);
                })
                ->where('tanggal_kadaluarsa', '<=', Carbon::now()->addDays(30))
                ->orderBy('tanggal_kadaluarsa', 'asc')
                ->take(6)
                ->get();

            foreach ($nearExpiryBatches as $batch) {
                $expDate = Carbon::parse($batch->tanggal_kadaluarsa)->locale('id');
                $isExpired = $expDate->isPast();
                $diffDays = (int) Carbon::now()->diffInDays($expDate, false);
                $namaBarang = $batch->barang->nama_barang ?? 'Barang Cold Storage';

                $notifications[] = [
                    'id' => 'batch-exp-' . $batch->id,
                    'type' => 'danger',
                    'category' => 'FEFO & Kedaluwarsa',
                    'title' => $namaBarang,
                    'message' => $isExpired 
                        ? "Batch {$batch->batch_number} telah kedaluwarsa ({$expDate->format('d M Y')})" 
                        : "Batch {$batch->batch_number} kedaluwarsa dlm {$diffDays} hari ({$expDate->format('d M Y')})",
                    'url' => route('barangs.show', $batch->barang_id),
                    'badge' => $isExpired ? 'Kedaluwarsa' : 'Near Exp',
                    'badge_class' => 'bg-rose-50 text-rose-700 border border-rose-200',
                    'icon' => 'clock',
                    'time' => $expDate->format('d/m/Y'),
                ];
            }
        } catch (\Throwable $e) {
            // Ignore
        }

        // 3. Permintaan Barang Produksi Menunggu (Pending Permintaan Barang)
        try {
            $pendingPB = PermintaanBarang::with('user')
                ->where('status', 'pending')
                ->latest()
                ->take(6)
                ->get();

            foreach ($pendingPB as $pb) {
                $pemohon = $pb->user->name ?? 'Staf Produksi';
                $timeAgo = $pb->created_at ? $pb->created_at->locale('id')->diffForHumans() : 'Baru saja';
                $notifications[] = [
                    'id' => 'req-pb-' . $pb->id,
                    'type' => 'info',
                    'category' => 'Permintaan Produksi',
                    'title' => $pb->no_permintaan,
                    'message' => "Diajukan oleh {$pemohon} • Menunggu pengeluaran gudang",
                    'url' => route('permintaan-barang.show', $pb->id),
                    'badge' => 'Menunggu Gudang',
                    'badge_class' => 'bg-slate-100 text-slate-700 border border-slate-200',
                    'icon' => 'clipboard',
                    'time' => $timeAgo,
                ];
            }
        } catch (\Throwable $e) {
            // Ignore
        }

        // 4. Permintaan Pembelian Menunggu (Pending PR)
        try {
            $pendingPR = PermintaanPembelian::with('user')
                ->where('status', 'pending')
                ->latest()
                ->take(6)
                ->get();

            foreach ($pendingPR as $pr) {
                $pemohon = $pr->user->name ?? 'Gudang';
                $timeAgo = $pr->created_at ? $pr->created_at->locale('id')->diffForHumans() : 'Baru saja';
                $notifications[] = [
                    'id' => 'req-pr-' . $pr->id,
                    'type' => 'info',
                    'category' => 'Purchase Requisition',
                    'title' => $pr->no_pr,
                    'message' => "Diajukan oleh {$pemohon} • Menunggu verifikasi Purchasing",
                    'url' => route('permintaan-pembelian.show', $pr->id),
                    'badge' => 'Menunggu PO',
                    'badge_class' => 'bg-slate-100 text-slate-700 border border-slate-200',
                    'icon' => 'shopping-cart',
                    'time' => $timeAgo,
                ];
            }
        } catch (\Throwable $e) {
            // Ignore
        }

        // 5. Purchase Order (PO) Dalam Pengiriman
        try {
            $activePOs = PurchaseOrder::with('supplier')
                ->whereIn('status', ['dikirim', 'diterima_sebagian'])
                ->latest()
                ->take(6)
                ->get();

            foreach ($activePOs as $po) {
                $supplier = $po->supplier->nama_supplier ?? 'Supplier';
                $timeAgo = $po->updated_at ? $po->updated_at->locale('id')->diffForHumans() : 'Aktif';
                $notifications[] = [
                    'id' => 'po-' . $po->id,
                    'type' => 'success',
                    'category' => 'PO Dalam Pengiriman',
                    'title' => $po->no_po,
                    'message' => "Pengiriman oleh {$supplier} • Siap penerimaan gudang",
                    'url' => route('purchase-orders.show', $po->id),
                    'badge' => strtoupper($po->status),
                    'badge_class' => 'bg-slate-100 text-slate-700 border border-slate-200',
                    'icon' => 'truck',
                    'time' => $timeAgo,
                ];
            }
        } catch (\Throwable $e) {
            // Ignore
        }

        $countsByType = [
            'warning' => 0,
            'danger' => 0,
            'info' => 0,
            'success' => 0,
        ];

        foreach ($notifications as $n) {
            if (isset($countsByType[$n['type']])) {
                $countsByType[$n['type']]++;
            }
        }

        return [
            'items' => $notifications,
            'count' => count($notifications),
            'counts_by_type' => $countsByType,
        ];
    }
}
