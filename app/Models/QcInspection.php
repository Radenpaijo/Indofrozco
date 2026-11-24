<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QcInspection extends Model
{
    protected $table = 'qc_inspections';

    protected $fillable = [
        'reference_type',
        'reference_id',
        'user_id',
        'tipe_qc',
        'nomor_dokumen',
        'tanggal_cek',
        'jam_cek',
        'kebersihan_kendaraan',
        'kemasan_utuh',
        'expire_date_aman',
        'bebas_benda_asing',
        'kondisi_aroma',
        'kondisi_tekstur',
        'kondisi_penampakan',
        'suhu',
        'berat_keluar_gudang',
        'berat_riil_diterima',
        'susut_kg',
        'susut_persen',
        'status_qc',
        'tindakan_koreksi',
        'catatan',
    ];

    protected $casts = [
        'kebersihan_kendaraan' => 'boolean',
        'kemasan_utuh' => 'boolean',
        'expire_date_aman' => 'boolean',
        'bebas_benda_asing' => 'boolean',
        'kondisi_aroma' => 'boolean',
        'kondisi_tekstur' => 'boolean',
        'kondisi_penampakan' => 'boolean',
        'suhu' => 'decimal:2',
        'berat_keluar_gudang' => 'decimal:2',
        'berat_riil_diterima' => 'decimal:2',
        'susut_kg' => 'decimal:2',
        'susut_persen' => 'decimal:2',
        'tanggal_cek' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reference()
    {
        return $this->morphTo();
    }
}

