<?php

namespace App\Observers;

use App\Models\BarangKeluar;
use App\Models\InventoryBatch;
use App\Models\InventoryTransaction;
use App\Models\AuditLog;
use Exception;

class BarangKeluarObserver
{
    /**
     * Handle the BarangKeluar "creating" event.
     */
    public function creating(BarangKeluar $barangKeluar)
    {
        $barang = $barangKeluar->barang;

        // Snapshot current item name for historical integrity
        if (empty($barangKeluar->snapshot_nama_barang) && $barang) {
            $barangKeluar->snapshot_nama_barang = $barang->nama_barang;
        }

        // Validate stock availability
        if (!$barang || $barang->stok < $barangKeluar->jumlah) {
            $stokTersedia = $barang ? $barang->stok : 0;
            $namaBarang = $barang ? $barang->nama_barang : 'Unknown Item';
            throw new Exception("Stok tidak mencukupi untuk barang: {$namaBarang} (Stok tersedia: {$stokTersedia}, Diminta: {$barangKeluar->jumlah})");
        }
    }

    /**
     * Handle the BarangKeluar "created" event with Dynamic FIFO Batch Deduction.
     */
    public function created(BarangKeluar $barangKeluar)
    {
        $barang = $barangKeluar->barang;
        $qtyNeeded = (float) $barangKeluar->jumlah;

        // 1. Decrement overall physical stock on Barang catalog
        $barang->decrement('stok', $qtyNeeded);

        // 2. Dynamic FIFO Batch Deduction (First In, First Out)
        // Find active batches for this item, sorted by entry date (FIFO)
        $activeBatches = InventoryBatch::where('barang_id', $barangKeluar->barang_id)
            ->where('stok', '>', 0)
            ->where('status', 'active')
            ->orderBy('tanggal_masuk', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $remainingQty = $qtyNeeded;
        $batchDetails = [];

        if ($activeBatches->isNotEmpty()) {
            foreach ($activeBatches as $batch) {
                if ($remainingQty <= 0) {
                    break;
                }

                $batchStok = (float) $batch->stok;
                $deduct = min($batchStok, $remainingQty);

                // Update batch stock
                $batch->stok = $batchStok - $deduct;
                if ($batch->stok <= 0) {
                    $batch->status = 'empty';
                }
                $batch->save();

                // Record Ledger Transaction for this specific FIFO batch
                InventoryTransaction::create([
                    'barang_id' => $barangKeluar->barang_id,
                    'batch_id' => $batch->id,
                    'location_id' => $batch->location_id ?: $barang->location_id,
                    'type' => 'OUT',
                    'jumlah' => $deduct,
                    'reference_type' => 'BarangKeluar',
                    'reference_id' => $barangKeluar->id,
                    'user_id' => $barangKeluar->user_id,
                    'catatan' => "Alokasi FIFO Batch #{$batch->batch_number} (-{$deduct} " . ($barang->satuan ?? 'unit') . ")",
                ]);

                $batchDetails[] = [
                    'batch_number' => $batch->batch_number,
                    'jumlah_dipotong' => $deduct,
                    'sisa_stok_batch' => $batch->stok,
                ];

                $remainingQty -= $deduct;
            }
        }

        // Fallback: If remainingQty > 0 (e.g., initial item stock created before batching system was active)
        if ($remainingQty > 0) {
            InventoryTransaction::create([
                'barang_id' => $barangKeluar->barang_id,
                'batch_id' => null,
                'location_id' => $barang->location_id,
                'type' => 'OUT',
                'jumlah' => $remainingQty,
                'reference_type' => 'BarangKeluar',
                'reference_id' => $barangKeluar->id,
                'user_id' => $barangKeluar->user_id,
                'catatan' => 'Barang Keluar (Stok fisik langsung tanpa batch)',
            ]);
        }

        // 3. Audit Log
        AuditLog::create([
            'user_id' => $barangKeluar->user_id ?: auth()->id(),
            'action' => 'OUTBOUND_FIFO',
            'module' => 'OUTBOUND',
            'record_id' => $barangKeluar->id,
            'new_values' => [
                'barang' => $barang->nama_barang ?? null,
                'jumlah' => $barangKeluar->jumlah,
                'tujuan' => $barangKeluar->tujuan ?? 'Pengeluaran Gudang / Produksi',
                'fifo_batches_processed' => $batchDetails,
            ],
            'ip_address' => request()->ip(),
        ]);
    }
}
