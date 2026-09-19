<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermintaanPembelianDetail extends Model
{
    protected $table = 'permintaan_pembelian_detail';

    protected $fillable = [
        'pp_id',
        'barang_id',
        'jumlah',
    ];

    protected $casts = [
        'jumlah' => 'integer',
    ];

    public function permintaanPembelian()
    {
        return $this->belongsTo(PermintaanPembelian::class, 'pp_id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}
