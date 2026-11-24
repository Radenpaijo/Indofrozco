<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3 min-w-0">
            <x-back-button :href="route('stock-adjustments.index')" />
            <div class="min-w-0">
                <h2 class="font-bold text-xl sm:text-2xl text-slate-800 leading-tight truncate">
                    {{ __('Detail Adjustment: ') }} {{ $stockAdjustment->nomor_adjustment }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="w-full space-y-6">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-slate-300 p-4 sm:p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm mb-6 pb-6 border-b border-slate-100">
                <div>
                    <span class="text-xs font-bold uppercase text-slate-400 block">Nomor Adjustment</span>
                    <span class="font-mono font-bold text-indigo-600 text-base">{{ $stockAdjustment->nomor_adjustment }}</span>
                </div>
                <div>
                    <span class="text-xs font-bold uppercase text-slate-400 block">Tanggal</span>
                    <span class="font-semibold text-slate-800">{{ $stockAdjustment->tanggal->format('d M Y') }}</span>
                </div>
                <div>
                    <span class="text-xs font-bold uppercase text-slate-400 block">Dibuat Oleh</span>
                    <span class="font-semibold text-slate-800">{{ $stockAdjustment->user->name }}</span>
                </div>
                <div>
                    <span class="text-xs font-bold uppercase text-slate-400 block">Status</span>
                    <span class="px-2.5 py-1 text-xs font-semibold rounded bg-slate-100 text-slate-700 border border-slate-300">
                        {{ strtoupper($stockAdjustment->status) }}
                    </span>
                </div>
            </div>

            <h3 class="font-bold text-slate-800 text-base mb-4">Rincian Perubahan Stok & Alasan</h3>

            <!-- 1. Mobile Cards View (block sm:hidden) -->
            <div class="space-y-3 sm:hidden mb-4">
                @foreach($stockAdjustment->details as $d)
                    <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/50 space-y-2.5">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <span class="font-bold text-sm text-slate-900 block leading-tight">{{ $d->barang->nama_barang }}</span>
                                <span class="font-mono text-xs text-indigo-600 mt-0.5 block">{{ $d->barang->kode_barang }}</span>
                            </div>
                            <div class="shrink-0 text-right">
                                <span class="font-bold text-xs {{ $d->stok_adjustment < 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                    Penyesuaian: {{ $d->stok_adjustment > 0 ? '+'.number_format($d->stok_adjustment, 2) : number_format($d->stok_adjustment, 2) }}
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-2 text-xs pt-2 border-t border-slate-200/70">
                            <div>
                                <span class="text-[10px] font-semibold uppercase text-slate-400 block">Stok Awal</span>
                                <span class="font-semibold text-slate-700">{{ number_format($d->stok_sistem, 2) }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] font-semibold uppercase text-slate-400 block">Selisih</span>
                                <span class="font-bold {{ $d->stok_adjustment < 0 ? 'text-rose-600' : 'text-emerald-600' }}">{{ $d->stok_adjustment > 0 ? '+'.number_format($d->stok_adjustment, 2) : number_format($d->stok_adjustment, 2) }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] font-semibold uppercase text-slate-400 block">Stok Akhir</span>
                                <span class="font-bold text-slate-900">{{ number_format($d->stok_akhir, 2) }}</span>
                            </div>
                            <div class="col-span-3 text-slate-600 text-[11px] pt-1 border-t border-slate-100">
                                <span class="font-semibold text-slate-400 uppercase text-[10px] block">Alasan:</span>
                                <span>{{ $d->alasan }}</span>
                            </div>
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
                            <th class="py-3.5 px-4 border-r border-slate-300">Stok Awal</th>
                            <th class="py-3.5 px-4 border-r border-slate-300">Perubahan (+/-)</th>
                            <th class="py-3.5 px-4 border-r border-slate-300">Stok Akhir</th>
                            <th class="py-3.5 px-4 border-r border-slate-300">Alasan (Reason)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-sm">
                        @foreach($stockAdjustment->details as $d)
                            <tr class="hover:bg-slate-50">
                                <td class="py-3.5 px-4 border-r border-slate-200 font-semibold text-slate-800">
                                    {{ $d->barang->nama_barang }}
                                    <span class="text-xs text-slate-400 block font-mono">{{ $d->barang->kode_barang }}</span>
                                </td>
                                <td class="py-3.5 px-4 border-r border-slate-200 font-semibold text-slate-600">{{ number_format($d->stok_sistem, 2) }}</td>
                                <td class="py-3.5 px-4 border-r border-slate-200 font-bold {{ $d->stok_adjustment < 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                    {{ $d->stok_adjustment > 0 ? '+'.number_format($d->stok_adjustment, 2) : number_format($d->stok_adjustment, 2) }}
                                </td>
                                <td class="py-3.5 px-4 border-r border-slate-200 font-bold text-slate-900">{{ number_format($d->stok_akhir, 2) }}</td>
                                <td class="py-3.5 px-4 font-semibold text-slate-700">{{ $d->alasan }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            </div>
        </div>
    </div>
</x-app-layout>
