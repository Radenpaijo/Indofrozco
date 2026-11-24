<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Location;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function index(Request $permintaan)
    {
        $kueri = Barang::with(['category', 'unit', 'location.warehouse']);

        if ($permintaan->filled('search')) {
            $pencarian = $permintaan->search;
            $kueri->where(function($q) use ($pencarian) {
                $q->where('nama_barang', 'like', "%{$pencarian}%")
                  ->orWhere('kode_barang', 'like', "%{$pencarian}%")
                  ->orWhere('kode_rak', 'like', "%{$pencarian}%");
            });
        }

        if ($permintaan->filled('category_id')) {
            $kueri->where('category_id', $permintaan->category_id);
        }

        if ($permintaan->filled('unit_id')) {
            $kueri->where('unit_id', $permintaan->unit_id);
        }

        if ($permintaan->filled('warehouse_id')) {
            $warehouseId = $permintaan->warehouse_id;
            $kueri->whereHas('location', function($lq) use ($warehouseId) {
                $lq->where('warehouse_id', $warehouseId);
            });
        }

        if ($permintaan->filled('location_id')) {
            $kueri->where('location_id', $permintaan->location_id);
        }

        if ($permintaan->filled('status')) {
            if ($permintaan->status === 'low_stock') {
                $kueri->where('stok', '<=', \DB::raw('stok_minimum'));
            } elseif ($permintaan->status === 'active') {
                $kueri->where('is_active', true);
            } elseif ($permintaan->status === 'inactive') {
                $kueri->where('is_active', false);
            }
        }

        $barangs = $kueri->orderBy('kode_barang', 'asc')->paginate(10)->withQueryString();
        $categories = Category::where('status', 'active')->get();
        $units = Unit::where('status', 'active')->get();
        $warehouses = \App\Models\Warehouse::where('status', 'active')->get();
        $locations = \App\Models\Location::with('warehouse')->where('status', 'active')->get();

        return view('barangs.index', compact('barangs', 'categories', 'units', 'warehouses', 'locations'));
    }

    public function create()
    {
        $categories = Category::where('status', 'active')->get();
        $units = Unit::where('status', 'active')->get();
        $locations = Location::with('warehouse')->where('status', 'active')->get();
        $nextKodeBarang = Barang::buatKodeOtomatis();

        return view('barangs.create', compact('categories', 'units', 'locations', 'nextKodeBarang'));
    }

    public function store(Request $permintaan)
    {
        if (!$permintaan->filled('kode_barang')) {
            $permintaan->merge(['kode_barang' => Barang::buatKodeOtomatis()]);
        }
        if (!$permintaan->filled('satuan') && $permintaan->filled('unit_id')) {
            $unit = Unit::find($permintaan->unit_id);
            if ($unit) {
                $permintaan->merge(['satuan' => $unit->name]);
            }
        }

        $permintaan->validate([
            'kode_barang' => 'required|unique:barangs,kode_barang',
            'nama_barang' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'location_id' => 'nullable|exists:locations,id',
            'satuan' => 'required|string|max:50',
            'stok_minimum' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
        ], [
            'kode_barang.required' => 'Kode barang wajib diisi.',
            'kode_barang.unique' => 'Kode barang sudah digunakan, gunakan kode lain.',
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'category_id.required' => 'Kelompok barang wajib dipilih.',
            'unit_id.required' => 'Satuan barang wajib dipilih.',
        ]);

        $lokasiGudang = null;
        $kodeRak = null;
        if ($permintaan->filled('location_id')) {
            $loc = Location::with('warehouse')->find($permintaan->location_id);
            if ($loc) {
                if ($permintaan->filled('stok_minimum') && $loc->capacity !== null && (float)$loc->capacity > 0) {
                    $stokMin = (float)$permintaan->stok_minimum;
                    $maxCap = (float)$loc->capacity;
                    if ($stokMin > $maxCap) {
                        return back()->withInput()->withErrors([
                            'stok_minimum' => "Batas stok minimum ({$stokMin}) tidak boleh melebihi kapasitas maksimal rak {$loc->code} (" . number_format($maxCap) . ").",
                            'location_id' => "Kapasitas maksimal rak {$loc->code} hanya " . number_format($maxCap) . ", tidak mencukupi untuk batas stok minimum ({$stokMin}).",
                        ]);
                    }
                }
                $lokasiGudang = $loc->warehouse->name ?? 'Gudang Utama';
                $kodeRak = $loc->code;
            }
        }

        $dataBarang = [
            'kode_barang' => $permintaan->kode_barang,
            'nama_barang' => $permintaan->nama_barang,
            'category_id' => $permintaan->category_id,
            'unit_id' => $permintaan->unit_id,
            'location_id' => $permintaan->location_id ?: null,
            'lokasi_gudang' => $lokasiGudang,
            'kode_rak' => $kodeRak,
            'satuan' => $permintaan->satuan,
            'stok_minimum' => $permintaan->stok_minimum ?? 0,
            'description' => $permintaan->description,
            'stok' => 0,
            'batas_umur_hari' => 0,
            'is_active' => true,
        ];

        $barang = Barang::create($dataBarang);

        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'CREATE_ITEM',
            'module' => 'MASTER_BARANG',
            'record_id' => (string)$barang->id,
            'new_values' => $dataBarang,
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('barangs.index')->with('success', 'Barang berhasil ditambahkan!');
    }

    public function edit(Barang $barang)
    {
        $categories = Category::where('status', 'active')->get();
        $units = Unit::where('status', 'active')->get();
        $locations = Location::with('warehouse')->where('status', 'active')->get();

        return view('barangs.edit', compact('barang', 'categories', 'units', 'locations'));
    }

    public function update(Request $permintaan, Barang $barang)
    {
        $permintaan->merge(['kode_barang' => $barang->kode_barang]);

        if (!$permintaan->filled('satuan') && $permintaan->filled('unit_id')) {
            $unit = Unit::find($permintaan->unit_id);
            if ($unit) {
                $permintaan->merge(['satuan' => $unit->name]);
            }
        }

        $permintaan->validate([
            'kode_barang' => 'required|unique:barangs,kode_barang,' . $barang->id,
            'nama_barang' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'location_id' => 'nullable|exists:locations,id',
            'satuan' => 'required|string|max:50',
            'stok_minimum' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
        ], [
            'kode_barang.required' => 'Kode barang wajib diisi.',
            'kode_barang.unique' => 'Kode barang sudah digunakan, gunakan kode lain.',
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'category_id.required' => 'Kelompok barang wajib dipilih.',
            'unit_id.required' => 'Satuan barang wajib dipilih.',
        ]);

        $lokasiGudang = $barang->lokasi_gudang;
        $kodeRak = $barang->kode_rak;
        if ($permintaan->filled('location_id')) {
            $loc = Location::with('warehouse')->find($permintaan->location_id);
            if ($loc) {
                if ($permintaan->filled('stok_minimum') && $loc->capacity !== null && (float)$loc->capacity > 0) {
                    $stokMin = (float)$permintaan->stok_minimum;
                    $maxCap = (float)$loc->capacity;
                    if ($stokMin > $maxCap) {
                        return back()->withInput()->withErrors([
                            'stok_minimum' => "Batas stok minimum ({$stokMin}) tidak boleh melebihi kapasitas maksimal rak {$loc->code} (" . number_format($maxCap) . ").",
                            'location_id' => "Kapasitas maksimal rak {$loc->code} hanya " . number_format($maxCap) . ", tidak mencukupi untuk batas stok minimum ({$stokMin}).",
                        ]);
                    }
                }
                $lokasiGudang = $loc->warehouse->name ?? 'Gudang Utama';
                $kodeRak = $loc->code;
            }
        } elseif ($permintaan->has('location_id') && empty($permintaan->location_id)) {
            $lokasiGudang = null;
            $kodeRak = null;
        }

        $dataLama = $barang->toArray();
        $dataBarang = [
            'kode_barang' => $permintaan->kode_barang,
            'nama_barang' => $permintaan->nama_barang,
            'category_id' => $permintaan->category_id,
            'unit_id' => $permintaan->unit_id,
            'location_id' => $permintaan->location_id ?: null,
            'lokasi_gudang' => $lokasiGudang,
            'kode_rak' => $kodeRak,
            'satuan' => $permintaan->satuan,
            'stok_minimum' => $permintaan->stok_minimum ?? 0,
            'description' => $permintaan->description,
            'is_active' => $permintaan->has('is_active'),
        ];

        $barang->update($dataBarang);

        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'UPDATE_ITEM',
            'module' => 'MASTER_BARANG',
            'record_id' => (string)$barang->id,
            'old_values' => $dataLama,
            'new_values' => $barang->fresh()->toArray(),
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('barangs.index')->with('success', 'Barang berhasil diperbarui!');
    }

    public function destroy(Barang $barang)
    {
        if ($barang->barangMasuks()->exists() || $barang->barangKeluars()->exists()) {
            $barang->update(['is_active' => false]);

            \App\Models\AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'DEACTIVATE_ITEM',
                'module' => 'MASTER_BARANG',
                'record_id' => (string)$barang->id,
                'new_values' => ['is_active' => false],
                'ip_address' => request()->ip(),
            ]);

            return redirect()->route('barangs.index')->with('warning', 'Barang memiliki riwayat transaksi. Status dinonaktifkan alih-alih dihapus.');
        }

        $barangId = $barang->id;
        $barang->delete();

        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'DELETE_ITEM',
            'module' => 'MASTER_BARANG',
            'record_id' => (string)$barangId,
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('barangs.index')->with('success', 'Barang berhasil dihapus!');
    }

    public function show(Barang $barang)
    {
        $barangMasuk = $barang->barangMasuks()->with('user')->orderBy('tanggal', 'desc')->get();
        $barangKeluar = $barang->barangKeluars()->with('user')->orderBy('tanggal', 'desc')->get();
        $expiringBatches = $barang->dapatkanBatchKadaluarsa();

        return view('barangs.show', compact('barang', 'barangMasuk', 'barangKeluar', 'expiringBatches'));
    }

    public function stockCard(Barang $barang, Request $request)
    {
        $startDate = $request->query('start_date', now()->subMonths(3)->toDateString());
        $endDate = $request->query('end_date', now()->toDateString());

        // Transaksi dari ledger
        $transactions = \App\Models\InventoryTransaction::with(['batch', 'user'])
            ->where('barang_id', $barang->id)
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $initialIn = \App\Models\InventoryTransaction::where('barang_id', $barang->id)
            ->whereDate('created_at', '<', $startDate)
            ->where('type', 'IN')
            ->sum('jumlah');

        $initialOut = \App\Models\InventoryTransaction::where('barang_id', $barang->id)
            ->whereDate('created_at', '<', $startDate)
            ->where('type', 'OUT')
            ->sum('jumlah');

        $initialBalance = max(0, $initialIn - $initialOut);

        // Fallback jika belum ada di tabel ledger
        if ($transactions->isEmpty() && ($barang->barangMasuks()->exists() || $barang->barangKeluars()->exists())) {
            $masuks = $barang->barangMasuks()->whereDate('tanggal', '>=', $startDate)->whereDate('tanggal', '<=', $endDate)->get()->map(function($m) {
                return (object)[
                    'id' => 'm_' . $m->id,
                    'created_at' => $m->tanggal,
                    'type' => 'IN',
                    'jumlah' => $m->jumlah,
                    'nomor_referensi' => $m->no_masuk,
                    'catatan' => $m->batch_number ? "Lot: {$m->batch_number}" : ($m->no_surat_jalan ? "SJ: {$m->no_surat_jalan}" : 'Penerimaan'),
                    'batch' => (object)['batch_number' => $m->batch_number, 'tanggal_kadaluarsa' => $m->tanggal_kadaluarsa],
                ];
            });

            $keluars = $barang->barangKeluars()->whereDate('tanggal', '>=', $startDate)->whereDate('tanggal', '<=', $endDate)->get()->map(function($k) {
                return (object)[
                    'id' => 'k_' . $k->id,
                    'created_at' => $k->tanggal,
                    'type' => 'OUT',
                    'jumlah' => $k->jumlah,
                    'nomor_referensi' => 'OUT-' . $k->id,
                    'catatan' => $k->permintaanBarang ? "Permintaan: {$k->permintaanBarang->no_permintaan}" : 'Pengeluaran Produksi',
                    'batch' => null,
                ];
            });

            $transactions = $masuks->concat($keluars)->sortBy('created_at')->values();
        }

        return view('barangs.stock-card', compact('barang', 'transactions', 'initialBalance', 'startDate', 'endDate'));
    }

    public function printStockCard(Barang $barang, Request $request)
    {
        $startDate = $request->query('start_date', now()->subMonths(3)->toDateString());
        $endDate = $request->query('end_date', now()->toDateString());

        $transactions = \App\Models\InventoryTransaction::with(['batch', 'user'])
            ->where('barang_id', $barang->id)
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $initialIn = \App\Models\InventoryTransaction::where('barang_id', $barang->id)
            ->whereDate('created_at', '<', $startDate)
            ->where('type', 'IN')
            ->sum('jumlah');

        $initialOut = \App\Models\InventoryTransaction::where('barang_id', $barang->id)
            ->whereDate('created_at', '<', $startDate)
            ->where('type', 'OUT')
            ->sum('jumlah');

        $initialBalance = max(0, $initialIn - $initialOut);

        if ($transactions->isEmpty() && ($barang->barangMasuks()->exists() || $barang->barangKeluars()->exists())) {
            $masuks = $barang->barangMasuks()->whereDate('tanggal', '>=', $startDate)->whereDate('tanggal', '<=', $endDate)->get()->map(function($m) {
                return (object)[
                    'id' => 'm_' . $m->id,
                    'created_at' => $m->tanggal,
                    'type' => 'IN',
                    'jumlah' => $m->jumlah,
                    'nomor_referensi' => $m->no_masuk,
                    'catatan' => $m->batch_number ? "Lot: {$m->batch_number}" : ($m->no_surat_jalan ? "SJ: {$m->no_surat_jalan}" : 'Penerimaan'),
                    'batch' => (object)['batch_number' => $m->batch_number, 'tanggal_kadaluarsa' => $m->tanggal_kadaluarsa],
                ];
            });

            $keluars = $barang->barangKeluars()->whereDate('tanggal', '>=', $startDate)->whereDate('tanggal', '<=', $endDate)->get()->map(function($k) {
                return (object)[
                    'id' => 'k_' . $k->id,
                    'created_at' => $k->tanggal,
                    'type' => 'OUT',
                    'jumlah' => $k->jumlah,
                    'nomor_referensi' => 'OUT-' . $k->id,
                    'catatan' => $k->permintaanBarang ? "Permintaan: {$k->permintaanBarang->no_permintaan}" : 'Pengeluaran Produksi',
                    'batch' => null,
                ];
            });

            $transactions = $masuks->concat($keluars)->sortBy('created_at')->values();
        }

        return view('barangs.stock-card-print', compact('barang', 'transactions', 'initialBalance', 'startDate', 'endDate'));
    }
}
