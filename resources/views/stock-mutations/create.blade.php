<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4 min-w-0">
            <x-back-button />
            <div class="min-w-0">
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    {{ __('Form Mutasi Lokasi / Rak Baru') }}
                </h2>
                <p class="text-xs text-slate-500 mt-1">Formulir pemindahan lokasi penyimpanan barang.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-200/80 p-6">
                <div class="mb-4 p-3 bg-blue-50 text-blue-800 border border-blue-200 rounded-xl text-xs">
                    <strong>Aturan BR-005:</strong> Mutasi lokasi memindahkan stok dari satu rak ke rak lain. <strong>Total stok barang di gudang tidak berubah.</strong>
                </div>

                <form action="{{ route('stock-mutations.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Tanggal Mutasi</label>
                        <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required class="w-full rounded-xl border-slate-300 text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Catatan Dokumen</label>
                        <input type="text" name="catatan" class="w-full rounded-xl border-slate-300 text-sm" placeholder="Contoh: Re-organisasi rak cold storage 01">
                    </div>

                    <hr class="border-slate-200" />

                    <div class="space-y-4">
                        <h3 class="font-bold text-slate-800 text-base">Detail Transfer Antar Rak</h3>

                        <div class="item-row p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-3">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Barang</label>
                                    <select name="items[0][barang_id]" required class="w-full rounded-lg border-slate-300 text-xs font-semibold">
                                        @foreach($barangs as $b)
                                            <option value="{{ $b->id }}">{{ $b->nama_barang }} (Stok Total: {{ $b->stok }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Batch / Lot (Opsional)</label>
                                    <input type="text" name="items[0][batch_id]" class="w-full rounded-lg border-slate-300 text-xs" placeholder="ID Batch / Kosongkan">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Rak Asal</label>
                                    <select name="items[0][lokasi_asal_id]" required class="w-full rounded-lg border-slate-300 text-xs">
                                        @foreach($locations as $loc)
                                            <option value="{{ $loc->id }}">{{ $loc->code }} ({{ $loc->warehouse->name ?? '' }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Rak Tujuan</label>
                                    <select name="items[0][lokasi_tujuan_id]" required class="w-full rounded-lg border-slate-300 text-xs">
                                        @foreach($locations as $loc)
                                            <option value="{{ $loc->id }}">{{ $loc->code }} ({{ $loc->warehouse->name ?? '' }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Jumlah Pindah</label>
                                    <input type="number" step="0.01" min="0.01" name="items[0][jumlah]" required class="w-full rounded-lg border-slate-300 text-xs font-bold" placeholder="Contoh: 100.00">
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold uppercase text-slate-500 mb-1">Alasan Pindah Rak</label>
                                <input type="text" name="items[0][alasan]" class="w-full rounded-lg border-slate-300 text-xs" placeholder="Contoh: Rak asal penuh / penyusunan ulang">
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                        <a href="{{ route('stock-mutations.index') }}" class="px-4 py-2 text-sm font-semibold text-slate-600 hover:text-slate-900">Batal</a>
                        <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition shadow-md shadow-indigo-600/20">
                            Post & Pindahkan Stok
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
