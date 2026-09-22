<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
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
     * Buat kode kategori otomatis tanpa count() (contoh: CAT-001).
     */
    public static function buatKodeOtomatis(string $prefix = 'CAT-', int $padding = 3): string
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
