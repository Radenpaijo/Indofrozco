<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PermintaanBarangController;
use App\Http\Controllers\PermintaanPembelianController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\BarangKeluarController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\StockMutationController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- MASTER DATA ROUTES ---
    Route::resource('categories', CategoryController::class);
    Route::resource('units', UnitController::class);
    Route::resource('warehouses', WarehouseController::class);
    Route::resource('locations', LocationController::class);

    // --- BARANG ROUTES ---
    Route::get('/barangs', [BarangController::class, 'index'])->name('barangs.index');
    Route::get('/barangs/create', [BarangController::class, 'create'])->name('barangs.create')->middleware('role:admin,gudang');
    Route::post('/barangs', [BarangController::class, 'store'])->name('barangs.store')->middleware('role:admin,gudang');
    Route::get('/barangs/{barang}', [BarangController::class, 'show'])->name('barangs.show');
    Route::get('/barangs/{barang}/edit', [BarangController::class, 'edit'])->name('barangs.edit')->middleware('role:admin,gudang');
    Route::put('/barangs/{barang}', [BarangController::class, 'update'])->name('barangs.update')->middleware('role:admin,gudang');
    Route::delete('/barangs/{barang}', [BarangController::class, 'destroy'])->name('barangs.destroy')->middleware('role:admin,gudang');

    // --- SUPPLIER ROUTES ---
    Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::get('/suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create')->middleware('role:admin,purchasing');
    Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store')->middleware('role:admin,purchasing');
    Route::get('/suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit')->middleware('role:admin,purchasing');
    Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update')->middleware('role:admin,purchasing');
    Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy')->middleware('role:admin,purchasing');

    // --- PERMINTAAN BARANG (PRODUKSI) ---
    Route::get('/permintaan-barang', [PermintaanBarangController::class, 'index'])->name('permintaan-barang.index');
    Route::get('/permintaan-barang/create', [PermintaanBarangController::class, 'create'])->name('permintaan-barang.create')->middleware('role:admin,produksi');
    Route::post('/permintaan-barang', [PermintaanBarangController::class, 'store'])->name('permintaan-barang.store')->middleware('role:admin,produksi');
    Route::get('/permintaan-barang/{permintaan}', [PermintaanBarangController::class, 'show'])->name('permintaan-barang.show');
    Route::post('/permintaan-barang/{permintaan}/approve', [PermintaanBarangController::class, 'approve'])->name('permintaan-barang.approve')->middleware('role:admin,gudang');
    Route::post('/permintaan-barang/{permintaan}/reject', [PermintaanBarangController::class, 'reject'])->name('permintaan-barang.reject')->middleware('role:admin,gudang');

    // --- PERMINTAAN PEMBELIAN (PR) ---
    Route::get('/permintaan-pembelian', [PermintaanPembelianController::class, 'index'])->name('permintaan-pembelian.index');
    Route::get('/permintaan-pembelian/create', [PermintaanPembelianController::class, 'create'])->name('permintaan-pembelian.create')->middleware('role:admin,gudang');
    Route::post('/permintaan-pembelian', [PermintaanPembelianController::class, 'store'])->name('permintaan-pembelian.store')->middleware('role:admin,gudang');
    Route::get('/permintaan-pembelian/{pr}', [PermintaanPembelianController::class, 'show'])->name('permintaan-pembelian.show');
    Route::post('/permintaan-pembelian/{pr}/cancel', [PermintaanPembelianController::class, 'cancel'])->name('permintaan-pembelian.cancel')->middleware('role:admin,gudang');

    // --- PURCHASE ORDERS (PO) ---
    Route::get('/purchase-orders', [PurchaseOrderController::class, 'index'])->name('purchase-orders.index');
    Route::get('/purchase-orders/create', [PurchaseOrderController::class, 'create'])->name('purchase-orders.create')->middleware('role:admin,purchasing');
    Route::post('/purchase-orders', [PurchaseOrderController::class, 'store'])->name('purchase-orders.store')->middleware('role:admin,purchasing');
    Route::get('/purchase-orders/{po}', [PurchaseOrderController::class, 'show'])->name('purchase-orders.show');
    Route::get('/purchase-orders/{po}/print', [PurchaseOrderController::class, 'print'])->name('purchase-orders.print');
    Route::post('/purchase-orders/{po}/cancel', [PurchaseOrderController::class, 'cancel'])->name('purchase-orders.cancel')->middleware('role:admin,purchasing');

    // --- BARANG MASUK (RECEIPT) ---
    Route::get('/barang-masuk', [BarangMasukController::class, 'index'])->name('barang-masuk.index');
    Route::get('/barang-masuk/create', [BarangMasukController::class, 'create'])->name('barang-masuk.create')->middleware('role:admin,gudang');
    Route::post('/barang-masuk', [BarangMasukController::class, 'store'])->name('barang-masuk.store')->middleware('role:admin,gudang');

    // --- BARANG KELUAR (ISSUE) ---
    Route::get('/barang-keluar', [BarangKeluarController::class, 'index'])->name('barang-keluar.index');
    Route::get('/barang-keluar/create', [BarangKeluarController::class, 'create'])->name('barang-keluar.create')->middleware('role:admin,gudang');
    Route::post('/barang-keluar', [BarangKeluarController::class, 'store'])->name('barang-keluar.store')->middleware('role:admin,gudang');

    // --- INVENTORY OPERATIONS ---
    Route::resource('stock-opnames', StockOpnameController::class)->only(['index', 'create', 'store', 'show']);
    Route::resource('stock-adjustments', StockAdjustmentController::class)->only(['index', 'create', 'store', 'show']);
    Route::resource('stock-mutations', StockMutationController::class)->only(['index', 'create', 'store', 'show']);
    Route::resource('audit-logs', AuditLogController::class)->only(['index', 'show']);

    // --- USER MANAGEMENT (ADMIN / OWNER ONLY) ---
    Route::resource('users', UserController::class)->middleware('role:admin,owner');

    // --- REPORTS ---
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/print', [ReportController::class, 'print'])->name('reports.print');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
});

require __DIR__.'/auth.php';
