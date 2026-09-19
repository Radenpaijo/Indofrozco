<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('barang_keluar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permintaan_id')->nullable()->constrained('permintaan_barang'); // Null jika manual
            $table->foreignId('barang_id')->constrained('barangs');
            $table->integer('jumlah');
            $table->foreignId('user_id')->constrained('users'); // Staf Gudang
            $table->string('snapshot_nama_barang')->nullable();
            $table->timestamp('tanggal')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_keluar');
    }
};
