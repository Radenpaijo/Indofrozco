<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use App\Models\Unit;
use App\Models\WarehouseType;
use App\Models\Warehouse;
use App\Models\Location;
use App\Models\Supplier;
use App\Models\Barang;
use App\Models\PermintaanPembelian;
use App\Models\PurchaseOrder;
use App\Models\BarangMasuk;
use App\Models\PermintaanBarang;
use App\Models\BarangKeluar;
use App\Models\InventoryBatch;
use App\Models\InventoryTransaction;
use App\Models\StockOpname;
use App\Models\StockAdjustment;
use App\Models\StockMutation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WmsEndToEndFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $gudang;
    protected User $purchasing;
    protected User $produksi;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Administrator',
            'email' => 'admin@indofroz.com',
            'role' => 'admin',
        ]);

        $this->gudang = User::factory()->create([
            'name' => 'Staf Gudang',
            'email' => 'gudang@indofroz.com',
            'role' => 'gudang',
        ]);

        $this->purchasing = User::factory()->create([
            'name' => 'Staf Purchasing',
            'email' => 'purchasing@indofroz.com',
            'role' => 'purchasing',
        ]);

        $this->produksi = User::factory()->create([
            'name' => 'Staf Produksi',
            'email' => 'produksi@indofroz.com',
            'role' => 'produksi',
        ]);
    }

    /**
     * Flow 1: Master Data Management (Category, Unit, Warehouse, Location, Supplier, Barang)
     */
    public function test_01_master_data_creation_and_barangs(): array
    {
        // 1. Category
        $category = Category::create([
            'code' => Category::buatKodeOtomatis(),
            'name' => 'Frozen Seafood',
            'status' => 'active',
        ]);
        $this->assertDatabaseHas('categories', ['name' => 'Frozen Seafood']);

        // 2. Unit
        $unit = Unit::create([
            'code' => Unit::buatKodeOtomatis(),
            'name' => 'Kilogram',
            'status' => 'active',
        ]);
        $this->assertDatabaseHas('units', ['name' => 'Kilogram']);

        // 3. Warehouse Type & Warehouse
        $type = WarehouseType::create([
            'code' => WarehouseType::buatKodeOtomatis(),
            'name' => 'Cold Storage Facility',
            'status' => 'active',
        ]);

        $warehouse = Warehouse::create([
            'code' => Warehouse::buatKodeOtomatis(),
            'name' => 'Gudang Cold Storage Utama',
            'warehouse_type_id' => $type->id,
            'status' => 'active',
        ]);
        $this->assertDatabaseHas('warehouses', ['name' => 'Gudang Cold Storage Utama']);

        // 4. Locations (Rak A1 dan Rak B1)
        $locationA = Location::create([
            'warehouse_id' => $warehouse->id,
            'code' => 'RAK-A01',
            'name' => 'Rak Pembekuan A-01',
            'capacity' => 1000,
            'status' => 'active',
        ]);

        $locationB = Location::create([
            'warehouse_id' => $warehouse->id,
            'code' => 'RAK-B01',
            'name' => 'Rak Pembekuan B-01',
            'capacity' => 1000,
            'status' => 'active',
        ]);
        $this->assertDatabaseHas('locations', ['code' => 'RAK-A01']);

        // 5. Supplier
        $supplier = Supplier::create([
            'nama_supplier' => 'PT Tambak Udang Sejahtera',
            'kontak' => '08123456789',
            'email' => 'supplier@tambak.com',
            'alamat' => 'Sidoarjo, Jawa Timur',
            'is_active' => true,
        ]);
        $this->assertDatabaseHas('suppliers', ['nama_supplier' => 'PT Tambak Udang Sejahtera']);

        // 6. Barang (Katalog Produk)
        $barang = Barang::create([
            'kode_barang' => Barang::buatKodeOtomatis(),
            'nama_barang' => 'Udang Vaname Size 30',
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'location_id' => $locationA->id,
            'satuan' => 'Kg',
            'stok' => 0,
            'stok_minimum' => 50,
            'batas_umur_hari' => 180,
            'is_active' => true,
        ]);
        $this->assertDatabaseHas('barangs', ['nama_barang' => 'Udang Vaname Size 30', 'stok' => 0]);
        $this->assertTrue($barang->is_low_stock);

        return compact('category', 'unit', 'warehouse', 'locationA', 'locationB', 'supplier', 'barang');
    }

    /**
     * Flow 2: Procurement (PR diajukan Gudang -> PO dibuat Purchasing ke Supplier)
     * Flow 3: Inbound Receiving (Barang Masuk -> Stok bertambah + Batch + Ledger)
     * Flow 4: Outbound (Material Request Produksi -> Approval Gudang -> Stok berkurang via FIFO)
     * Flow 5: Stock Mutation (Pindah Rak A ke Rak B -> Kuantitas teralokasi)
     * Flow 6: Stock Opname & Adjustment (Cek fisik -> Koreksi dengan alasan wajib)
     * Flow 7: Reporting (Laporan Gudang, PO, Produksi & Export CSV)
     */
    public function test_full_wms_business_workflow(): void
    {
        $fixtures = $this->test_01_master_data_creation_and_barangs();
        $barang = $fixtures['barang'];
        $supplier = $fixtures['supplier'];
        $locationA = $fixtures['locationA'];
        $locationB = $fixtures['locationB'];

        // --- STEP 1: PENGADAAN (PR OLEH STAF GUDANG) ---
        $this->actingAs($this->gudang);
        $responsePR = $this->post(route('permintaan-pembelian.store'), [
            'items' => [
                ['barang_id' => $barang->id, 'jumlah' => 100],
            ],
        ]);
        $responsePR->assertRedirect(route('permintaan-pembelian.index'));
        $this->assertDatabaseHas('permintaan_pembelian', ['status' => 'pending']);
        $pr = PermintaanPembelian::first();
        $this->assertNotNull($pr);
        $this->assertStringStartsWith('PR-', $pr->no_pr);

        // --- STEP 2: PURCHASE ORDER (PO OLEH PURCHASING) ---
        $this->actingAs($this->purchasing);
        $responsePO = $this->post(route('purchase-orders.store'), [
            'pp_id' => $pr->id,
            'supplier_id' => $supplier->id,
            'syarat_pembayaran' => 'Net 30 Days',
            'catatan' => 'Pesanan Bahan Baku Udang Segar',
            'pajak_persen' => 11,
            'diskon' => 0,
            'items' => [
                [
                    'barang_id' => $barang->id,
                    'jumlah' => 100,
                    'harga_satuan' => 75000,
                ],
            ],
        ]);
        $responsePO->assertRedirect(route('purchase-orders.index'));

        // Verifikasi PR status terupdate menjadi 'di_po'
        $this->assertEquals('di_po', $pr->fresh()->status);

        $po = PurchaseOrder::first();
        $this->assertNotNull($po);
        $this->assertEquals('dikirim', $po->status);
        $this->assertEquals(7500000, $po->subtotal); // 100 * 75,000
        $this->assertEquals(825000, $po->pajak_nominal); // 11% PPN
        $this->assertEquals(8325000, $po->total_harga); // Subtotal + Pajak

        // Cek halaman cetak PO
        $responsePrint = $this->get(route('purchase-orders.print', $po->id));
        $responsePrint->assertStatus(200);

        // --- STEP 3: PENERIMAAN BARANG MASUK (INBOUND RECEIVING OLEH GUDANG) ---
        $this->actingAs($this->gudang);
        $responseInbound = $this->post(route('barang-masuk.store'), [
            'po_id' => $po->id,
            'no_surat_jalan' => 'SJ-SUPP-99881',
            'tanggal' => now()->toDateString(),
            'items' => [
                ['barang_id' => $barang->id, 'jumlah' => 100],
            ],
        ]);
        $responseInbound->assertRedirect(route('barang-masuk.index'));

        // Cek status PO otomatis selesai karena kuantiti 100/100 terpenuhi
        $this->assertEquals('selesai', $po->fresh()->status);

        // Cek stok barang fisik bertambah
        $this->assertEquals(100, $barang->fresh()->stok);

        // Cek InventoryBatch tercipta otomatis
        $this->assertDatabaseHas('inventory_batches', [
            'barang_id' => $barang->id,
            'stok' => 100,
            'status' => 'active',
        ]);

        // Cek Transaksi Ledger masuk
        $this->assertDatabaseHas('inventory_transactions', [
            'barang_id' => $barang->id,
            'type' => 'IN',
            'jumlah' => 100,
        ]);

        // --- STEP 4: PERMINTAAN BAHAN PRODUKSI & PENGELUARAN (OUTBOUND FIFO) ---
        // Staf Produksi meminta 30 Kg
        $this->actingAs($this->produksi);
        $responseReq = $this->post(route('permintaan-barang.store'), [
            'items' => [
                ['barang_id' => $barang->id, 'jumlah' => 30],
            ],
        ]);
        $responseReq->assertRedirect(route('permintaan-barang.index'));
        $reqProduksi = PermintaanBarang::first();
        $this->assertEquals('pending', $reqProduksi->status);

        // Cek halaman cetak Dokumen Pengambilan Digital
        $responsePrint = $this->get(route('permintaan-barang.print', $reqProduksi->id));
        $responsePrint->assertStatus(200);
        $responsePrint->assertSee('BUKTI PENGELUARAN BARANG DIGITAL');

        // Staf Gudang Approve Permintaan Produksi
        $this->actingAs($this->gudang);
        $responseApprove = $this->post(route('permintaan-barang.approve', $reqProduksi->id));
        $responseApprove->assertRedirect(route('permintaan-barang.show', $reqProduksi->id));

        // Verifikasi status selesai, stok berkurang dari 100 -> 70
        $this->assertEquals('selesai', $reqProduksi->fresh()->status);
        $this->assertEquals(70, $barang->fresh()->stok);

        // Verifikasi BarangKeluar tercatat
        $this->assertDatabaseHas('barang_keluar', [
            'barang_id' => $barang->id,
            'jumlah' => 30,
            'permintaan_id' => $reqProduksi->id,
        ]);

        // --- STEP 5: MUTASI STOK ANTAR LOKASI RAK (RAK A -> RAK B) ---
        $this->actingAs($this->gudang);
        $responseMutasi = $this->post(route('stock-mutations.store'), [
            'tanggal' => now()->toDateString(),
            'catatan' => 'Pindah 20 kg ke Rak B-01 untuk reorganisasi ruang',
            'items' => [
                [
                    'barang_id' => $barang->id,
                    'lokasi_asal_id' => $locationA->id,
                    'lokasi_tujuan_id' => $locationB->id,
                    'jumlah' => 20,
                    'alasan' => 'Reorganisasi ruang rak pendingin',
                ],
            ],
        ]);
        $responseMutasi->assertRedirect(route('stock-mutations.index'));
        $this->assertDatabaseHas('stock_mutations', ['status' => 'posted']);

        // Stok total keseluruhan perusahaan tetap 70
        $this->assertEquals(70, $barang->fresh()->stok);

        // --- STEP 6: STOCK OPNAME & ADJUSTMENT ---
        // Lakukan Cek Fisik: Di sistem ada 70, hasil hitung fisik ternyata 68 (selisih -2 kg)
        $responseSO = $this->post(route('stock-opnames.store'), [
            'tanggal' => now()->toDateString(),
            'warehouse_id' => $fixtures['warehouse']->id,
            'location_id' => $locationA->id,
            'catatan' => 'Pemeriksaan rutin mingguan',
            'items' => [
                [
                    'barang_id' => $barang->id,
                    'stok_fisik' => 68,
                    'location_id' => $locationA->id,
                    'catatan' => 'Terjadi susut es/cairan 2 kg',
                ],
            ],
        ]);
        $responseSO->assertRedirect(route('stock-opnames.index'));
        $opname = StockOpname::first();
        $this->assertNotNull($opname);
        $this->assertEquals('completed', $opname->status);

        // Eksekusi Penyesuaian Stok (Stock Adjustment -2 Kg)
        $responseSA = $this->post(route('stock-adjustments.store'), [
            'tanggal' => now()->toDateString(),
            'stock_opname_id' => $opname->id,
            'catatan' => 'Penyesuaian susut timbangan',
            'items' => [
                [
                    'barang_id' => $barang->id,
                    'stok_adjustment' => -2,
                    'alasan' => 'Susut alami glazing es pada produk beku',
                ],
            ],
        ]);
        $responseSA->assertRedirect(route('stock-adjustments.index'));

        // Stok akhir barang terkoreksi menjadi 68
        $this->assertEquals(68, $barang->fresh()->stok);

        // --- STEP 7: LAPORAN & EKSPOR CSV ---
        $this->actingAs($this->admin);

        // Laporan Gudang
        $responseReportGudang = $this->get(route('reports.index', ['type' => 'gudang']));
        $responseReportGudang->assertStatus(200);

        // Laporan Pembelian
        $responseReportPO = $this->get(route('reports.index', ['type' => 'pembelian']));
        $responseReportPO->assertStatus(200);

        // Laporan Produksi
        $responseReportProd = $this->get(route('reports.index', ['type' => 'produksi']));
        $responseReportProd->assertStatus(200);

        // Ekspor CSV
        $responseExport = $this->get(route('reports.export', ['type' => 'gudang']));
        $responseExport->assertStatus(200);
        $responseExport->assertHeader('content-type', 'text/csv; charset=UTF-8');

        // --- STEP 8: DASHBOARD & AUDIT TRAIL ---
        $responseDashboard = $this->get(route('dashboard'));
        $responseDashboard->assertStatus(200);

        $responseAudits = $this->get(route('audit-logs.index'));
        $responseAudits->assertStatus(200);
    }
}
