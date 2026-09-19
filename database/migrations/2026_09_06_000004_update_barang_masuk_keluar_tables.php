<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->foreignId('location_id')->nullable()->after('barang_id')->constrained('locations')->onDelete('set null');
            $table->foreignId('batch_id')->nullable()->after('location_id')->constrained('inventory_batches')->onDelete('set null');
            $table->string('batch_number')->nullable()->after('batch_id');
            $table->date('tanggal_kadaluarsa')->nullable()->after('batch_number');
            $table->integer('jumlah_diterima')->default(0)->after('jumlah');
            $table->integer('jumlah_ditolak')->default(0)->after('jumlah_diterima');
            $table->string('kondisi')->default('Baik')->after('jumlah_ditolak');
            $table->text('catatan')->nullable()->after('kondisi');
        });

        Schema::table('barang_keluar', function (Blueprint $table) {
            $table->foreignId('location_id')->nullable()->after('barang_id')->constrained('locations')->onDelete('set null');
            $table->foreignId('batch_id')->nullable()->after('location_id')->constrained('inventory_batches')->onDelete('set null');
            $table->string('tujuan')->nullable()->after('jumlah');
            $table->text('catatan')->nullable()->after('tujuan');
        });
    }

    public function down(): void
    {
        Schema::table('barang_keluar', function (Blueprint $table) {
            $table->dropForeign(['location_id']);
            $table->dropForeign(['batch_id']);
            $table->dropColumn(['location_id', 'batch_id', 'tujuan', 'catatan']);
        });

        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->dropForeign(['location_id']);
            $table->dropForeign(['batch_id']);
            $table->dropColumn(['location_id', 'batch_id', 'batch_number', 'tanggal_kadaluarsa', 'jumlah_diterima', 'jumlah_ditolak', 'kondisi', 'catatan']);
        });
    }
};
