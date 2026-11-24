<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Barang;
use App\Models\InventoryBatch;
use App\Models\InventoryTransaction;
use Illuminate\Support\Facades\DB;
use Exception;

class InventoryService
{
    /**
     * Catat Penerimaan Stok (Inbound Receiving)
     */
    public function recordReceiving(
        Barang $barang,
        float $qtyDiterima,
        ?int $locationId = null,
        ?string $batchNumber = null,
        ?string $expDate = null,
        string $refType = 'Receiving',
        ?int $refId = null,
        ?int $userId = null,
        ?string $notes = null
    ): InventoryBatch {
        return DB::transaction(function () use ($barang, $qtyDiterima, $locationId, $batchNumber, $expDate, $refType, $refId, $userId, $notes) {
            $batchNo = $batchNumber ?: 'BATCH-' . date('Ymd') . '-' . rand(100, 999);
            
            $batch = InventoryBatch::create([
                'barang_id' => $barang->id,
                'location_id' => $locationId ?: $barang->location_id,
                'batch_number' => $batchNo,
                'tanggal_masuk' => now()->toDateString(),
                'tanggal_kadaluarsa' => $expDate,
                'stok' => $qtyDiterima,
                'status' => 'active',
            ]);

            // Update stok total barang
            $oldStok = $barang->stok;
            $barang->increment('stok', $qtyDiterima);

            // Ledger Transaction
            InventoryTransaction::create([
                'barang_id' => $barang->id,
                'batch_id' => $batch->id,
                'location_id' => $batch->location_id,
                'type' => 'IN',
                'jumlah' => $qtyDiterima,
                'reference_type' => $refType,
                'reference_id' => $refId,
                'user_id' => $userId,
                'catatan' => $notes ?: "Receiving batch {$batchNo}",
            ]);

            // Audit Log
            $this->logAudit(
                $userId,
                'RECEIVING',
                'INBOUND',
                $refId,
                ['stok' => $oldStok],
                ['stok' => $barang->stok, 'batch_number' => $batchNo, 'qty' => $qtyDiterima]
            );

            return $batch;
        });
    }

    /**
     * Catat Pengeluaran Stok (Outbound)
     */
    public function recordOutbound(
        Barang $barang,
        float $qtyOut,
        ?int $batchId = null,
        ?int $locationId = null,
        string $refType = 'Outbound',
        ?int $refId = null,
        ?int $userId = null,
        ?string $notes = null
    ): void {
        DB::transaction(function () use ($barang, $qtyOut, $batchId, $locationId, $refType, $refId, $userId, $notes) {
            if ($barang->stok < $qtyOut) {
                throw new Exception("Stok barang {$barang->nama_barang} tidak mencukupi (Tersedia: {$barang->stok}, Diminta: {$qtyOut}).");
            }

            $oldStok = $barang->stok;
            $remainingToDeduct = $qtyOut;

            if ($batchId) {
                // Manual Batch selection
                $batch = InventoryBatch::findOrFail($batchId);
                if ($batch->stok < $qtyOut) {
                    throw new Exception("Stok pada batch {$batch->batch_number} tidak mencukupi (Tersedia: {$batch->stok}).");
                }
                $batch->decrement('stok', $qtyOut);
                if ($batch->stok <= 0) {
                    $batch->update(['status' => 'empty']);
                }

                InventoryTransaction::create([
                    'barang_id' => $barang->id,
                    'batch_id' => $batch->id,
                    'location_id' => $batch->location_id,
                    'type' => 'OUT',
                    'jumlah' => $qtyOut,
                    'reference_type' => $refType,
                    'reference_id' => $refId,
                    'user_id' => $userId,
                    'catatan' => $notes ?: "Outbound batch {$batch->batch_number}",
                ]);
            } else {
                // FEFO Auto recommendation (Prioritas tanggal kadaluarsa terdekat, lalu tanggal masuk tertua)
                $batches = InventoryBatch::where('barang_id', $barang->id)
                    ->where('stok', '>', 0)
                    ->where('status', 'active')
                    ->orderByRaw('COALESCE(tanggal_kadaluarsa, "9999-12-31") ASC')
                    ->orderBy('tanggal_masuk', 'asc')
                    ->orderBy('id', 'asc')
                    ->get();

                foreach ($batches as $batch) {
                    if ($remainingToDeduct <= 0) break;

                    $deduct = min($batch->stok, $remainingToDeduct);
                    $batch->decrement('stok', $deduct);
                    if ($batch->stok <= 0) {
                        $batch->update(['status' => 'empty']);
                    }

                    $remainingToDeduct -= $deduct;

                    InventoryTransaction::create([
                        'barang_id' => $barang->id,
                        'batch_id' => $batch->id,
                        'location_id' => $batch->location_id,
                        'type' => 'OUT',
                        'jumlah' => $deduct,
                        'reference_type' => $refType,
                        'reference_id' => $refId,
                        'user_id' => $userId,
                        'catatan' => $notes ?: "Outbound FIFO batch {$batch->batch_number}",
                    ]);
                }
            }

            $barang->decrement('stok', $qtyOut);

            $this->logAudit(
                $userId,
                'OUTBOUND',
                'OUTBOUND',
                $refId,
                ['stok' => $oldStok],
                ['stok' => $barang->stok, 'qty' => $qtyOut]
            );
        });
    }

    /**
     * Catat Mutasi Stok Antar Rak/Lokasi
     */
    public function recordMutation(
        Barang $barang,
        ?int $batchId,
        int $sourceLocationId,
        int $targetLocationId,
        float $qty,
        ?string $reason = null,
        ?int $userId = null,
        ?int $refId = null
    ): void {
        DB::transaction(function () use ($barang, $batchId, $sourceLocationId, $targetLocationId, $qty, $reason, $userId, $refId) {
            if ($batchId) {
                $batch = InventoryBatch::findOrFail($batchId);
                if ($batch->stok < $qty) {
                    throw new Exception("Stok batch {$batch->batch_number} tidak cukup untuk dimutasi.");
                }
                // Kurangi stok dari batch lama, buat/update stok di lokasi tujuan
                $batch->decrement('stok', $qty);

                $targetBatch = InventoryBatch::create([
                    'barang_id' => $barang->id,
                    'location_id' => $targetLocationId,
                    'batch_number' => $batch->batch_number,
                    'tanggal_masuk' => $batch->tanggal_masuk,
                    'tanggal_kadaluarsa' => $batch->tanggal_kadaluarsa,
                    'stok' => $qty,
                    'status' => 'active',
                ]);
            }

            InventoryTransaction::create([
                'barang_id' => $barang->id,
                'batch_id' => $batchId,
                'location_id' => $sourceLocationId,
                'type' => 'TRANSFER',
                'jumlah' => -$qty,
                'reference_type' => 'MutationOut',
                'reference_id' => $refId,
                'user_id' => $userId,
                'catatan' => "Mutasi keluar ke lokasi {$targetLocationId}. " . $reason,
            ]);

            InventoryTransaction::create([
                'barang_id' => $barang->id,
                'batch_id' => $targetBatch->id ?? null,
                'location_id' => $targetLocationId,
                'type' => 'TRANSFER',
                'jumlah' => $qty,
                'reference_type' => 'MutationIn',
                'reference_id' => $refId,
                'user_id' => $userId,
                'catatan' => "Mutasi masuk dari lokasi {$sourceLocationId}. " . $reason,
            ]);

            $this->logAudit(
                $userId,
                'MUTATION',
                'STOCK_MUTATION',
                $refId,
                ['source_location' => $sourceLocationId],
                ['target_location' => $targetLocationId, 'qty' => $qty, 'reason' => $reason]
            );
        });
    }

    /**
     * Catat Penyesuaian Stok (Adjustment)
     */
    public function recordAdjustment(
        Barang $barang,
        ?int $batchId,
        ?int $locationId,
        float $qtyAdjustment,
        string $reason,
        ?int $userId = null,
        ?int $refId = null
    ): void {
        DB::transaction(function () use ($barang, $batchId, $locationId, $qtyAdjustment, $reason, $userId, $refId) {
            $oldStok = $barang->stok;
            $newStok = $oldStok + $qtyAdjustment;

            if ($newStok < 0) {
                throw new Exception("Adjustment menyebabkan stok minus.");
            }

            $barang->update(['stok' => $newStok]);

            if ($batchId) {
                $batch = InventoryBatch::findOrFail($batchId);
                $batch->increment('stok', $qtyAdjustment);
            }

            InventoryTransaction::create([
                'barang_id' => $barang->id,
                'batch_id' => $batchId,
                'location_id' => $locationId ?: $barang->location_id,
                'type' => 'ADJUSTMENT',
                'jumlah' => $qtyAdjustment,
                'reference_type' => 'StockAdjustment',
                'reference_id' => $refId,
                'user_id' => $userId,
                'catatan' => "Adjustment: {$reason}",
            ]);

            $this->logAudit(
                $userId,
                'ADJUSTMENT',
                'STOCK_ADJUSTMENT',
                $refId,
                ['stok' => $oldStok],
                ['stok' => $newStok, 'adjustment' => $qtyAdjustment, 'reason' => $reason]
            );
        });
    }

    /**
     * Audit Log Helper
     */
    public function logAudit(
        ?int $userId,
        string $action,
        string $module,
        ?int $recordId = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): void {
        AuditLog::create([
            'user_id' => $userId ?: auth()->id(),
            'action' => $action,
            'module' => $module,
            'record_id' => $recordId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
        ]);
    }
}
