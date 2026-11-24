<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3 min-w-0">
            <x-back-button :href="route('stock-mutations.index')" />
            <div class="min-w-0">
                <h2 class="font-bold text-xl sm:text-2xl text-slate-800 leading-tight truncate">
                    {{ __('Detail Mutasi: ') }} {{ $stockMutation->nomor_mutasi }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="w-full space-y-6">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-slate-300 p-4 sm:p-6">
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

            <!-- 1. Mobile Cards View (block sm:hidden) -->
            <div class="space-y-3 sm:hidden mb-4">
                @foreach($stockMutation->details as $d)
                    <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/50 space-y-2.5">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <span class="font-bold text-sm text-slate-900 block leading-tight">{{ $d->barang->nama_barang }}</span>
                                <span class="font-mono text-xs text-indigo-600 mt-0.5 block">{{ $d->barang->kode_barang }}</span>
                            </div>
                            <div class="shrink-0 text-right">
                                <span class="text-[10px] font-semibold uppercase text-slate-400 block">Jumlah Pindah</span>
                                <span class="font-black text-sm text-indigo-600 font-mono">{{ number_format($d->jumlah, 2) }}</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-xs pt-2 border-t border-slate-200/70">
                            <div class="p-2 rounded-lg bg-rose-50/60 border border-rose-200/60">
                                <span class="text-[10px] font-semibold uppercase text-rose-500 block">Rak Asal</span>
                                <span class="font-bold text-rose-800 font-mono">{{ $d->lokasiAsal->code ?? '-' }}</span>
                            </div>
                            <div class="p-2 rounded-lg bg-emerald-50/60 border border-emerald-200/60">
                                <span class="text-[10px] font-semibold uppercase text-emerald-500 block">Rak Tujuan</span>
                                <span class="font-bold text-emerald-800 font-mono">{{ $d->lokasiTujuan->code ?? '-' }}</span>
                            </div>
                            @if($d->batch)
                                <div class="col-span-2">
                                    <span class="text-[10px] font-semibold uppercase text-slate-400 block">Batch / Lot</span>
                                    <span class="font-mono text-xs text-slate-700">#{{ $d->batch->batch_number }}</span>
                                </div>
                            @endif
                            @if($d->alasan)
                                <div class="col-span-2 text-slate-500 text-[11px] italic">
                                    Alasan: {{ $d->alasan }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- 2. Desktop Table View (hidden sm:block) -->
            <div class="hidden sm:block overflow-x-auto border border-slate-300 rounded-lg">
                <table class="w-full text-left border-collapse min-w-[650px]">
                    <thead>
                        <tr class="bg-slate-100 border-b border-slate-300 text-[11px] font-bold uppercase tracking-wider text-slate-700 whitespace-nowrap">
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
