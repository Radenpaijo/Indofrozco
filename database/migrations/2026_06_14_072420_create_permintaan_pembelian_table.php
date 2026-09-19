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
        Schema::create('permintaan_pembelian', function (Blueprint $table) {
            $table->id();
            // $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained('users'); // Pemohon (Gudang)
            $table->string('no_pr')->unique();
            $table->timestamp('tanggal')->useCurrent();
            $table->enum('status', ['pending', 'di_po', 'dibatalkan'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permintaan_pembelian');
    }
};
