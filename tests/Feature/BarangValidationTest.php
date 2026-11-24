<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use App\Models\Unit;
use App\Models\WarehouseType;
use App\Models\Warehouse;
use App\Models\Location;
use App\Models\Barang;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BarangValidationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Category $category;
    protected Unit $unit;
    protected Warehouse $warehouse;
    protected Location $locationLimited;
    protected Location $locationUnlimited;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'role' => 'admin',
        ]);

        $this->category = Category::create([
            'code' => 'KAT-001',
            'name' => 'Bahan Baku',
            'status' => 'active',
        ]);

        $this->unit = Unit::create([
            'code' => 'SAT-001',
            'name' => 'Kg',
            'status' => 'active',
        ]);

        $type = WarehouseType::create([
            'code' => 'TIPE-001',
            'name' => 'Cold Storage',
            'status' => 'active',
        ]);

        $this->warehouse = Warehouse::create([
            'code' => 'GDG-001',
            'name' => 'Gudang Utama',
            'warehouse_type_id' => $type->id,
            'status' => 'active',
        ]);

        $this->locationLimited = Location::create([
            'warehouse_id' => $this->warehouse->id,
            'code' => 'RAK-004',
            'name' => 'Rak Kecil',
            'capacity' => 10,
            'status' => 'active',
        ]);

        $this->locationUnlimited = Location::create([
            'warehouse_id' => $this->warehouse->id,
            'code' => 'RAK-003',
            'name' => 'Rak Bebas',
            'capacity' => null,
            'status' => 'active',
        ]);
    }

    public function test_cannot_create_barang_when_stok_minimum_exceeds_rack_capacity(): void
    {
        $response = $this->actingAs($this->admin)->post(route('barangs.store'), [
            'kode_barang' => 'BRG-001',
            'nama_barang' => 'Barang Melebihi Rak',
            'category_id' => $this->category->id,
            'unit_id' => $this->unit->id,
            'satuan' => 'Kg',
            'location_id' => $this->locationLimited->id, // capacity 10
            'stok_minimum' => 11, // exceeds 10!
        ]);

        $response->assertSessionHasErrors(['stok_minimum', 'location_id']);
        $this->assertDatabaseMissing('barangs', [
            'kode_barang' => 'BRG-001',
        ]);
    }

    public function test_can_create_barang_when_stok_minimum_within_rack_capacity(): void
    {
        $response = $this->actingAs($this->admin)->post(route('barangs.store'), [
            'kode_barang' => 'BRG-002',
            'nama_barang' => 'Barang Muat di Rak',
            'category_id' => $this->category->id,
            'unit_id' => $this->unit->id,
            'satuan' => 'Kg',
            'location_id' => $this->locationLimited->id, // capacity 10
            'stok_minimum' => 8, // <= 10
        ]);

        $response->assertRedirect(route('barangs.index'));
        $this->assertDatabaseHas('barangs', [
            'kode_barang' => 'BRG-002',
            'stok_minimum' => 8,
            'location_id' => $this->locationLimited->id,
        ]);
    }

    public function test_can_create_barang_when_rack_has_unlimited_capacity(): void
    {
        $response = $this->actingAs($this->admin)->post(route('barangs.store'), [
            'kode_barang' => 'BRG-003',
            'nama_barang' => 'Barang Rak Bebas',
            'category_id' => $this->category->id,
            'unit_id' => $this->unit->id,
            'satuan' => 'Kg',
            'location_id' => $this->locationUnlimited->id, // capacity null
            'stok_minimum' => 9999,
        ]);

        $response->assertRedirect(route('barangs.index'));
        $this->assertDatabaseHas('barangs', [
            'kode_barang' => 'BRG-003',
            'stok_minimum' => 9999,
        ]);
    }

    public function test_cannot_update_barang_when_stok_minimum_exceeds_rack_capacity(): void
    {
        $barang = Barang::create([
            'kode_barang' => 'BRG-004',
            'nama_barang' => 'Barang Edit',
            'category_id' => $this->category->id,
            'unit_id' => $this->unit->id,
            'satuan' => 'Kg',
            'location_id' => $this->locationLimited->id,
            'stok_minimum' => 5,
            'stok' => 0,
            'batas_umur_hari' => 0,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->put(route('barangs.update', $barang->id), [
            'kode_barang' => 'BRG-004',
            'nama_barang' => 'Barang Edit Diubah',
            'category_id' => $this->category->id,
            'unit_id' => $this->unit->id,
            'satuan' => 'Kg',
            'location_id' => $this->locationLimited->id,
            'stok_minimum' => 15, // exceeds 10!
        ]);

        $response->assertSessionHasErrors(['stok_minimum', 'location_id']);
        $barang->refresh();
        $this->assertEquals(5, $barang->stok_minimum);
    }
}
