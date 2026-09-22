<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    use HasUuids;

    protected $fillable = [
        'code',
        'name',
        'warehouse_type_id',
        'status',
        'description',
    ];

    public function warehouseType(): BelongsTo
    {
        return $this->belongsTo(WarehouseType::class, 'warehouse_type_id');
    }

    public function tipeGudang(): BelongsTo
    {
        return $this->warehouseType();
    }

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class, 'warehouse_id');
    }

    public function daftarLokasiRak(): HasMany
    {
        return $this->locations();
    }

    /**
     * Generate kode gudang berurutan secara otomatis tanpa count() (contoh: GDG-001, GDG-002).
     */
    public static function buatKodeOtomatis(string $prefix = 'GDG-', int $padding = 3): string
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

    public static function generateNextCode(): string
    {
        return self::buatKodeOtomatis();
    }
}
