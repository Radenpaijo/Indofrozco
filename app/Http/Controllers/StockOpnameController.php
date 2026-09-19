<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\InventoryBatch;
use App\Models\Location;
use App\Models\StockOpname;
use App\Models\StockOpnameDetail;
use App\Models\Warehouse;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockOpnameController extends Controller
{
    public function index()
    {
        $opnames = StockOpname::with(['warehouse', 'location', 'user'])->latest()->paginate(15);
        return view('stock-opnames.index', compact('opnames'));
    }

    public function create()
    {
        $warehouses = Warehouse::where('status', 'active')->get();
        $locations = Location::where('status', 'active')->get();
        $barangs = Barang::with('batches')->where('is_active', true)->get();

        return view('stock-opnames.create', compact('warehouses', 'locations', 'barangs'));
    }

    public function store(Request $request, InventoryService $inventoryService)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'location_id' => 'nullable|exists:locations,id',
            'catatan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barangs,id',
            'items.*.batch_id' => 'nullable|exists:inventory_batches,id',
            'items.*.stok_fisik' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $inventoryService) {
            $nomor = 'SO-' . date('Ymd') . '-' . str_pad(StockOpname::count() + 1, 4, '0', STR_PAD_LEFT);

            $opname = StockOpname::create([
                'nomor_opname' => $nomor,
                'tanggal' => $request->tanggal,
                'warehouse_id' => $request->warehouse_id,
                'location_id' => $request->location_id,
                'user_id' => auth()->id(),
                'status' => 'completed',
                'catatan' => $request->catatan,
            ]);

            foreach ($request->items as $item) {
                $barang = Barang::findOrFail($item['barang_id']);
                $batchId = $item['batch_id'] ?? null;
                
                if ($batchId) {
                    $batch = InventoryBatch::find($batchId);
                    $stokSistem = $batch ? $batch->stok : 0;
                } else {
                    $stokSistem = $barang->stok;
                }

                $stokFisik = (float)$item['stok_fisik'];
                $selisih = $stokFisik - $stokSistem;

                StockOpnameDetail::create([
                    'stock_opname_id' => $opname->id,
                    'barang_id' => $barang->id,
                    'batch_id' => $batchId,
                    'location_id' => $item['location_id'] ?? $barang->location_id,
                    'stok_sistem' => $stokSistem,
                    'stok_fisik' => $stokFisik,
                    'selisih' => $selisih,
                    'catatan' => $item['catatan'] ?? null,
                ]);
            }

            $inventoryService->logAudit(
                auth()->id(),
                'CREATE',
                'STOCK_OPNAME',
                $opname->id,
                null,
                ['nomor' => $opname->nomor_opname, 'item_count' => count($request->items)]
            );
        });

        return redirect()->route('stock-opnames.index')->with('success', 'Sesi Stock Opname berhasil dibuat.');
    }

    public function show(StockOpname $stockOpname)
    {
        $stockOpname->load(['details.barang', 'details.batch', 'details.location', 'user', 'warehouse']);
        return view('stock-opnames.show', compact('stockOpname'));
    }
}
