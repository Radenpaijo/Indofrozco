<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\InventoryBatch;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentDetail;
use App\Models\StockOpname;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockAdjustmentController extends Controller
{
    public function index()
    {
        $adjustments = StockAdjustment::with(['user', 'approver', 'stockOpname'])->latest()->paginate(15);
        return view('stock-adjustments.index', compact('adjustments'));
    }

    public function create()
    {
        $barangs = Barang::with('batches')->where('is_active', true)->get();
        $opnames = StockOpname::where('status', 'completed')->latest()->get();

        return view('stock-adjustments.create', compact('barangs', 'opnames'));
    }

    public function store(Request $request, InventoryService $inventoryService)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'stock_opname_id' => 'nullable|exists:stock_opnames,id',
            'catatan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barangs,id',
            'items.*.batch_id' => 'nullable|exists:inventory_batches,id',
            'items.*.stok_adjustment' => 'required|numeric', // bisa positif atau negatif
            'items.*.alasan' => 'required|string|max:255', // BR-006: Wajib Alasan
        ]);

        DB::transaction(function () use ($request, $inventoryService) {
            $nomor = 'SA-' . date('Ymd') . '-' . str_pad(StockAdjustment::count() + 1, 4, '0', STR_PAD_LEFT);

            $adjustment = StockAdjustment::create([
                'nomor_adjustment' => $nomor,
                'tanggal' => $request->tanggal,
                'stock_opname_id' => $request->stock_opname_id,
                'user_id' => auth()->id(),
                'status' => 'posted',
                'catatan' => $request->catatan,
            ]);

            foreach ($request->items as $item) {
                $barang = Barang::findOrFail($item['barang_id']);
                $batchId = $item['batch_id'] ?? null;
                $adjQty = (float)$item['stok_adjustment'];

                if ($batchId) {
                    $batch = InventoryBatch::find($batchId);
                    $stokSistem = $batch ? $batch->stok : 0;
                } else {
                    $stokSistem = $barang->stok;
                }

                $stokAkhir = $stokSistem + $adjQty;

                StockAdjustmentDetail::create([
                    'stock_adjustment_id' => $adjustment->id,
                    'barang_id' => $barang->id,
                    'batch_id' => $batchId,
                    'location_id' => $barang->location_id,
                    'stok_sistem' => $stokSistem,
                    'stok_adjustment' => $adjQty,
                    'stok_akhir' => $stokAkhir,
                    'alasan' => $item['alasan'],
                ]);

                // Eksekusi perubahan stok atomic lewat InventoryService
                $inventoryService->recordAdjustment(
                    $barang,
                    $batchId,
                    $barang->location_id,
                    $adjQty,
                    $item['alasan'],
                    auth()->id(),
                    $adjustment->id
                );
            }
        });

        return redirect()->route('stock-adjustments.index')->with('success', 'Stock Adjustment berhasil diposting dan stok telah diperbarui.');
    }

    public function show(StockAdjustment $stockAdjustment)
    {
        $stockAdjustment->load(['details.barang', 'details.batch', 'details.location', 'user']);
        return view('stock-adjustments.show', compact('stockAdjustment'));
    }
}
