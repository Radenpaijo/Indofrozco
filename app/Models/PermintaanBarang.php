<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermintaanBarang extends Model
{
    protected $table = 'permintaan_barang';

    protected $fillable = [
        'user_id',
        'no_permintaan',
        'tanggal',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(PermintaanBarangDetail::class, 'permintaan_id');
    }

    public function barangKeluars()
    {
        return $this->hasMany(BarangKeluar::class, 'permintaan_id');
    }
}
