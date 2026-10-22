<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4 min-w-0">
            <x-back-button />
            <div class="min-w-0">
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    {{ __('Form Sesi Stock Opname Baru') }}
                </h2>
                <p class="text-xs text-slate-500 mt-1">Input data stok hasil audit fisik di lapangan.</p>
            </div>
        </div>
    </x-slot>

    <div class="w-full">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-200/80 p-6">
                <form action="{{ route('stock-opnames.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Tanggal Sesi</label>
                            <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required class="w-full rounded-xl border-slate-300 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Gudang</label>
                            <select name="warehouse_id" class="w-full rounded-xl border-slate-300 text-sm">
                                <option value="">Semua Gudang</option>
                                @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Lokasi / Rak</label>
                            <select name="location_id" class="w-full rounded-xl border-slate-300 text-sm">
                                <option value="">Semua Rak</option>
                                @foreach($locations as $loc)
                                    <option value="{{ $loc->id }}">{{ $loc->code }} - {{ $loc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Catatan Opname</label>
                        <input type="text" name="catatan" class="w-full rounded-xl border-slate-300 text-sm" placeholder="Contoh: Opname rutin akhir bulan September">
                    </div>

                    <hr class="border-slate-200" />

                    <div class="space-y-4">
                        <h3 class="font-bold text-slate-800 text-base">Detail Perhitungan Fisik</h3>
                        
                        <div class="overflow-x-auto border border-slate-300 rounded-xl">
                            <table class="w-full text-left border-collapse" id="opname-table">
                                <thead>
                                    <tr class="bg-slate-100 border-b border-slate-300 text-[11px] font-bold uppercase tracking-wider text-slate-700">
                                        <th class="py-3.5 px-4 border-r border-slate-300">Barang / Item</th>
                                        <th class="py-3.5 px-4 border-r border-slate-300">Batch / Lot (Opsional)</th>
                                        <th class="py-3.5 px-4 border-r border-slate-300">Stok Sistem</th>
                                        <th class="py-3.5 px-4 border-r border-slate-300">Stok Fisik</th>
                                        <th class="py-3.5 px-4">Catatan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 text-sm">
                                    @foreach($barangs as $index => $b)
                                        <tr class="hover:bg-slate-50">
                                            <td class="py-3.5 px-4 border-r border-slate-200">
                                                <input type="hidden" name="items[{{ $index }}][barang_id]" value="{{ $b->id }}">
                                                <span class="font-bold text-slate-800">{{ $b->nama_barang }}</span>
                                                <span class="text-xs text-slate-400 block font-mono">{{ $b->kode_barang }}</span>
                                            </td>
                                            <td class="py-3.5 px-4 border-r border-slate-200">
                                                <select name="items[{{ $index }}][batch_id]" class="w-full rounded-lg border-slate-300 text-xs">
                                                    <option value="">Semua Batch (Stok Total)</option>
                                                    @foreach($b->batches as $bt)
                                                        <option value="{{ $bt->id }}">{{ $bt->batch_number }} (Stok: {{ $bt->stok }})</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="py-3.5 px-4 border-r border-slate-200 font-semibold text-slate-600">
                                                {{ $b->stok }} {{ $b->satuan }}
                                            </td>
                                            <td class="py-3.5 px-4 border-r border-slate-200">
                                                <input type="number" step="0.01" min="0" name="items[{{ $index }}][stok_fisik]" value="{{ $b->stok }}" required class="w-32 rounded-lg border-slate-300 text-sm font-semibold">
                                            </td>
                                            <td class="py-3.5 px-4">
                                                <input type="text" name="items[{{ $index }}][catatan]" class="w-full rounded-lg border-slate-300 text-xs" placeholder="Kondisi kemasan / kemiringan">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                        <a href="{{ route('stock-opnames.index') }}" class="px-4 py-2 text-sm font-semibold text-slate-600 hover:text-slate-900">Batal</a>
                        <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition shadow-md shadow-indigo-600/20">
                            Simpan Stock Opname
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
