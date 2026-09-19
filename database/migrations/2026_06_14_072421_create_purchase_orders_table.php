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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pp_id')->nullable()->constrained('permintaan_pembelian'); // Referensi PR
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->foreignId('user_id')->constrained('users'); // Pembuat PO (Purchasing)
            $table->string('no_po')->unique();
            $table->timestamp('tanggal')->useCurrent();
            $table->enum('status', ['dikirim', 'diterima_sebagian', 'selesai', 'dibatalkan'])->default('dikirim');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
