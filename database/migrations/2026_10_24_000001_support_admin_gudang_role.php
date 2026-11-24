<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change enum to string so custom roles like admin_gudang can be saved without error
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(50) NOT NULL DEFAULT 'gudang'");
        }

        // Update User ID 2 (Admin Gudang) to role admin_gudang and grant full warehouse master & operational permissions
        $adminGudangPerms = json_encode([
            'barangs',
            'barangs_create',
            'barangs_edit',
            'barangs_delete',
            'categories',
            'categories_manage',
            'units',
            'units_manage',
            'locations',
            'locations_manage',
            'warehouses',
            'warehouses_manage',
            'barang_masuk',
            'barang_masuk_create',
            'barang_keluar',
            'barang_keluar_create',
            'stock_opnames',
            'stock_adjustments',
            'stock_mutations',
            'permintaan_barang',
            'permintaan_barang_approve',
            'permintaan_pembelian',
            'permintaan_pembelian_create',
        ]);

        DB::table('users')->where('id', 2)->update([
            'role' => 'admin_gudang',
            'permissions' => $adminGudangPerms,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'gudang', 'purchasing', 'produksi', 'owner') NOT NULL DEFAULT 'produksi'");
        }
    }
};
