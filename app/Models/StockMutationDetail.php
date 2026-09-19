<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMutationDetail extends Model
{
    protected $fillable = [
        'stock_mutation_id',
        'barang_id',
        'batch_id',
        'lokasi_asal_id',
        'lokasi_tujuan_id',
        'jumlah',
        'alasan',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
    ];

    public function stockMutation(): BelongsTo
    {
        return $this->belongsTo(StockMutation::class);
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(InventoryBatch::class, 'batch_id');
    }

    public function lokasiAsal(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'lokasi_asal_id');
    }

    public function lokasiTujuan(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'lokasi_tujuan_id');
    }
}
