<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->foreignId('purchase_order_id')->nullable()->after('po_id')->constrained('purchase_orders')->onDelete('set null');
            $table->foreignId('location_id')->nullable()->after('barang_id')->constrained('locations')->onDelete('set null');
            $table->string('nomor_penerimaan')->nullable()->after('no_masuk');
            $table->string('nomor_surat_jalan')->nullable()->after('nomor_penerimaan');
            $table->date('tanggal_kadaluarsa')->nullable()->after('tanggal');
            $table->decimal('harga_satuan', 15, 2)->default(0)->after('jumlah');
            $table->decimal('total_harga', 15, 2)->default(0)->after('harga_satuan');
            $table->text('catatan')->nullable()->after('total_harga');
        });

        Schema::table('barang_keluar', function (Blueprint $table) {
            $table->foreignId('permintaan_barang_id')->nullable()->after('user_id')->constrained('permintaan_barang')->onDelete('set null');
            $table->foreignId('location_id')->nullable()->after('barang_id')->constrained('locations')->onDelete('set null');
            $table->string('nomor_pengeluaran')->nullable()->after('permintaan_barang_id');
            $table->string('tujuan_pengeluaran')->nullable()->after('nomor_pengeluaran');
            $table->text('catatan')->nullable()->after('jumlah');
        });
    }

    public function down(): void
    {
        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->dropForeign(['purchase_order_id']);
            $table->dropForeign(['location_id']);
            $table->dropColumn([
                'purchase_order_id', 'location_id', 'nomor_penerimaan',
                'nomor_surat_jalan', 'tanggal_kadaluarsa', 'harga_satuan',
                'total_harga', 'catatan'
            ]);
        });

        Schema::table('barang_keluar', function (Blueprint $table) {
            $table->dropForeign(['permintaan_barang_id']);
            $table->dropForeign(['location_id']);
            $table->dropColumn([
                'permintaan_barang_id', 'location_id', 'nomor_pengeluaran',
                'tujuan_pengeluaran', 'catatan'
            ]);
        });
    }
};
