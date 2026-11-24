<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangMasuk extends Model
{
    protected $table = 'barang_masuk';

    protected $fillable = [
        'no_masuk',
        'po_id',
        'barang_id',
        'location_id',
        'jumlah',
        'jumlah_pack',
        'batch_number',
        'tanggal_kadaluarsa',
        'no_surat_jalan',
        'user_id',
        'snapshot_nama_barang',
        'tanggal',
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'jumlah_pack' => 'integer',
        'tanggal' => 'datetime',
        'tanggal_kadaluarsa' => 'date',
    ];

    public function qcInspection()
    {
        return $this->morphOne(QcInspection::class, 'reference');
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'po_id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
