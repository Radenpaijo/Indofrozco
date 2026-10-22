<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryBatch extends Model
{
    protected $fillable = [
        'barang_id',
        'location_id',
        'batch_number',
        'tanggal_masuk',
        'tanggal_kadaluarsa',
        'stok',
        'stok_awal',
        'stok_sekarang',
        'status',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'tanggal_kadaluarsa' => 'date',
        'stok' => 'decimal:2',
        'stok_awal' => 'decimal:2',
        'stok_sekarang' => 'decimal:2',
    ];

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class, 'batch_id');
    }
}
