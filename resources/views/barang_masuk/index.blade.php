<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                <x-back-button :href="route('dashboard')" />
                <div class="min-w-0">
                    <h2 class="font-bold text-xl sm:text-2xl text-slate-800 leading-tight truncate">
                        {{ __('Penerimaan Gudang (Inbound)') }}
                    </h2>
                </div>
            </div>
            @if(auth()->user()->hasRole(['admin', 'gudang']))
                <a href="{{ route('barang-masuk.create') }}" class="w-full sm:w-auto shrink-0 self-start sm:self-center">
                    <x-button variant="indigo" class="w-full sm:w-auto justify-center shadow-sm">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Catat Penerimaan Baru
                    </x-button>
                </a>
            @endif
        </div>
    </x-slot>

    <div class="w-full space-y-4 sm:space-y-6">

        <!-- 1. Mobile Card View (block sm:hidden) -->
        <div class="space-y-3 sm:hidden">
            @forelse($barangMasuks as $masuk)
                <div x-data="{ open: false }" class="bg-white rounded-xl border border-slate-200 shadow-2xs p-3.5 space-y-2.5 transition-all">
                    <!-- Header Kartu: No. Masuk & Tanggal -->
                    <div class="flex items-center justify-between gap-2">
                        <span class="font-mono font-bold text-xs text-indigo-600 bg-indigo-50 border border-indigo-100 rounded px-2 py-0.5">
                            {{ $masuk->no_masuk }}
                        </span>
                        <span class="text-[11px] text-slate-500 font-medium">
                            {{ \Carbon\Carbon::parse($masuk->tanggal)->format('d M Y, H:i') }}
                        </span>
                    </div>

                    <!-- Highlight Info Utama: Barang & Kuantitas -->
                    <div class="flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <h4 class="font-bold text-slate-800 text-sm leading-snug truncate">
                                {{ $masuk->snapshot_nama_barang ?? ($masuk->barang->nama_barang ?? '-') }}
                            </h4>
                            @if($masuk->batch_number || $masuk->tanggal_kadaluarsa)
                                <div class="flex items-center gap-2 mt-0.5 text-[11px] font-mono text-slate-500">
                                    @if($masuk->batch_number)
                                        <span class="bg-slate-100 px-1.5 py-0.5 rounded font-bold text-slate-700">Lot: {{ $masuk->batch_number }}</span>
                                    @endif
                                    @if($masuk->tanggal_kadaluarsa)
                                        <span class="text-amber-700 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-200">ED: {{ \Carbon\Carbon::parse($masuk->tanggal_kadaluarsa)->format('d/m/Y') }}</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                        <div class="text-right shrink-0">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                                +{{ $masuk->jumlah }} {{ $masuk->barang->unit->name ?? $masuk->barang->satuan ?? '' }}
                            </span>
                            @if($masuk->jumlah_pack)
                                <span class="block text-[11px] text-slate-500 font-medium">({{ $masuk->jumlah_pack }} Pack)</span>
                            @endif
                        </div>
                    </div>

                    <!-- Bar Bawah: Lokasi Ringkas & Tombol Detail -->
                    <div class="pt-1.5 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-[11px] text-slate-400 font-mono truncate max-w-[170px]">
                            @if($masuk->location)
                                {{ $masuk->location->code }}
                            @elseif($masuk->barang && $masuk->barang->location)
                                {{ $masuk->barang->location->code }}
                            @else
                                Gudang
                            @endif
                        </span>
                        <button type="button" @click="open = !open" 
                            class="inline-flex items-center gap-1 text-xs font-semibold text-slate-700 hover:text-indigo-600 bg-slate-50 hover:bg-slate-100 active:bg-slate-200 px-2.5 py-1 rounded-lg border border-slate-200 transition-colors">
                            <span x-text="open ? 'Tutup Detail' : 'Detail'">Detail</span>
                            <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                    </div>

                    <!-- Rincian Detail (Bisa dibuka/tutup saat klik Detail) -->
                    <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="pt-2 border-t border-dashed border-slate-200 text-xs space-y-2">
                        <div class="grid grid-cols-2 gap-2 bg-slate-50 p-2.5 rounded-lg border border-slate-100">
                            <div>
                                <span class="text-slate-400 block text-[10px] uppercase font-semibold">No. Surat Jalan</span>
                                <span class="font-semibold text-slate-800 block mt-0.5 font-mono text-xs truncate">
                                    {{ $masuk->no_surat_jalan ?: '-' }}
                                </span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Ref. PO</span>
                                <div class="mt-0.5">
                                    @if($masuk->purchaseOrder)
                                        <a href="{{ route('purchase-orders.show', $masuk->purchaseOrder->id) }}" class="text-indigo-600 hover:underline font-mono font-semibold text-xs inline-flex items-center gap-0.5">
                                            <span>{{ $masuk->purchaseOrder->no_po }}</span>
                                            <svg class="w-3 h-3 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                        </a>
                                    @else
                                        <span class="text-slate-400 italic text-[11px]">Manual (Tanpa PO)</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-span-2 pt-1 border-t border-slate-200/60">
                                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Lokasi Gudang / Rak</span>
                                <span class="font-medium text-slate-700 block mt-0.5">
                                    @if($masuk->location)
                                        {{ $masuk->location->warehouse->name ?? 'Gudang' }} &bull; {{ $masuk->location->code }}
                                    @elseif($masuk->barang && $masuk->barang->location)
                                        {{ $masuk->barang->location->warehouse->name ?? 'Gudang' }} &bull; {{ $masuk->barang->location->code }}
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                            <div class="col-span-2 pt-1 border-t border-slate-200/60 flex items-center justify-between text-slate-500 text-[11px]">
                                <span>Petugas Penerima:</span>
                                <span class="font-medium text-slate-700">{{ $masuk->user->name ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl border border-dashed border-slate-200 p-8 text-center text-slate-400 text-xs">
                    <svg class="h-8 w-8 text-slate-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    Belum ada penerimaan gudang.
                </div>
            @endforelse
        </div>

        <!-- 2. Desktop Table View (hidden sm:block) -->
        <div class="hidden sm:block">
            <x-table>
                <x-table.header>
                    <x-table.row>
                        <x-table.head class="whitespace-nowrap">No. Masuk</x-table.head>
                        <x-table.head class="whitespace-nowrap">No. Surat Jalan</x-table.head>
                        <x-table.head>Barang</x-table.head>
                        <x-table.head class="whitespace-nowrap">Lot & ED (FEFO)</x-table.head>
                        <x-table.head class="whitespace-nowrap">QC Inbound</x-table.head>
                        <x-table.head class="text-right sm:text-left whitespace-nowrap">Jumlah</x-table.head>
                        <x-table.head class="whitespace-nowrap">Petugas</x-table.head>
                        <x-table.head class="whitespace-nowrap">Ref. PO</x-table.head>
                        <x-table.head class="whitespace-nowrap text-right">Tanggal</x-table.head>
                    </x-table.row>
                </x-table.header>
                <x-table.body>
                    @forelse($barangMasuks as $masuk)
                        <x-table.row>
                            <x-table.cell class="whitespace-nowrap">
                                <span class="font-mono font-bold text-indigo-600 text-xs">{{ $masuk->no_masuk }}</span>
                            </x-table.cell>
                            <x-table.cell class="text-slate-900 font-semibold text-xs whitespace-nowrap">
                                {{ $masuk->no_surat_jalan }}
                            </x-table.cell>
                            <x-table.cell class="font-medium text-slate-800 text-xs sm:text-sm">
                                <div>{{ $masuk->snapshot_nama_barang ?? ($masuk->barang->nama_barang ?? '-') }}</div>
                                @if($masuk->location)
                                    <div class="text-[11px] text-slate-500 font-mono mt-0.5">
                                        {{ $masuk->location->warehouse->name ?? 'Gudang' }} &bull; {{ $masuk->location->code }}
                                    </div>
                                @elseif($masuk->barang && $masuk->barang->location)
                                    <div class="text-[11px] text-slate-500 font-mono mt-0.5">
                                        {{ $masuk->barang->location->warehouse->name ?? 'Gudang' }} &bull; {{ $masuk->barang->location->code }}
                                    </div>
                                @endif
                            </x-table.cell>
                            <x-table.cell class="text-xs font-mono whitespace-nowrap">
                                @if($masuk->batch_number)
                                    <div class="font-bold text-slate-800">{{ $masuk->batch_number }}</div>
                                @else
                                    <div class="text-slate-400 italic">-</div>
                                @endif
                                @if($masuk->tanggal_kadaluarsa)
                                    <div class="text-[11px] text-amber-700 font-semibold">ED: {{ \Carbon\Carbon::parse($masuk->tanggal_kadaluarsa)->format('d/m/Y') }}</div>
                                @endif
                            </x-table.cell>
                            <x-table.cell class="whitespace-nowrap">
                                @if($masuk->qcInspection)
                                    @if($masuk->qcInspection->status_qc === 'pass')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                            ✓ Lolos QC
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-rose-100 text-rose-800 border border-rose-300">
                                            ✕ Ditolak
                                        </span>
                                    @endif
                                @else
                                    <span class="text-slate-400 text-xs">-</span>
                                @endif
                            </x-table.cell>
                            <x-table.cell class="font-bold text-emerald-600 text-xs sm:text-sm text-right sm:text-left whitespace-nowrap">
                                <div>+{{ $masuk->jumlah }} {{ $masuk->barang->unit->name ?? $masuk->barang->satuan ?? '' }}</div>
                                @if($masuk->jumlah_pack)
                                    <div class="text-[11px] text-slate-500 font-normal">({{ $masuk->jumlah_pack }} Pack)</div>
                                @endif
                            </x-table.cell>
                            <x-table.cell class="text-slate-600 whitespace-nowrap text-xs">
                                {{ $masuk->user->name ?? '-' }}
                            </x-table.cell>
                            <x-table.cell class="text-slate-500 font-mono whitespace-nowrap text-xs">
                                @if($masuk->purchaseOrder)
                                    <a href="{{ route('purchase-orders.show', $masuk->purchaseOrder->id) }}" class="text-indigo-600 hover:underline font-semibold">
                                        {{ $masuk->purchaseOrder->no_po }}
                                    </a>
                                @else
                                    <span class="text-slate-400 italic">Manual (Tanpa PO)</span>
                                @endif
                            </x-table.cell>
                            <x-table.cell class="text-slate-500 whitespace-nowrap text-xs text-right">
                                {{ \Carbon\Carbon::parse($masuk->tanggal)->format('d M Y H:i') }}
                            </x-table.cell>
                        </x-table.row>
                    @empty
                        <x-table.row>
                            <x-table.cell colspan="9" class="py-8 text-center text-slate-400">
                                Belum ada penerimaan gudang.
                            </x-table.cell>
                        </x-table.row>
                    @endforelse
                </x-table.body>
            </x-table>
        </div>

        @if($barangMasuks->hasPages())
            <div class="px-6 py-4 bg-white rounded-lg border border-slate-300">
                {{ $barangMasuks->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
