<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    protected $fillable = ['code', 'name', 'status', 'description'];

    public function barangs(): HasMany
    {
        return $this->hasMany(Barang::class);
    }

    public function daftarBarang(): HasMany
    {
        return $this->barangs();
    }

    /**
     * Buat kode satuan otomatis tanpa count() (contoh: STN-001).
     */
    public static function buatKodeOtomatis(string $prefix = 'STN-', int $padding = 3): string
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
