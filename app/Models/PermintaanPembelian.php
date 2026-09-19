<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermintaanPembelian extends Model
{
    protected $table = 'permintaan_pembelian';

    protected $fillable = [
        'user_id',
        'no_pr',
        'tanggal',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(PermintaanPembelianDetail::class, 'pp_id');
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'pp_id');
    }
}
