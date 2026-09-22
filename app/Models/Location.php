<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    protected $fillable = ['warehouse_id', 'code', 'name', 'capacity', 'status'];

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
        return (float) Barang::where('location_id', $this->id)->sum('stok');
    }

    /**
     * Buat kode lokasi/rak otomatis tanpa count() (contoh: RAK-001).
     */
    public static function buatKodeOtomatis(string $prefix = 'RAK-', int $padding = 3): string
    {
        $field = 'code';
        $lastRecord = self::where($field, 'like', $prefix . '%')
            ->orderByRaw('CAST(SUBSTRING(' . $field . ', ' . (strlen($prefix) + 1) . ') AS UNSIGNED) DESC')
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
