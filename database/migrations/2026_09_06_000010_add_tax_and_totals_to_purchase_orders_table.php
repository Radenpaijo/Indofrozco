<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->decimal('subtotal', 15, 2)->default(0)->after('status');
            $table->decimal('diskon', 15, 2)->default(0)->after('subtotal');
            $table->decimal('pajak_persen', 5, 2)->default(11.00)->after('diskon');
            $table->decimal('pajak_nominal', 15, 2)->default(0)->after('pajak_persen');
            $table->decimal('total_harga', 15, 2)->default(0)->after('pajak_nominal');
            $table->string('syarat_pembayaran')->nullable()->after('total_harga');
            $table->text('catatan')->nullable()->after('syarat_pembayaran');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn(['subtotal', 'diskon', 'pajak_persen', 'pajak_nominal', 'total_harga', 'syarat_pembayaran', 'catatan']);
        });
    }
};
