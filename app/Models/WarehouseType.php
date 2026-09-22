<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WarehouseType extends Model
{
    use HasUuids;

    protected $fillable = [
        'code',
        'name',
        'status',
        'description',
    ];

    public function warehouses(): HasMany
    {
        return $this->hasMany(Warehouse::class, 'warehouse_type_id');
    }

    public function daftarGudang(): HasMany
    {
        return $this->warehouses();
    }

    /**
     * Membuat kode tipe gudang otomatis tanpa count() (contoh: TPG-001).
     */
    public static function buatKodeOtomatis(string $prefix = 'TPG-', int $padding = 3): string
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
