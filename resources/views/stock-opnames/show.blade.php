<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-slate-800 leading-tight">
                {{ __('Detail Stock Opname: ') }} {{ $stockOpname->nomor_opname }}
            </h2>
            <a href="{{ route('stock-opnames.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-xl transition">
                &larr; Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-200/80 p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm mb-6 pb-6 border-b border-slate-100">
                    <div>
                        <span class="text-xs font-bold uppercase text-slate-400 block">Nomor Opname</span>
                        <span class="font-mono font-bold text-indigo-600 text-base">{{ $stockOpname->nomor_opname }}</span>
                    </div>
                    <div>
                        <span class="text-xs font-bold uppercase text-slate-400 block">Tanggal</span>
                        <span class="font-semibold text-slate-800">{{ $stockOpname->tanggal->format('d M Y') }}</span>
                    </div>
                    <div>
                        <span class="text-xs font-bold uppercase text-slate-400 block">Petugas</span>
                        <span class="font-semibold text-slate-800">{{ $stockOpname->user->name }}</span>
                    </div>
                    <div>
                        <span class="text-xs font-bold uppercase text-slate-400 block">Status</span>
                        <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-emerald-100 text-emerald-700">
                            {{ strtoupper($stockOpname->status) }}
                        </span>
                    </div>
                </div>

                <h3 class="font-bold text-slate-800 text-base mb-4">Hasil Perbandingan Stok</h3>

                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-xs font-bold uppercase text-slate-500 border-b">
                            <th class="p-3">Barang / Item</th>
                            <th class="p-3">Batch / Lot</th>
                            <th class="p-3">Stok Sistem</th>
                            <th class="p-3">Stok Fisik</th>
                            <th class="p-3">Selisih</th>
                            <th class="p-3">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @foreach($stockOpname->details as $d)
                            <tr class="hover:bg-slate-50/80">
                                <td class="p-3 font-semibold text-slate-800">
                                    {{ $d->barang->nama_barang }}
                                    <span class="text-xs text-slate-400 block font-mono">{{ $d->barang->kode_barang }}</span>
                                </td>
                                <td class="p-3 font-mono text-xs text-slate-600">{{ $d->batch ? $d->batch->batch_number : 'Semua Batch' }}</td>
                                <td class="p-3 font-semibold text-slate-600">{{ number_format($d->stok_sistem, 2) }}</td>
                                <td class="p-3 font-semibold text-slate-800">{{ number_format($d->stok_fisik, 2) }}</td>
                                <td class="p-3 font-bold {{ $d->selisih < 0 ? 'text-rose-600' : ($d->selisih > 0 ? 'text-emerald-600' : 'text-slate-400') }}">
                                    {{ $d->selisih > 0 ? '+'.number_format($d->selisih, 2) : number_format($d->selisih, 2) }}
                                </td>
                                <td class="p-3 text-slate-500 text-xs">{{ $d->catatan ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
