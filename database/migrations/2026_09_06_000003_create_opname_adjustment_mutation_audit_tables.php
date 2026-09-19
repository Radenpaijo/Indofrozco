<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Stock Opname
        Schema::create('stock_opnames', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_opname')->unique();
            $table->date('tanggal');
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->onDelete('set null');
            $table->foreignId('location_id')->nullable()->constrained('locations')->onDelete('set null');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['draft', 'reviewed', 'completed'])->default('draft');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_opname_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_opname_id')->constrained('stock_opnames')->onDelete('cascade');
            $table->foreignId('barang_id')->constrained('barangs')->onDelete('cascade');
            $table->foreignId('batch_id')->nullable()->constrained('inventory_batches')->onDelete('set null');
            $table->foreignId('location_id')->nullable()->constrained('locations')->onDelete('set null');
            $table->decimal('stok_sistem', 15, 2);
            $table->decimal('stok_fisik', 15, 2);
            $table->decimal('selisih', 15, 2); // stok_fisik - stok_sistem
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // Stock Adjustment
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_adjustment')->unique();
            $table->date('tanggal');
            $table->foreignId('stock_opname_id')->nullable()->constrained('stock_opnames')->onDelete('set null');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['draft', 'approved', 'posted'])->default('draft');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_adjustment_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_adjustment_id')->constrained('stock_adjustments')->onDelete('cascade');
            $table->foreignId('barang_id')->constrained('barangs')->onDelete('cascade');
            $table->foreignId('batch_id')->nullable()->constrained('inventory_batches')->onDelete('set null');
            $table->foreignId('location_id')->nullable()->constrained('locations')->onDelete('set null');
            $table->decimal('stok_sistem', 15, 2);
            $table->decimal('stok_adjustment', 15, 2); // selisih perubahan (+ atau -)
            $table->decimal('stok_akhir', 15, 2);
            $table->string('alasan'); // BR-006: Adjustment WAJIB memiliki alasan
            $table->timestamps();
        });

        // Stock Mutation
        Schema::create('stock_mutations', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_mutasi')->unique();
            $table->date('tanggal');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['draft', 'posted'])->default('draft');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_mutation_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_mutation_id')->constrained('stock_mutations')->onDelete('cascade');
            $table->foreignId('barang_id')->constrained('barangs')->onDelete('cascade');
            $table->foreignId('batch_id')->nullable()->constrained('inventory_batches')->onDelete('set null');
            $table->foreignId('lokasi_asal_id')->constrained('locations')->onDelete('cascade');
            $table->foreignId('lokasi_tujuan_id')->constrained('locations')->onDelete('cascade');
            $table->decimal('jumlah', 15, 2);
            $table->string('alasan')->nullable();
            $table->timestamps();
        });

        // Audit Log
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('action'); // login, create, update, delete, posting, approve
            $table->string('module'); // Master, Receiving, Outbound, StockOpname, dll
            $table->unsignedBigInteger('record_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('stock_mutation_details');
        Schema::dropIfExists('stock_mutations');
        Schema::dropIfExists('stock_adjustment_details');
        Schema::dropIfExists('stock_adjustments');
        Schema::dropIfExists('stock_opname_details');
        Schema::dropIfExists('stock_opnames');
    }
};
