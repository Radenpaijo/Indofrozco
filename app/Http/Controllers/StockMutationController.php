<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\InventoryBatch;
use App\Models\Location;
use App\Models\StockMutation;
use App\Models\StockMutationDetail;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockMutationController extends Controller
{
    public function index()
    {
        $mutations = StockMutation::with(['user', 'details.barang', 'details.lokasiAsal', 'details.lokasiTujuan'])->latest()->paginate(15);
        return view('stock-mutations.index', compact('mutations'));
    }

    public function create()
    {
        $barangs = Barang::with('batches')->where('is_active', true)->get();
        $locations = Location::with('warehouse')->where('status', 'active')->get();

        return view('stock-mutations.create', compact('barangs', 'locations'));
    }

    public function store(Request $request, InventoryService $inventoryService)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'catatan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barangs,id',
            'items.*.batch_id' => 'nullable|exists:inventory_batches,id',
            'items.*.lokasi_asal_id' => 'required|exists:locations,id',
            'items.*.lokasi_tujuan_id' => 'required|exists:locations,id|different:items.*.lokasi_asal_id',
            'items.*.jumlah' => 'required|numeric|gt:0',
            'items.*.alasan' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request, $inventoryService) {
            $nomor = 'MUT-' . date('Ymd') . '-' . str_pad(StockMutation::count() + 1, 4, '0', STR_PAD_LEFT);

            $mutation = StockMutation::create([
                'nomor_mutasi' => $nomor,
                'tanggal' => $request->tanggal,
                'user_id' => auth()->id(),
                'status' => 'posted',
                'catatan' => $request->catatan,
            ]);

            foreach ($request->items as $item) {
                $barang = Barang::findOrFail($item['barang_id']);
                $batchId = $item['batch_id'] ?? null;
                $qty = (float)$item['jumlah'];

                StockMutationDetail::create([
                    'stock_mutation_id' => $mutation->id,
                    'barang_id' => $barang->id,
                    'batch_id' => $batchId,
                    'lokasi_asal_id' => $item['lokasi_asal_id'],
                    'lokasi_tujuan_id' => $item['lokasi_tujuan_id'],
                    'jumlah' => $qty,
                    'alasan' => $item['alasan'] ?? null,
                ]);

                // Eksekusi mutasi perpindahan stok (Total quantity tidak berubah)
                $inventoryService->recordMutation(
                    $barang,
                    $batchId,
                    (int)$item['lokasi_asal_id'],
                    (int)$item['lokasi_tujuan_id'],
                    $qty,
                    $item['alasan'] ?? null,
                    auth()->id(),
                    $mutation->id
                );
            }
        });

        return redirect()->route('stock-mutations.index')->with('success', 'Mutasi stok berhasil diposting.');
    }

    public function show(StockMutation $stockMutation)
    {
        $stockMutation->load(['details.barang', 'details.batch', 'details.lokasiAsal', 'details.lokasiTujuan', 'user']);
        return view('stock-mutations.show', compact('stockMutation'));
    }
}
