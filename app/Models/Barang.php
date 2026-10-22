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

    public function kategori()
    {
        return $this->category();
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function satuanModel()
    {
        return $this->unit();
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function lokasiRak()
    {
        return $this->location();
    }

    public function warehouse()
    {
        return $this->hasOneThrough(Warehouse::class, Location::class, 'id', 'id', 'location_id', 'warehouse_id');
    }

    public function gudang()
    {
        return $this->warehouse();
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
     * Generate kode barang berurutan secara otomatis tanpa count() (contoh: BRG-001, BRG-002).
     */
    public static function buatKodeOtomatis(string $prefix = 'BRG-', int $padding = 3): string
    {
        $field = 'kode_barang';
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

    /**
     * Dapatkan daftar batch barang yang expired atau mendekati kadaluarsa (dalam 7 hari).
     */
    public function dapatkanBatchKadaluarsa()
    {
        if ($this->batas_umur_hari <= 0) {
            return collect();
        }

        return $this->barangMasuks()
            ->get()
            ->map(function ($masuk) {
                $tanggalMasuk = \Carbon\Carbon::parse($masuk->tanggal);
                $tanggalKadaluarsa = $tanggalMasuk->copy()->addDays($this->batas_umur_hari);
                $sekarang = \Carbon\Carbon::now();
                
                $sisaHari = (int)$sekarang->startOfDay()->diffInDays($tanggalKadaluarsa->startOfDay(), false);
                
                $masuk->expiry_date = $tanggalKadaluarsa;
                $masuk->days_left = $sisaHari;
                $masuk->status = $sisaHari < 0 ? 'expired' : ($sisaHari <= 7 ? 'warning' : 'safe');
                
                return $masuk;
            })
            ->filter(function ($masuk) {
                return $masuk->status === 'expired' || $masuk->status === 'warning';
            })
            ->values();
    }

    public function getExpiringBatches()
    {
        return $this->dapatkanBatchKadaluarsa();
    }
}
