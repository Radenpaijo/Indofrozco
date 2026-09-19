<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'pp_id',
        'supplier_id',
        'user_id',
        'no_po',
        'tanggal',
        'status',
        'subtotal',
        'diskon',
        'pajak_persen',
        'pajak_nominal',
        'total_harga',
        'syarat_pembayaran',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
        'subtotal' => 'decimal:2',
        'diskon' => 'decimal:2',
        'pajak_persen' => 'decimal:2',
        'pajak_nominal' => 'decimal:2',
        'total_harga' => 'decimal:2',
    ];

    public function permintaanPembelian()
    {
        return $this->belongsTo(PermintaanPembelian::class, 'pp_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(PurchaseOrderDetail::class, 'po_id');
    }

    public function barangMasuks()
    {
        return $this->hasMany(BarangMasuk::class, 'po_id');
    }
}
