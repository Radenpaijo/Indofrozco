<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                <x-back-button :href="route('dashboard')" />
                <div class="min-w-0">
                    <h2 class="font-bold text-xl sm:text-2xl text-slate-800 leading-tight truncate">
                        {{ __('Pengeluaran Gudang (Outbound)') }}
                    </h2>
                </div>
            </div>
            @if(auth()->user()->hasRole(['admin', 'gudang']))
                <a href="{{ route('barang-keluar.create') }}" class="w-full sm:w-auto shrink-0 self-start sm:self-center">
                    <x-button variant="indigo" class="w-full sm:w-auto justify-center shadow-sm">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Catat Rilis Manual
                    </x-button>
                </a>
            @endif
        </div>
    </x-slot>

    <div class="w-full space-y-4 sm:space-y-6">

        <!-- 1. Mobile Card View (block sm:hidden) -->
        <div class="space-y-3 sm:hidden">
            @forelse($barangKeluars as $keluar)
                <div x-data="{ open: false }" class="bg-white rounded-xl border border-slate-200 shadow-2xs p-3.5 space-y-2.5 transition-all">
                    <!-- Header Kartu: No. Pengeluaran & Tanggal -->
                    <div class="flex items-center justify-between gap-2">
                        <span class="font-mono font-bold text-xs text-indigo-600 bg-indigo-50 border border-indigo-100 rounded px-2 py-0.5">
                            @if($keluar->permintaanBarang)
                                {{ $keluar->permintaanBarang->no_permintaan }}
                            @else
                                MANUAL-OUT-{{ $keluar->id }}
                            @endif
                        </span>
                        <span class="text-[11px] text-slate-500 font-medium">
                            {{ \Carbon\Carbon::parse($keluar->tanggal)->format('d M Y, H:i') }}
                        </span>
                    </div>

                    <!-- Highlight Info Utama: Barang & Jumlah Keluar -->
                    <div class="flex items-center justify-between gap-3">
                        <h4 class="font-bold text-slate-800 text-sm leading-snug truncate">
                            {{ $keluar->snapshot_nama_barang ?? ($keluar->barang->nama_barang ?? '-') }}
                        </h4>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200/80 shrink-0">
                            -{{ $keluar->jumlah }} {{ $keluar->barang->satuan ?? '' }}
                        </span>
                    </div>

                    <!-- Bar Bawah: Petugas Ringkas & Tombol Detail -->
                    <div class="pt-1.5 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-[11px] text-slate-400 truncate max-w-[170px]">
                            {{ $keluar->user->name ?? '-' }}
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
                                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Ref. Permintaan</span>
                                <div class="mt-0.5">
                                    @if($keluar->permintaanBarang)
                                        <a href="{{ route('permintaan-barang.show', $keluar->permintaanBarang->id) }}" class="text-indigo-600 hover:underline font-mono font-semibold text-xs inline-flex items-center gap-0.5">
                                            <span>{{ $keluar->permintaanBarang->no_permintaan }}</span>
                                            <svg class="w-3 h-3 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                        </a>
                                    @else
                                        <span class="text-slate-400 italic text-[11px]">Rilis Manual</span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Petugas</span>
                                <span class="font-medium text-slate-700 block mt-0.5 truncate">
                                    {{ $keluar->user->name ?? '-' }}
                                </span>
                            </div>
                            @if($keluar->inventoryTransactions && $keluar->inventoryTransactions->count() > 0)
                                <div class="col-span-2 pt-1 border-t border-slate-200/60">
                                    <span class="text-slate-400 block text-[10px] uppercase font-semibold mb-1">Alokasi Batch (FIFO)</span>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($keluar->inventoryTransactions as $tx)
                                            @if($tx->batch)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-mono font-medium bg-white text-slate-700 border border-slate-200">
                                                    Batch: {{ $tx->batch->batch_number }} (-{{ (float)$tx->jumlah }})
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-mono font-medium bg-white text-slate-600 border border-slate-200">
                                                    Direct (-{{ (float)$tx->jumlah }})
                                                </span>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl border border-dashed border-slate-200 p-8 text-center text-slate-400 text-xs">
                    <svg class="h-8 w-8 text-slate-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    Belum ada pengeluaran gudang.
                </div>
            @endforelse
        </div>

        <!-- 2. Desktop Table View (hidden sm:block) -->
        <div class="hidden sm:block">
            <x-table>
                <x-table.header>
                    <x-table.row>
                        <x-table.head class="whitespace-nowrap">No. Pengeluaran</x-table.head>
                        <x-table.head>Nama Barang</x-table.head>
                        <x-table.head class="text-right sm:text-left whitespace-nowrap">Jumlah</x-table.head>
                        <x-table.head class="whitespace-nowrap">Batch (FIFO)</x-table.head>
                        <x-table.head class="whitespace-nowrap">Petugas</x-table.head>
                        <x-table.head class="whitespace-nowrap">Ref. Permintaan</x-table.head>
                        <x-table.head class="whitespace-nowrap text-right">Tanggal</x-table.head>
                    </x-table.row>
                </x-table.header>
                <x-table.body>
                    @forelse($barangKeluars as $keluar)
                        <x-table.row>
                            <x-table.cell>
                                <div class="font-mono font-bold text-indigo-600 text-xs">
                                    @if($keluar->permintaanBarang)
                                        {{ $keluar->permintaanBarang->no_permintaan }}
                                    @else
                                        MANUAL-OUT-{{ $keluar->id }}
                                    @endif
                                </div>
                                <div class="text-[11px] text-slate-500 sm:hidden mt-0.5">
                                    {{ \Carbon\Carbon::parse($keluar->tanggal)->format('d/m/Y') }}
                                </div>
                            </x-table.cell>
                            <x-table.cell class="font-medium text-slate-800 text-xs sm:text-sm">
                                {{ $keluar->snapshot_nama_barang ?? ($keluar->barang->nama_barang ?? '-') }}
                            </x-table.cell>
                            <x-table.cell class="font-bold text-rose-600 text-xs sm:text-sm text-right sm:text-left whitespace-nowrap">
                                -{{ $keluar->jumlah }} {{ $keluar->barang->satuan ?? '' }}
                            </x-table.cell>
                            <x-table.cell>
                                @if($keluar->inventoryTransactions && $keluar->inventoryTransactions->count() > 0)
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($keluar->inventoryTransactions as $tx)
                                            @if($tx->batch)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-mono font-medium bg-slate-100 text-slate-700 border border-slate-300">
                                                    {{ $tx->batch->batch_number }} (-{{ (float)$tx->jumlah }})
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-mono font-medium bg-slate-50 text-slate-600 border border-slate-200">
                                                    Direct Stock (-{{ (float)$tx->jumlah }})
                                                </span>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 italic">No batch ledger</span>
                                @endif
                            </x-table.cell>
                            <x-table.cell class="text-slate-600 whitespace-nowrap text-xs">
                                {{ $keluar->user->name ?? '-' }}
                            </x-table.cell>
                            <x-table.cell class="text-slate-500 font-mono whitespace-nowrap text-xs">
                                @if($keluar->permintaanBarang)
                                    <a href="{{ route('permintaan-barang.show', $keluar->permintaanBarang->id) }}" class="text-indigo-600 hover:underline font-semibold">
                                        {{ $keluar->permintaanBarang->no_permintaan }}
                                    </a>
                                @else
                                    <span class="text-slate-400 italic">Rilis Manual</span>
                                @endif
                            </x-table.cell>
                            <x-table.cell class="text-slate-500 text-xs font-mono whitespace-nowrap text-right">
                                {{ \Carbon\Carbon::parse($keluar->tanggal)->format('d M Y H:i') }}
                            </x-table.cell>
                        </x-table.row>
                    @empty
                        <x-table.row>
                            <x-table.cell colspan="7" class="py-8 text-center text-slate-400">
                                Belum ada pengeluaran gudang.
                            </x-table.cell>
                        </x-table.row>
                    @endforelse
                </x-table.body>
            </x-table>
        </div>

        @if($barangKeluars->hasPages())
            <div class="px-6 py-4 bg-white rounded-lg border border-slate-300">
                {{ $barangKeluars->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
