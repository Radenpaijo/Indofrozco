<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-slate-800 leading-tight">
                {{ __('Detail Adjustment: ') }} {{ $stockAdjustment->nomor_adjustment }}
            </h2>
            <a href="{{ route('stock-adjustments.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-xl transition">
                &larr; Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-200/80 p-6">
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
                        <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-emerald-100 text-emerald-700">
                            {{ strtoupper($stockAdjustment->status) }}
                        </span>
                    </div>
                </div>

                <h3 class="font-bold text-slate-800 text-base mb-4">Rincian Perubahan Stok & Alasan</h3>

                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-xs font-bold uppercase text-slate-500 border-b">
                            <th class="p-3">Barang / Item</th>
                            <th class="p-3">Stok Awal</th>
                            <th class="p-3">Perubahan (+/-)</th>
                            <th class="p-3">Stok Akhir</th>
                            <th class="p-3">Alasan (Reason)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @foreach($stockAdjustment->details as $d)
                            <tr class="hover:bg-slate-50/80">
                                <td class="p-3 font-semibold text-slate-800">
                                    {{ $d->barang->nama_barang }}
                                    <span class="text-xs text-slate-400 block font-mono">{{ $d->barang->kode_barang }}</span>
                                </td>
                                <td class="p-3 font-semibold text-slate-600">{{ number_format($d->stok_sistem, 2) }}</td>
                                <td class="p-3 font-bold {{ $d->stok_adjustment < 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                    {{ $d->stok_adjustment > 0 ? '+'.number_format($d->stok_adjustment, 2) : number_format($d->stok_adjustment, 2) }}
                                </td>
                                <td class="p-3 font-bold text-slate-900">{{ number_format($d->stok_akhir, 2) }}</td>
                                <td class="p-3 font-semibold text-slate-700">{{ $d->alasan }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
