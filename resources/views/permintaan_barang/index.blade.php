<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                <x-back-button :href="route('dashboard')" />
                <div class="min-w-0">
                    <h2 class="font-bold text-xl sm:text-2xl text-slate-800 leading-tight">
                        {{ __('Permintaan Barang (Produksi)') }}
                    </h2>
                </div>
            </div>
            @if(auth()->user()->hasRole(['admin', 'produksi']))
                <a href="{{ route('permintaan-barang.create') }}" class="w-full sm:w-auto shrink-0 self-start sm:self-center">
                    <x-button variant="indigo" class="w-full sm:w-auto justify-center shadow-sm">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Ajukan Permintaan
                    </x-button>
                </a>
            @endif
        </div>
    </x-slot>

    <div class="w-full space-y-4">

        <!-- Search Bar & Filter Controls (Clean Neutral Palette) -->
        <div class="space-y-2.5">
            <form method="GET" action="{{ route('permintaan-barang.index') }}" class="flex gap-2">
                <input type="hidden" name="status" value="{{ request('status', 'semua') }}">
                <div class="relative flex-1">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" 
                        placeholder="Cari no. permintaan / pemohon..." 
                        class="w-full rounded-xl border border-slate-200 bg-white pl-9 pr-4 py-2 text-xs text-slate-800 placeholder-slate-400 shadow-2xs focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500 transition-all">
                </div>
                <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-slate-900 hover:bg-slate-800 rounded-xl transition-colors shrink-0">
                    Cari
                </button>
                @if(request()->filled('search') || (request()->filled('status') && request('status') !== 'semua'))
                    <a href="{{ route('permintaan-barang.index') }}" class="px-3 py-2 text-xs font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors shrink-0 flex items-center">
                        Reset
                    </a>
                @endif
            </form>

            <!-- Status Filter Tabs: Clean, Monochromatic, Professional -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 no-scrollbar text-xs">
                @php
                    $currentStatus = request('status', 'semua');
                    $baseParams = request()->except(['status', 'page']);
                @endphp
                <a href="{{ route('permintaan-barang.index', array_merge($baseParams, ['status' => 'semua'])) }}"
                    class="px-3.5 py-1.5 rounded-lg font-medium transition-all shrink-0 {{ $currentStatus === 'semua' ? 'bg-slate-900 text-white shadow-2xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 hover:text-slate-900' }}">
                    Semua
                    @if(isset($counts['semua']))
                        <span class="ml-1 text-[10px] {{ $currentStatus === 'semua' ? 'text-slate-300' : 'text-slate-400' }}">({{ $counts['semua'] }})</span>
                    @endif
                </a>
                <a href="{{ route('permintaan-barang.index', array_merge($baseParams, ['status' => 'pending'])) }}"
                    class="px-3.5 py-1.5 rounded-lg font-medium transition-all shrink-0 {{ $currentStatus === 'pending' ? 'bg-slate-900 text-white shadow-2xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 hover:text-slate-900' }}">
                    Pending
                    @if(isset($counts['pending']))
                        <span class="ml-1 text-[10px] {{ $currentStatus === 'pending' ? 'text-slate-300' : 'text-slate-400' }}">({{ $counts['pending'] }})</span>
                    @endif
                </a>
                <a href="{{ route('permintaan-barang.index', array_merge($baseParams, ['status' => 'selesai'])) }}"
                    class="px-3.5 py-1.5 rounded-lg font-medium transition-all shrink-0 {{ $currentStatus === 'selesai' ? 'bg-slate-900 text-white shadow-2xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 hover:text-slate-900' }}">
                    Disetujui
                    @if(isset($counts['selesai']))
                        <span class="ml-1 text-[10px] {{ $currentStatus === 'selesai' ? 'text-slate-300' : 'text-slate-400' }}">({{ $counts['selesai'] }})</span>
                    @endif
                </a>
                <a href="{{ route('permintaan-barang.index', array_merge($baseParams, ['status' => 'ditolak'])) }}"
                    class="px-3.5 py-1.5 rounded-lg font-medium transition-all shrink-0 {{ $currentStatus === 'ditolak' ? 'bg-slate-900 text-white shadow-2xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 hover:text-slate-900' }}">
                    Ditolak
                    @if(isset($counts['ditolak']))
                        <span class="ml-1 text-[10px] {{ $currentStatus === 'ditolak' ? 'text-slate-300' : 'text-slate-400' }}">({{ $counts['ditolak'] }})</span>
                    @endif
                </a>
            </div>
        </div>

        <!-- 1. Mobile Card View (block sm:hidden) -->
        <div class="space-y-3 sm:hidden">
            @forelse($permintaans as $permintaan)
                <div class="bg-white rounded-xl border border-slate-200 shadow-2xs p-4 space-y-3">
                    <!-- Header Kartu: ID Permintaan di kiri atas, Status di kanan atas -->
                    <div class="flex items-center justify-between gap-2">
                        <span class="font-mono font-bold text-sm text-slate-900">
                            {{ $permintaan->no_permintaan }}
                        </span>
                        @if($permintaan->status === 'pending')
                            <span class="px-2.5 py-0.5 rounded-md text-[10px] font-semibold bg-amber-50 text-amber-800 border border-amber-200/80">
                                PENDING
                            </span>
                        @elseif($permintaan->status === 'selesai')
                            <span class="px-2.5 py-0.5 rounded-md text-[10px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/80">
                                DISETUJUI
                            </span>
                        @elseif($permintaan->status === 'ditolak')
                            <span class="px-2.5 py-0.5 rounded-md text-[10px] font-semibold bg-rose-50 text-rose-800 border border-rose-200/80">
                                DITOLAK
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-md text-[10px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                {{ strtoupper($permintaan->status) }}
                            </span>
                        @endif
                    </div>

                    <!-- Body Kartu: Pembuat, Tanggal, Item -->
                    <div class="grid grid-cols-2 gap-2 text-xs py-2.5 border-y border-slate-100">
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-semibold">Pembuat</span>
                            <span class="font-medium text-slate-800 block mt-0.5 truncate">
                                {{ $permintaan->user->name ?? '-' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-semibold">Tanggal</span>
                            <span class="font-medium text-slate-700 block mt-0.5">
                                {{ \Carbon\Carbon::parse($permintaan->tanggal)->format('d/m/Y') }}
                            </span>
                        </div>
                        <div class="col-span-2 pt-1 flex items-center justify-between text-slate-700 bg-slate-50 px-2.5 py-1.5 rounded-lg mt-0.5">
                            <span class="text-slate-500 text-[11px]">Total Muatan:</span>
                            <span class="font-semibold text-slate-800 text-xs">{{ $permintaan->details->count() }} Macam Barang</span>
                        </div>
                    </div>

                    <!-- Footer Kartu: Tombol Aksi "Lihat Detail" Bersih & Netral -->
                    <div>
                        <a href="{{ route('permintaan-barang.show', $permintaan->id) }}" 
                            class="w-full inline-flex items-center justify-center gap-1.5 py-2 px-3 text-xs font-semibold text-slate-700 bg-slate-50 hover:bg-slate-100 active:bg-slate-200 rounded-lg transition-all border border-slate-200">
                            <span>Lihat Detail</span>
                            <svg class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl border border-dashed border-slate-200 p-8 text-center">
                    <svg class="h-8 w-8 text-slate-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="text-xs text-slate-500 font-medium">Belum ada pengajuan permintaan barang.</p>
                </div>
            @endforelse
        </div>

        <!-- 2. Desktop Table View (hidden sm:block) -->
        <div class="hidden sm:block">
            <x-table>
                <x-table.header>
                    <x-table.row>
                        <x-table.head class="whitespace-nowrap">No. Permintaan</x-table.head>
                        <x-table.head>Tanggal</x-table.head>
                        <x-table.head>Pemohon</x-table.head>
                        <x-table.head class="text-center">Item</x-table.head>
                        <x-table.head class="text-center">Status</x-table.head>
                        <x-table.head class="text-right">Aksi</x-table.head>
                    </x-table.row>
                </x-table.header>
                <x-table.body>
                    @forelse($permintaans as $permintaan)
                        <x-table.row>
                            <x-table.cell>
                                <span class="font-mono font-bold text-slate-800 text-xs">{{ $permintaan->no_permintaan }}</span>
                            </x-table.cell>
                            <x-table.cell class="text-slate-600 text-xs whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($permintaan->tanggal)->format('d M Y H:i') }}
                            </x-table.cell>
                            <x-table.cell class="text-slate-800 font-medium text-xs">
                                {{ $permintaan->user->name ?? '-' }}
                            </x-table.cell>
                            <x-table.cell class="text-center text-slate-600 text-xs">
                                {{ $permintaan->details->count() }} Macam
                            </x-table.cell>
                            <x-table.cell class="text-center">
                                <div class="flex flex-col items-center gap-1">
                                    @if($permintaan->status === 'pending')
                                        <span class="px-2.5 py-0.5 rounded-md text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200/80">
                                            PENDING
                                        </span>
                                    @elseif($permintaan->status === 'selesai')
                                        <span class="px-2.5 py-0.5 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/80">
                                            DISETUJUI
                                        </span>
                                    @elseif($permintaan->status === 'ditolak')
                                        <span class="px-2.5 py-0.5 rounded-md text-xs font-semibold bg-rose-50 text-rose-800 border border-rose-200/80">
                                            DITOLAK
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-md text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                            {{ strtoupper($permintaan->status) }}
                                        </span>
                                    @endif

                                    @if($permintaan->status === 'selesai')
                                        @if($permintaan->qcInspection)
                                            @if($permintaan->qcInspection->status_qc === 'pass')
                                                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.2 rounded border border-emerald-200">
                                                    ✓ Defrost OK ({{ $permintaan->qcInspection->susut_persen }}% susut)
                                                </span>
                                            @else
                                                <span class="text-[10px] font-bold text-rose-700 bg-rose-50 px-1.5 py-0.2 rounded border border-rose-200">
                                                    ✕ Defrost Ditolak
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-[10px] font-medium text-amber-700 bg-amber-50 px-1.5 py-0.2 rounded border border-amber-200">
                                                Perlu QC Defrost
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </x-table.cell>
                            <x-table.cell class="text-right">
                                <a href="{{ route('permintaan-barang.show', $permintaan->id) }}">
                                    <x-button variant="outline" size="sm" class="text-xs px-3 py-1">Detail</x-button>
                                </a>
                            </x-table.cell>
                        </x-table.row>
                    @empty
                        <x-table.row>
                            <x-table.cell colspan="6" class="py-8 text-center text-slate-400">
                                Belum ada pengajuan permintaan barang.
                            </x-table.cell>
                        </x-table.row>
                    @endforelse
                </x-table.body>
            </x-table>
        </div>

        @if($permintaans->hasPages())
            <div class="px-4 py-3 bg-white rounded-xl border border-slate-200 shadow-2xs">
                {{ $permintaans->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
