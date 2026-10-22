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

    <div class="w-full space-y-6">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-slate-300 p-6">
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
                        <span class="px-2.5 py-1 text-xs font-semibold rounded bg-slate-100 text-slate-700 border border-slate-300">
                            {{ strtoupper($stockMutation->status) }}
                        </span>
                    </div>
                </div>

                <h3 class="font-bold text-slate-800 text-base mb-4">Item Pindah Lokasi</h3>

                <div class="overflow-x-auto border border-slate-300 rounded-lg">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-100 border-b border-slate-300 text-[11px] font-bold uppercase tracking-wider text-slate-700">
                                <th class="py-3.5 px-4 border-r border-slate-300">Barang / Item</th>
                                <th class="py-3.5 px-4 border-r border-slate-300">Batch / Lot</th>
                                <th class="py-3.5 px-4 border-r border-slate-300">Rak Asal</th>
                                <th class="py-3.5 px-4 border-r border-slate-300">Rak Tujuan</th>
                                <th class="py-3.5 px-4 border-r border-slate-300">Jumlah Pindah</th>
                                <th class="py-3.5 px-4 border-r border-slate-300">Alasan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 text-sm">
                            @foreach($stockMutation->details as $d)
                                <tr class="hover:bg-slate-50">
                                    <td class="py-3.5 px-4 border-r border-slate-200 font-semibold text-slate-800">
                                        {{ $d->barang->nama_barang }}
                                        <span class="text-xs text-slate-400 block font-mono">{{ $d->barang->kode_barang }}</span>
                                    </td>
                                    <td class="py-3.5 px-4 border-r border-slate-200 font-mono text-xs text-slate-600">{{ $d->batch ? $d->batch->batch_number : '-' }}</td>
                                    <td class="py-3.5 px-4 border-r border-slate-200 font-semibold text-rose-700 bg-rose-50/50">{{ $d->lokasiAsal->code ?? '-' }}</td>
                                    <td class="py-3.5 px-4 border-r border-slate-200 font-semibold text-emerald-700 bg-emerald-50/50">{{ $d->lokasiTujuan->code ?? '-' }}</td>
                                    <td class="py-3.5 px-4 border-r border-slate-200 font-bold text-indigo-600">{{ number_format($d->jumlah, 2) }}</td>
                                    <td class="py-3.5 px-4 text-slate-500 text-xs">{{ $d->alasan ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
