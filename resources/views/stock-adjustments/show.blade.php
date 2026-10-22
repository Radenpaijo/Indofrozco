<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4 min-w-0">
            <x-back-button />
            <div class="min-w-0">
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    {{ __('Detail Adjustment: ') }} {{ $stockAdjustment->nomor_adjustment }}
                </h2>
                <p class="text-xs text-slate-500 mt-1">Rincian penyesuaian kuantitas stok barang.</p>
            </div>
        </div>
    </x-slot>

    <div class="w-full space-y-6">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-slate-300 p-6">
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

                <div class="overflow-x-auto border border-slate-300 rounded-lg">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-100 border-b border-slate-300 text-[11px] font-bold uppercase tracking-wider text-slate-700">
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
