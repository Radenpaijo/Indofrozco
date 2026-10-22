<?php

namespace App\Observers;

use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use App\Models\InventoryBatch;
use App\Models\InventoryTransaction;
use App\Models\AuditLog;
use Carbon\Carbon;

class BarangMasukObserver
{
    public function creating(BarangMasuk $barangMasuk)
    {
        // Snapshot the current item name for historical records
        if (empty($barangMasuk->snapshot_nama_barang) && $barangMasuk->barang) {
            $barangMasuk->snapshot_nama_barang = $barangMasuk->barang->nama_barang;
        }
    }

    public function created(BarangMasuk $barangMasuk)
    {
        $qtyDiterima = $barangMasuk->jumlah_diterima > 0 ? $barangMasuk->jumlah_diterima : $barangMasuk->jumlah;

        // Otomatis tambah stok barang fisik saat tercatat barang masuk
        $barang = $barangMasuk->barang;
        $stokSebelum = $barang ? $barang->stok : 0;
        if ($barang) {
            $barang->increment('stok', $qtyDiterima);
        }
        $stokSesudah = $barang ? $barang->fresh()->stok : $qtyDiterima;

        // Record Inventory Batch
        $batchNo = $barangMasuk->batch_number ?: ('BATCH-' . date('Ymd') . '-' . str_pad($barangMasuk->id, 4, '0', STR_PAD_LEFT));
        $batch = InventoryBatch::create([
            'barang_id' => $barangMasuk->barang_id,
            'location_id' => $barangMasuk->location_id ?: ($barang ? $barang->location_id : null),
            'batch_number' => $batchNo,
            'tanggal_masuk' => $barangMasuk->tanggal ? Carbon::parse($barangMasuk->tanggal)->toDateString() : now()->toDateString(),
            'tanggal_kadaluarsa' => $barangMasuk->tanggal_kadaluarsa,
            'stok' => $qtyDiterima,
            'stok_awal' => $qtyDiterima,
            'stok_sekarang' => $qtyDiterima,
            'status' => 'active',
        ]);

        // Record Inventory Transaction Ledger
        InventoryTransaction::create([
            'barang_id' => $barangMasuk->barang_id,
            'batch_id' => $batch->id,
            'location_id' => $batch->location_id,
            'type' => 'IN',
            'jenis_transaksi' => 'masuk',
            'nomor_referensi' => $barangMasuk->no_masuk,
            'jumlah' => $qtyDiterima,
            'stok_sebelum' => $stokSebelum,
            'stok_sesudah' => $stokSesudah,
            'reference_type' => 'BarangMasuk',
            'reference_id' => $barangMasuk->id,
            'user_id' => $barangMasuk->user_id,
            'catatan' => 'Penerimaan barang ' . $barangMasuk->no_masuk,
        ]);

        // Record Audit Log
        AuditLog::create([
            'user_id' => $barangMasuk->user_id ?: auth()->id(),
            'action' => 'RECEIVING',
            'module' => 'INBOUND',
            'record_id' => $barangMasuk->id,
            'new_values' => [
                'no_masuk' => $barangMasuk->no_masuk,
                'barang' => $barangMasuk->barang->nama_barang ?? null,
                'jumlah' => $qtyDiterima,
                'no_surat_jalan' => $barangMasuk->no_surat_jalan,
                'batch_number' => $batchNo,
            ],
            'ip_address' => request()->ip(),
        ]);

    }
}
