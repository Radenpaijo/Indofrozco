<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAdjustmentDetail extends Model
{
    protected $fillable = [
        'stock_adjustment_id',
        'barang_id',
        'batch_id',
        'location_id',
        'stok_sistem',
        'stok_adjustment',
        'stok_akhir',
        'alasan',
    ];

    protected $casts = [
        'stok_sistem' => 'decimal:2',
        'stok_adjustment' => 'decimal:2',
        'stok_akhir' => 'decimal:2',
    ];

    public function stockAdjustment(): BelongsTo
    {
        return $this->belongsTo(StockAdjustment::class);
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(InventoryBatch::class, 'batch_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}
