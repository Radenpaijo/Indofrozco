<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermintaanBarangDetail extends Model
{
    protected $table = 'permintaan_barang_detail';

    protected $fillable = [
        'permintaan_id',
        'barang_id',
        'jumlah',
    ];

    protected $casts = [
        'jumlah' => 'integer',
    ];

    public function permintaan()
    {
        return $this->belongsTo(PermintaanBarang::class, 'permintaan_id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}
