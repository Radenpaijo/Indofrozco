<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4 min-w-0">
            <x-back-button />
            <div class="min-w-0">
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    {{ __('Detail Mutasi: ') }} {{ $stockMutation->nomor_mutasi }}
                </h2>
                <p class="text-xs text-slate-500 mt-1">Informasi asal dan lokasi tujuan mutasi rak.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-200/80 p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm mb-6 pb-6 border-b border-slate-100">
                    <div>
                        <span class="text-xs font-bold uppercase text-slate-400 block">Nomor Mutasi</span>
                        <span class="font-mono font-bold text-indigo-600 text-base">{{ $stockMutation->nomor_mutasi }}</span>
                    </div>
                    <div>
                        <span class="text-xs font-bold uppercase text-slate-400 block">Tanggal</span>
                        <span class="font-semibold text-slate-800">{{ $stockMutation->tanggal->format('d M Y') }}</span>
                    </div>
                    <div>
                        <span class="text-xs font-bold uppercase text-slate-400 block">Dibuat Oleh</span>
                        <span class="font-semibold text-slate-800">{{ $stockMutation->user->name }}</span>
                    </div>
                    <div>
                        <span class="text-xs font-bold uppercase text-slate-400 block">Status</span>
                        <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-emerald-100 text-emerald-700">
                            {{ strtoupper($stockMutation->status) }}
                        </span>
                    </div>
                </div>

                <h3 class="font-bold text-slate-800 text-base mb-4">Item Pindah Lokasi</h3>

                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-xs font-bold uppercase text-slate-500 border-b">
                            <th class="p-3">Barang / Item</th>
                            <th class="p-3">Batch / Lot</th>
                            <th class="p-3">Rak Asal</th>
                            <th class="p-3">Rak Tujuan</th>
                            <th class="p-3">Jumlah Pindah</th>
                            <th class="p-3">Alasan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @foreach($stockMutation->details as $d)
                            <tr class="hover:bg-slate-50/80">
                                <td class="p-3 font-semibold text-slate-800">
                                    {{ $d->barang->nama_barang }}
                                    <span class="text-xs text-slate-400 block font-mono">{{ $d->barang->kode_barang }}</span>
                                </td>
                                <td class="p-3 font-mono text-xs text-slate-600">{{ $d->batch ? $d->batch->batch_number : '-' }}</td>
                                <td class="p-3 font-semibold text-rose-700 bg-rose-50/50 rounded-lg">{{ $d->lokasiAsal->code ?? '-' }}</td>
                                <td class="p-3 font-semibold text-emerald-700 bg-emerald-50/50 rounded-lg">{{ $d->lokasiTujuan->code ?? '-' }}</td>
                                <td class="p-3 font-bold text-indigo-600">{{ number_format($d->jumlah, 2) }}</td>
                                <td class="p-3 text-slate-500 text-xs">{{ $d->alasan ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
