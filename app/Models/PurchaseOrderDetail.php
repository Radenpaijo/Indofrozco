<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderDetail extends Model
{
    protected $table = 'purchase_order_detail';

    protected $fillable = [
        'po_id',
        'barang_id',
        'jumlah',
        'harga_satuan',
        'subtotal',
        'snapshot_nama_barang',
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'harga_satuan' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'po_id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}
