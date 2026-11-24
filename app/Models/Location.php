<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    protected $fillable = ['warehouse_id', 'code', 'name', 'capacity', 'satuan', 'status'];

    /**
     * Label satuan kapasitas rak (misal: Kg, Karung, Box, Pcs).
     */
    public function getSatuanLabelAttribute(): string
    {
        if (!empty($this->satuan)) {
            return $this->satuan;
        }

        if ($this->relationLoaded('barangs') && $this->barangs->isNotEmpty()) {
            $barang = $this->barangs->first();
            return $barang->unit->name ?? ($barang->satuan ?? '');
        }

        return '';
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function gudang(): BelongsTo
    {
        return $this->warehouse();
    }

    public function batches(): HasMany
    {
        return $this->hasMany(InventoryBatch::class);
    }

    public function barangs(): HasMany
    {
        return $this->hasMany(Barang::class, 'location_id');
    }

    /**
     * Hitung total stok terisi saat ini di lokasi rak ini.
     */
    public function hitungKapasitasTerpakai(): float
    {
        $barangStok = (float) Barang::where('location_id', $this->id)->sum('stok');
        $batchStok = (float) InventoryBatch::where('location_id', $this->id)->where('status', 'active')->sum('stok_sekarang');
        return max($barangStok, $batchStok);
    }

    /**
     * Kapasitas terpakai saat ini.
     */
    public function getUsedCapacityAttribute(): float
    {
        return $this->hitungKapasitasTerpakai();
    }

    /**
     * Sisa kapasitas rak (null jika tanpa batas).
     */
    public function getSisaKapasitasAttribute(): ?float
    {
        if ($this->capacity === null || (float)$this->capacity <= 0) {
            return null; // Tanpa batas kapasitas
        }
        return max(0.0, (float)$this->capacity - $this->hitungKapasitasTerpakai());
    }

    /**
     * Buat kode lokasi/rak otomatis tanpa count() (contoh: RAK-001).
     */
    public static function buatKodeOtomatis(string $prefix = 'RAK-', int $padding = 3): string
    {
        $field = 'code';
        $isSqlite = \Illuminate\Support\Facades\DB::connection()->getDriverName() === 'sqlite';
        $orderRaw = $isSqlite
            ? 'CAST(SUBSTR(' . $field . ', ' . (strlen($prefix) + 1) . ') AS INTEGER) DESC'
            : 'CAST(SUBSTRING(' . $field . ', ' . (strlen($prefix) + 1) . ') AS UNSIGNED) DESC';

        $lastRecord = self::where($field, 'like', $prefix . '%')
            ->orderByRaw($orderRaw)
            ->first();

        if ($lastRecord) {
            $lastNumber = (int) substr($lastRecord->{$field}, strlen($prefix));
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        $kode = $prefix . str_pad($nextNumber, $padding, '0', STR_PAD_LEFT);

        while (self::where($field, $kode)->exists()) {
            $nextNumber++;
            $kode = $prefix . str_pad($nextNumber, $padding, '0', STR_PAD_LEFT);
        }

        return $kode;
    }
}
