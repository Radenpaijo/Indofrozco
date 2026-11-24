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
use App\Http\Controllers\WarehouseTypeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ShortcutController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware(['auth'])->group(function () {
    // Redirect legacy /admin to Dashboard
    Route::redirect('/admin', '/dashboard');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- MASTER DATA ROUTES ---
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index')->middleware('permission:categories');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create')->middleware('permission:categories_manage');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store')->middleware('permission:categories_manage');
    Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show')->middleware('permission:categories');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit')->middleware('permission:categories_manage');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update')->middleware('permission:categories_manage');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy')->middleware('permission:categories_manage');
    Route::patch('categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('categories.toggle-status')->middleware('permission:categories_manage');

    Route::get('/units', [UnitController::class, 'index'])->name('units.index')->middleware('permission:units');
    Route::get('/units/create', [UnitController::class, 'create'])->name('units.create')->middleware('permission:units_manage');
    Route::post('/units', [UnitController::class, 'store'])->name('units.store')->middleware('permission:units_manage');
    Route::get('/units/{unit}', [UnitController::class, 'show'])->name('units.show')->middleware('permission:units');
    Route::get('/units/{unit}/edit', [UnitController::class, 'edit'])->name('units.edit')->middleware('permission:units_manage');
    Route::put('/units/{unit}', [UnitController::class, 'update'])->name('units.update')->middleware('permission:units_manage');
    Route::delete('/units/{unit}', [UnitController::class, 'destroy'])->name('units.destroy')->middleware('permission:units_manage');
    Route::patch('units/{unit}/toggle-status', [UnitController::class, 'toggleStatus'])->name('units.toggle-status')->middleware('permission:units_manage');

    Route::resource('warehouse-types', WarehouseTypeController::class);
    Route::patch('warehouse-types/{warehouse_type}/toggle-status', [WarehouseTypeController::class, 'toggleStatus'])->name('warehouse-types.toggle-status');

    Route::get('/warehouses', [WarehouseController::class, 'index'])->name('warehouses.index')->middleware('permission:warehouses');
    Route::get('/warehouses/create', [WarehouseController::class, 'create'])->name('warehouses.create')->middleware('permission:warehouses_manage');
    Route::post('/warehouses', [WarehouseController::class, 'store'])->name('warehouses.store')->middleware('permission:warehouses_manage');
    Route::get('/warehouses/{warehouse}', [WarehouseController::class, 'show'])->name('warehouses.show')->middleware('permission:warehouses');
    Route::get('/warehouses/{warehouse}/edit', [WarehouseController::class, 'edit'])->name('warehouses.edit')->middleware('permission:warehouses_manage');
    Route::put('/warehouses/{warehouse}', [WarehouseController::class, 'update'])->name('warehouses.update')->middleware('permission:warehouses_manage');
    Route::delete('/warehouses/{warehouse}', [WarehouseController::class, 'destroy'])->name('warehouses.destroy')->middleware('permission:warehouses_manage');
    Route::patch('warehouses/{warehouse}/toggle-status', [WarehouseController::class, 'toggleStatus'])->name('warehouses.toggle-status')->middleware('permission:warehouses_manage');

    Route::get('/locations', [LocationController::class, 'index'])->name('locations.index')->middleware('permission:locations');
    Route::get('/locations/create', [LocationController::class, 'create'])->name('locations.create')->middleware('permission:locations_manage');
    Route::post('/locations', [LocationController::class, 'store'])->name('locations.store')->middleware('permission:locations_manage');
    Route::get('/locations/{location}', [LocationController::class, 'show'])->name('locations.show')->middleware('permission:locations');
    Route::get('/locations/{location}/edit', [LocationController::class, 'edit'])->name('locations.edit')->middleware('permission:locations_manage');
    Route::put('/locations/{location}', [LocationController::class, 'update'])->name('locations.update')->middleware('permission:locations_manage');
    Route::delete('/locations/{location}', [LocationController::class, 'destroy'])->name('locations.destroy')->middleware('permission:locations_manage');
    Route::patch('locations/{location}/toggle-status', [LocationController::class, 'toggleStatus'])->name('locations.toggle-status')->middleware('permission:locations_manage');

    // --- BARANG ROUTES ---
    Route::get('/barangs', [BarangController::class, 'index'])->name('barangs.index')->middleware('permission:barangs');
    Route::get('/barangs/create', [BarangController::class, 'create'])->name('barangs.create')->middleware('permission:barangs_create');
    Route::post('/barangs', [BarangController::class, 'store'])->name('barangs.store')->middleware('permission:barangs_create');
    Route::get('/barangs/{barang}', [BarangController::class, 'show'])->name('barangs.show')->middleware('permission:barangs');
    Route::get('/barangs/{barang}/stock-card', [BarangController::class, 'stockCard'])->name('barangs.stock-card');
    Route::get('/barangs/{barang}/stock-card/print', [BarangController::class, 'printStockCard'])->name('barangs.stock-card.print');
    Route::get('/barangs/{barang}/edit', [BarangController::class, 'edit'])->name('barangs.edit')->middleware('permission:barangs_edit');
    Route::put('/barangs/{barang}', [BarangController::class, 'update'])->name('barangs.update')->middleware('permission:barangs_edit');
    Route::delete('/barangs/{barang}', [BarangController::class, 'destroy'])->name('barangs.destroy')->middleware('permission:barangs_delete');

    // --- SUPPLIER ROUTES ---
    Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index')->middleware('permission:suppliers');
    Route::get('/suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create')->middleware('permission:suppliers_manage');
    Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store')->middleware('permission:suppliers_manage');
    Route::get('/suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit')->middleware('permission:suppliers_manage');
    Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update')->middleware('permission:suppliers_manage');
    Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy')->middleware('permission:suppliers_manage');

    // --- PERMINTAAN BARANG (PRODUKSI) ---
    Route::get('/permintaan-barang', [PermintaanBarangController::class, 'index'])->name('permintaan-barang.index');
    Route::get('/permintaan-barang/create', [PermintaanBarangController::class, 'create'])->name('permintaan-barang.create')->middleware('role:admin,produksi');
    Route::post('/permintaan-barang', [PermintaanBarangController::class, 'store'])->name('permintaan-barang.store')->middleware('role:admin,produksi');
    Route::get('/permintaan-barang/{permintaan}', [PermintaanBarangController::class, 'show'])->name('permintaan-barang.show');
    Route::get('/permintaan-barang/{permintaan}/print', [PermintaanBarangController::class, 'print'])->name('permintaan-barang.print');
    Route::post('/permintaan-barang/{permintaan}/approve', [PermintaanBarangController::class, 'approve'])->name('permintaan-barang.approve')->middleware('role:admin,gudang');
    Route::post('/permintaan-barang/{permintaan}/reject', [PermintaanBarangController::class, 'reject'])->name('permintaan-barang.reject')->middleware('role:admin,gudang');
    Route::post('/permintaan-barang/{permintaan}/verify-qc', [PermintaanBarangController::class, 'verifyQc'])->name('permintaan-barang.verify-qc')->middleware('role:admin,produksi,gudang');

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

    // --- KEYBOARD SHORTCUTS ---
    Route::get('/shortcuts', [ShortcutController::class, 'index'])->name('shortcuts.index');
});

require __DIR__ . '/auth.php';
