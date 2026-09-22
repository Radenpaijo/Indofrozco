<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4 min-w-0">
            <x-back-button />
            <div class="min-w-0">
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    {{ __('Form Stock Adjustment Baru') }}
                </h2>
                <p class="text-xs text-slate-500 mt-1">Formulir penyesuaian selisih stok persediaan.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-200/80 p-6">
                <div class="mb-4 p-3 bg-amber-50 text-amber-800 border border-amber-200 rounded-xl text-xs">
                    <strong>Aturan BR-006:</strong> Setiap item penyesuaian stok <strong>WAJIB menyertakan Alasan (Reason)</strong> resmi yang valid (misal: Kerusakan kemasan, Hasil Opname, Barang Kadaluarsa).
                </div>

                <form action="{{ route('stock-adjustments.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Tanggal Adjustment</label>
                            <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required class="w-full rounded-xl border-slate-300 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Referensi Sesi Opname (Opsional)</label>
                            <select name="stock_opname_id" class="w-full rounded-xl border-slate-300 text-sm">
                                <option value="">-- Tanpa Opname (Penyesuaian Langsung) --</option>
                                @foreach($opnames as $op)
                                    <option value="{{ $op->id }}">{{ $op->nomor_opname }} ({{ $op->tanggal->format('d/m/Y') }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Catatan Dokumen</label>
                        <input type="text" name="catatan" class="w-full rounded-xl border-slate-300 text-sm" placeholder="Catatan opsional...">
                    </div>

                    <hr class="border-slate-200" />

                    <div class="space-y-4">
                        <h3 class="font-bold text-slate-800 text-base">Item Adjustment & Alasan</h3>

                        <div id="items-container" class="space-y-4">
                            <div class="item-row p-4 bg-slate-50 border border-slate-200 rounded-xl grid grid-cols-1 md:grid-cols-4 gap-3">
                                <div>
                                    <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Barang</label>
                                    <select name="items[0][barang_id]" required class="w-full rounded-lg border-slate-300 text-xs font-semibold">
                                        @foreach($barangs as $b)
                                            <option value="{{ $b->id }}">{{ $b->nama_barang }} (Stok: {{ $b->stok }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Batch (Opsional)</label>
                                    <input type="text" name="items[0][batch_id]" class="w-full rounded-lg border-slate-300 text-xs" placeholder="ID Batch / Kosongkan">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Selisih Adjustment (+ / -)</label>
                                    <input type="number" step="0.01" name="items[0][stok_adjustment]" required class="w-full rounded-lg border-slate-300 text-xs font-bold" placeholder="Contoh: -5.00 atau 10.00">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Alasan (Wajib)</label>
                                    <input type="text" name="items[0][alasan]" required class="w-full rounded-lg border-slate-300 text-xs" placeholder="Misal: Selisih Opname / Rusak">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                        <a href="{{ route('stock-adjustments.index') }}" class="px-4 py-2 text-sm font-semibold text-slate-600 hover:text-slate-900">Batal</a>
                        <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition shadow-md shadow-indigo-600/20">
                            Post & Update Stok
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
