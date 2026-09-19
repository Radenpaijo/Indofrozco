<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Category;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Models\Location;
use App\Models\Barang;
use App\Models\Supplier;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Users for each Role
        $roles = ['admin', 'gudang', 'purchasing', 'produksi', 'owner'];
        foreach ($roles as $role) {
            User::updateOrCreate(
                ['email' => "{$role}@indofroz.com"],
                [
                    'name' => 'Staf ' . ucfirst($role),
                    'password' => Hash::make('password'),
                    'role' => $role,
                ]
            );
        }

        // Create a generic test user too
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin Utama',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // 2. Seed Master Categories & Units
        $catFrozen = Category::updateOrCreate(['code' => 'CAT-FROZEN'], ['name' => 'Bahan Frozen / Seafood', 'status' => 'active']);
        $catKering = Category::updateOrCreate(['code' => 'CAT-KERING'], ['name' => 'Bahan Kering & Bumbu', 'status' => 'active']);
        $catKemasan = Category::updateOrCreate(['code' => 'CAT-PACK'], ['name' => 'Kemasan & Packaging', 'status' => 'active']);

        $unitKg = Unit::updateOrCreate(['code' => 'KG'], ['name' => 'Kilogram', 'status' => 'active']);
        $unitRoll = Unit::updateOrCreate(['code' => 'ROLL'], ['name' => 'Roll', 'status' => 'active']);
        $unitSak = Unit::updateOrCreate(['code' => 'SAK'], ['name' => 'Sak / Karung', 'status' => 'active']);

        // 3. Seed Master Warehouses & Locations
        $whCold = Warehouse::updateOrCreate(['code' => 'WH-COLD-01'], ['name' => 'Cold Storage Utama (-18°C)', 'type' => 'cold_storage', 'status' => 'active']);
        $whDry = Warehouse::updateOrCreate(['code' => 'WH-DRY-01'], ['name' => 'Gudang Kering (Dry Storage)', 'type' => 'dry_storage', 'status' => 'active']);

        $locCS1 = Location::updateOrCreate(['code' => 'RAK-CS-01'], ['warehouse_id' => $whCold->id, 'name' => 'Rak Cold Storage 01', 'capacity' => 5000, 'status' => 'active']);
        $locCS2 = Location::updateOrCreate(['code' => 'RAK-CS-02'], ['warehouse_id' => $whCold->id, 'name' => 'Rak Cold Storage 02', 'capacity' => 5000, 'status' => 'active']);
        $locA1 = Location::updateOrCreate(['code' => 'RAK-A-01'], ['warehouse_id' => $whDry->id, 'name' => 'Rak Gudang Kering A-01', 'capacity' => 2000, 'status' => 'active']);
        $locB2 = Location::updateOrCreate(['code' => 'RAK-B-02'], ['warehouse_id' => $whDry->id, 'name' => 'Rak Gudang Kering B-02', 'capacity' => 1000, 'status' => 'active']);

        // 4. Seed Suppliers
        $suppliers = [
            [
                'nama_supplier' => 'PT Udang Windu Jaya',
                'kontak' => '08123456789',
                'alamat' => 'Jl. Pantai Indah No. 10, Sidoarjo',
            ],
            [
                'nama_supplier' => 'CV Kering Sentosa',
                'kontak' => '08777654321',
                'alamat' => 'Kawasan Industri Gresik Blok B-5',
            ],
            [
                'nama_supplier' => 'PT Cold Chain Logistics',
                'kontak' => '08218889990',
                'alamat' => 'Kawasan Pergudangan Margomulyo Permai, Surabaya',
            ],
        ];

        foreach ($suppliers as $sup) {
            Supplier::updateOrCreate(
                ['nama_supplier' => $sup['nama_supplier']],
                $sup
            );
        }

        // 5. Seed Master Barangs
        $barangs = [
            [
                'kode_barang' => 'BRG-001',
                'nama_barang' => 'Udang Windu Segar',
                'satuan' => 'Kg',
                'stok' => 50,
                'stok_minimum' => 20,
                'batas_umur_hari' => 5, // FIFO shelf life
                'lokasi_gudang' => 'Cold Storage',
                'kode_rak' => 'RAK-CS-01',
                'category_id' => $catFrozen->id,
                'unit_id' => $unitKg->id,
                'location_id' => $locCS1->id,
            ],
            [
                'kode_barang' => 'BRG-002',
                'nama_barang' => 'Udang Vaname Super',
                'satuan' => 'Kg',
                'stok' => 120,
                'stok_minimum' => 30,
                'batas_umur_hari' => 4,
                'lokasi_gudang' => 'Cold Storage',
                'kode_rak' => 'RAK-CS-02',
                'category_id' => $catFrozen->id,
                'unit_id' => $unitKg->id,
                'location_id' => $locCS2->id,
            ],
            [
                'kode_barang' => 'BRG-003',
                'nama_barang' => 'Tepung Bumbu Crispy',
                'satuan' => 'Kg',
                'stok' => 200,
                'stok_minimum' => 50,
                'batas_umur_hari' => 180,
                'lokasi_gudang' => 'Gudang Kering',
                'kode_rak' => 'RAK-A-01',
                'category_id' => $catKering->id,
                'unit_id' => $unitKg->id,
                'location_id' => $locA1->id,
            ],
            [
                'kode_barang' => 'BRG-004',
                'nama_barang' => 'Plastik Kemasan PE',
                'satuan' => 'Roll',
                'stok' => 15,
                'stok_minimum' => 5,
                'batas_umur_hari' => 0, // No limit
                'lokasi_gudang' => 'Gudang Kering',
                'kode_rak' => 'RAK-B-02',
                'category_id' => $catKemasan->id,
                'unit_id' => $unitRoll->id,
                'location_id' => $locB2->id,
            ],
            [
                'kode_barang' => 'BRG-005',
                'nama_barang' => 'Garam Halus Premium',
                'satuan' => 'Sak',
                'stok' => 4, // Low stock on purpose
                'stok_minimum' => 10,
                'batas_umur_hari' => 360,
                'lokasi_gudang' => 'Gudang Kering',
                'kode_rak' => 'RAK-A-01',
                'category_id' => $catKering->id,
                'unit_id' => $unitSak->id,
                'location_id' => $locA1->id,
            ],
        ];

        foreach ($barangs as $brg) {
            $createdBarang = Barang::updateOrCreate(
                ['kode_barang' => $brg['kode_barang']],
                $brg
            );

            \App\Models\AuditLog::updateOrCreate(
                [
                    'action' => 'CREATE_ITEM',
                    'record_id' => $createdBarang->id,
                ],
                [
                    'user_id' => 1,
                    'action' => 'CREATE_ITEM',
                    'module' => 'MASTER_BARANG',
                    'record_id' => $createdBarang->id,
                    'new_values' => [
                        'kode_barang' => $createdBarang->kode_barang,
                        'nama_barang' => $createdBarang->nama_barang,
                        'stok' => $createdBarang->stok,
                    ],
                    'ip_address' => '127.0.0.1',
                ]
            );
        }
    }
}
