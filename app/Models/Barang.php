<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'satuan',
        'stok',
        'stok_minimum',
        'batas_umur_hari',
        'lokasi_gudang',
        'kode_rak',
        'is_active',
        'category_id',
        'unit_id',
        'location_id',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'stok' => 'integer',
        'stok_minimum' => 'integer',
        'batas_umur_hari' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function batches()
    {
        return $this->hasMany(InventoryBatch::class, 'barang_id');
    }

    public function transactions()
    {
        return $this->hasMany(InventoryTransaction::class, 'barang_id');
    }

    public function barangMasuks()
    {
        return $this->hasMany(BarangMasuk::class);
    }

    public function barangKeluars()
    {
        return $this->hasMany(BarangKeluar::class);
    }

    public function getIsLowStockAttribute()
    {
        return $this->stok <= $this->stok_minimum;
    }

    /**
     * Get batches of this item that are expired or expiring soon (within 7 days).
     */
    public function getExpiringBatches()
    {
        if ($this->batas_umur_hari <= 0) {
            return collect();
        }

        return $this->barangMasuks()
            ->get()
            ->map(function ($masuk) {
                // Carbon date calculations
                $tanggalMasuk = \Carbon\Carbon::parse($masuk->tanggal);
                $expiryDate = $tanggalMasuk->copy()->addDays($this->batas_umur_hari);
                $now = \Carbon\Carbon::now();
                
                // Diff in days (can be negative if already expired)
                $daysLeft = (int)$now->startOfDay()->diffInDays($expiryDate->startOfDay(), false);
                
                $masuk->expiry_date = $expiryDate;
                $masuk->days_left = $daysLeft;
                $masuk->status = $daysLeft < 0 ? 'expired' : ($daysLeft <= 7 ? 'warning' : 'safe');
                
                return $masuk;
            })
            ->filter(function ($masuk) {
                return $masuk->status === 'expired' || $masuk->status === 'warning';
            })
            ->values();
    }
}
