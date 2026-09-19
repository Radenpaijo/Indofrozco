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
        Schema::create('barang_masuk', function (Blueprint $table) {
            $table->id();
            $table->string('no_masuk');
            $table->foreignId('po_id')->nullable()->constrained('purchase_orders'); // Null jika input manual
            $table->foreignId('barang_id')->constrained('barangs');
            $table->integer('jumlah');
            $table->string('no_surat_jalan')->nullable();
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
        Schema::dropIfExists('barang_masuk');
    }
};
