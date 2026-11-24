<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                <x-back-button :href="route('dashboard')" />
                <div class="min-w-0">
                    <h2 class="font-bold text-xl sm:text-2xl text-slate-800 leading-tight">
                        {{ __('Permintaan Pembelian (PR)') }}
                    </h2>
                </div>
            </div>
            @if(auth()->user()->hasRole(['admin', 'gudang']))
                <a href="{{ route('permintaan-pembelian.create') }}" class="w-full sm:w-auto shrink-0 self-start sm:self-center">
                    <x-button variant="indigo" class="w-full sm:w-auto justify-center shadow-sm">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Buat PR Baru
                    </x-button>
                </a>
            @endif
        </div>
    </x-slot>

    <div class="w-full space-y-4">

        <!-- Search Bar & Filter Controls (Clean Neutral Palette) -->
        <div class="space-y-2.5">
            <form method="GET" action="{{ route('permintaan-pembelian.index') }}" class="flex gap-2">
                <input type="hidden" name="status" value="{{ request('status', 'semua') }}">
                <div class="relative flex-1">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" 
                        placeholder="Cari no. PR / pembuat..." 
                        class="w-full rounded-xl border border-slate-200 bg-white pl-9 pr-4 py-2 text-xs text-slate-800 placeholder-slate-400 shadow-2xs focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500 transition-all">
                </div>
                <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-slate-900 hover:bg-slate-800 rounded-xl transition-colors shrink-0">
                    Cari
                </button>
                @if(request()->filled('search') || (request()->filled('status') && request('status') !== 'semua'))
                    <a href="{{ route('permintaan-pembelian.index') }}" class="px-3 py-2 text-xs font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors shrink-0 flex items-center">
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
                <a href="{{ route('permintaan-pembelian.index', array_merge($baseParams, ['status' => 'semua'])) }}"
                    class="px-3.5 py-1.5 rounded-lg font-medium transition-all shrink-0 {{ $currentStatus === 'semua' ? 'bg-slate-900 text-white shadow-2xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 hover:text-slate-900' }}">
                    Semua
                    @if(isset($counts['semua']))
                        <span class="ml-1 text-[10px] {{ $currentStatus === 'semua' ? 'text-slate-300' : 'text-slate-400' }}">({{ $counts['semua'] }})</span>
                    @endif
                </a>
                <a href="{{ route('permintaan-pembelian.index', array_merge($baseParams, ['status' => 'pending'])) }}"
                    class="px-3.5 py-1.5 rounded-lg font-medium transition-all shrink-0 {{ $currentStatus === 'pending' ? 'bg-slate-900 text-white shadow-2xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 hover:text-slate-900' }}">
                    Pending
                    @if(isset($counts['pending']))
                        <span class="ml-1 text-[10px] {{ $currentStatus === 'pending' ? 'text-slate-300' : 'text-slate-400' }}">({{ $counts['pending'] }})</span>
                    @endif
                </a>
                <a href="{{ route('permintaan-pembelian.index', array_merge($baseParams, ['status' => 'di_po'])) }}"
                    class="px-3.5 py-1.5 rounded-lg font-medium transition-all shrink-0 {{ $currentStatus === 'di_po' ? 'bg-slate-900 text-white shadow-2xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 hover:text-slate-900' }}">
                    Telah Di-PO-kan
                    @if(isset($counts['di_po']))
                        <span class="ml-1 text-[10px] {{ $currentStatus === 'di_po' ? 'text-slate-300' : 'text-slate-400' }}">({{ $counts['di_po'] }})</span>
                    @endif
                </a>
                <a href="{{ route('permintaan-pembelian.index', array_merge($baseParams, ['status' => 'dibatalkan'])) }}"
                    class="px-3.5 py-1.5 rounded-lg font-medium transition-all shrink-0 {{ $currentStatus === 'dibatalkan' ? 'bg-slate-900 text-white shadow-2xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 hover:text-slate-900' }}">
                    Dibatalkan
                    @if(isset($counts['dibatalkan']))
                        <span class="ml-1 text-[10px] {{ $currentStatus === 'dibatalkan' ? 'text-slate-300' : 'text-slate-400' }}">({{ $counts['dibatalkan'] }})</span>
                    @endif
                </a>
            </div>
        </div>

        <!-- Mobile Card View (block sm:hidden) -->
        <div class="space-y-3 sm:hidden">
            @forelse($prs as $pr)
                <div class="bg-white rounded-xl border border-slate-200 shadow-2xs p-4 space-y-3">
                    <!-- Header Kartu: ID PR di kiri atas, Status di kanan atas -->
                    <div class="flex items-center justify-between gap-2">
                        <span class="font-mono font-bold text-sm text-slate-900">
                            {{ $pr->no_pr }}
                        </span>
                        @if($pr->status === 'pending')
                            <span class="px-2.5 py-0.5 rounded-md text-[10px] font-semibold bg-amber-50 text-amber-800 border border-amber-200/80">
                                PENDING
                            </span>
                        @elseif($pr->status === 'di_po')
                            <span class="px-2.5 py-0.5 rounded-md text-[10px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/80">
                                TELAH DI-PO-KAN
                            </span>
                        @elseif($pr->status === 'dibatalkan')
                            <span class="px-2.5 py-0.5 rounded-md text-[10px] font-semibold bg-rose-50 text-rose-800 border border-rose-200/80">
                                DIBATALKAN
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-md text-[10px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                {{ strtoupper($pr->status) }}
                            </span>
                        @endif
                    </div>

                    <!-- Body Kartu: Pembuat, Tanggal, Item -->
                    <div class="grid grid-cols-2 gap-2 text-xs py-2.5 border-y border-slate-100">
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-semibold">Pembuat</span>
                            <span class="font-medium text-slate-800 block mt-0.5 truncate">
                                {{ $pr->user->name ?? '-' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-semibold">Tanggal</span>
                            <span class="font-medium text-slate-700 block mt-0.5">
                                {{ \Carbon\Carbon::parse($pr->tanggal)->format('d/m/Y') }}
                            </span>
                        </div>
                        <div class="col-span-2 pt-1 flex items-center justify-between text-slate-700 bg-slate-50 px-2.5 py-1.5 rounded-lg mt-0.5">
                            <span class="text-slate-500 text-[11px]">Total Muatan:</span>
                            <span class="font-semibold text-slate-800 text-xs">{{ $pr->details->count() }} Macam Barang</span>
                        </div>
                    </div>

                    <!-- Footer Kartu: Tombol Aksi Bersih & Netral -->
                    <div class="flex flex-col gap-2">
                        <a href="{{ route('permintaan-pembelian.show', $pr->id) }}" 
                            class="w-full inline-flex items-center justify-center gap-1.5 py-2 px-3 text-xs font-semibold text-slate-700 bg-slate-50 hover:bg-slate-100 active:bg-slate-200 rounded-lg transition-all border border-slate-200">
                            <span>Lihat Detail PR</span>
                            <svg class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>

                        @if($pr->status === 'pending' && auth()->user()->hasRole(['admin', 'purchasing']))
                            <a href="{{ route('purchase-orders.create', ['pr_id' => $pr->id]) }}"
                                class="w-full inline-flex items-center justify-center gap-1.5 py-2 px-3 text-xs font-semibold text-white bg-slate-900 hover:bg-slate-800 rounded-lg transition-all shadow-2xs">
                                <span>Proses Jadi PO</span>
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl border border-dashed border-slate-200 p-8 text-center">
                    <svg class="h-8 w-8 text-slate-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="text-xs text-slate-500 font-medium">Belum ada pengajuan Purchase Requisition (PR).</p>
                </div>
            @endforelse
        </div>

        <!-- Desktop Table View (hidden sm:block) -->
        <div class="hidden sm:block">
            <x-table>
                <x-table.header>
                    <x-table.row>
                        <x-table.head class="whitespace-nowrap">No. PR</x-table.head>
                        <x-table.head>Tanggal</x-table.head>
                        <x-table.head>Pembuat</x-table.head>
                        <x-table.head class="text-center">Item</x-table.head>
                        <x-table.head class="text-center">Status</x-table.head>
                        <x-table.head class="text-right">Aksi</x-table.head>
                    </x-table.row>
                </x-table.header>
                <x-table.body>
                    @forelse($prs as $pr)
                        <x-table.row>
                            <x-table.cell>
                                <span class="font-mono font-bold text-slate-800 text-xs">{{ $pr->no_pr }}</span>
                            </x-table.cell>
                            <x-table.cell class="text-slate-600 text-xs whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($pr->tanggal)->format('d M Y H:i') }}
                            </x-table.cell>
                            <x-table.cell class="text-slate-800 font-medium text-xs">
                                {{ $pr->user->name ?? '-' }}
                            </x-table.cell>
                            <x-table.cell class="text-center text-slate-600 text-xs">
                                {{ $pr->details->count() }} Macam
                            </x-table.cell>
                            <x-table.cell class="text-center">
                                @if($pr->status === 'pending')
                                    <span class="px-2.5 py-0.5 rounded-md text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200/80">
                                        PENDING
                                    </span>
                                @elseif($pr->status === 'di_po')
                                    <span class="px-2.5 py-0.5 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/80">
                                        TELAH DI-PO-KAN
                                    </span>
                                @elseif($pr->status === 'dibatalkan')
                                    <span class="px-2.5 py-0.5 rounded-md text-xs font-semibold bg-rose-50 text-rose-800 border border-rose-200/80">
                                        DIBATALKAN
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-md text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ strtoupper($pr->status) }}
                                    </span>
                                @endif
                            </x-table.cell>
                            <x-table.cell class="text-right">
                                <div class="inline-flex items-center justify-end gap-1.5 whitespace-nowrap">
                                    <a href="{{ route('permintaan-pembelian.show', $pr->id) }}">
                                        <x-button variant="outline" size="sm" class="text-xs px-3 py-1">Detail</x-button>
                                    </a>
                                    
                                    @if($pr->status === 'pending' && auth()->user()->hasRole(['admin', 'purchasing']))
                                        <a href="{{ route('purchase-orders.create', ['pr_id' => $pr->id]) }}">
                                            <x-button variant="default" size="sm" class="text-xs px-3 py-1">
                                                Proses PO
                                            </x-button>
                                        </a>
                                    @endif
                                </div>
                            </x-table.cell>
                        </x-table.row>
                    @empty
                        <x-table.row>
                            <x-table.cell colspan="6" class="py-8 text-center text-slate-400">
                                Belum ada pengajuan Purchase Requisition (PR).
                            </x-table.cell>
                        </x-table.row>
                    @endforelse
                </x-table.body>
            </x-table>
        </div>

        @if($prs->hasPages())
            <div class="px-4 py-3 bg-white rounded-xl border border-slate-200 shadow-2xs">
                {{ $prs->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
