<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_batches', function (Blueprint $table) {
            if (!Schema::hasColumn('inventory_batches', 'stok')) {
                $table->decimal('stok', 15, 2)->default(0)->after('stok_sekarang');
            }
            $table->decimal('stok_awal', 15, 2)->default(0)->change();
            $table->decimal('stok_sekarang', 15, 2)->default(0)->change();
        });

        Schema::table('inventory_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('inventory_transactions', 'type')) {
                $table->string('type', 50)->nullable()->after('location_id');
            }
            if (!Schema::hasColumn('inventory_transactions', 'reference_type')) {
                $table->string('reference_type', 100)->nullable()->after('jumlah');
            }
            if (!Schema::hasColumn('inventory_transactions', 'reference_id')) {
                $table->string('reference_id', 100)->nullable()->after('reference_type');
            }
            $table->string('jenis_transaksi', 50)->nullable()->change();
            $table->string('nomor_referensi', 100)->nullable()->change();
            $table->decimal('stok_sebelum', 15, 2)->default(0)->change();
            $table->decimal('stok_sesudah', 15, 2)->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('inventory_batches', function (Blueprint $table) {
            if (Schema::hasColumn('inventory_batches', 'stok')) {
                $table->dropColumn('stok');
            }
        });

        Schema::table('inventory_transactions', function (Blueprint $table) {
            $table->dropColumn(['type', 'reference_type', 'reference_id']);
        });
    }
};
