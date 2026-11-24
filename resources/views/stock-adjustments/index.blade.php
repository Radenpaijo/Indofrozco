<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                <x-back-button :href="route('dashboard')" />
                <div class="min-w-0">
                    <h2 class="font-bold text-xl sm:text-2xl text-slate-800 leading-tight truncate">
                        {{ __('Stock Adjustment') }}
                    </h2>
                </div>
            </div>
            <a href="{{ route('stock-adjustments.create') }}" class="w-full sm:w-auto shrink-0 self-start sm:self-center">
                <x-button variant="indigo" class="w-full sm:w-auto justify-center shadow-sm">
                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Buat Adjustment Stok
                </x-button>
            </a>
        </div>
    </x-slot>

    <div class="w-full space-y-4 sm:space-y-6">

        <!-- 1. Mobile Card View (block sm:hidden) -->
        <div class="space-y-3 sm:hidden">
            @forelse($adjustments as $adj)
                <div class="bg-white rounded-xl border border-slate-200 shadow-2xs p-4 space-y-3">
                    <div class="flex items-center justify-between gap-2">
                        <span class="font-mono font-bold text-xs text-indigo-600 bg-indigo-50 border border-indigo-100 rounded px-2 py-0.5">
                            {{ $adj->nomor_adjustment }}
                        </span>
                        <x-badge variant="outline" class="text-[10px]">
                            {{ strtoupper($adj->status) }}
                        </x-badge>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-xs py-2 px-3 bg-slate-50 rounded-lg border border-slate-100">
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-semibold">Tanggal</span>
                            <span class="font-medium text-slate-800 block mt-0.5">
                                {{ $adj->tanggal->format('d M Y') }}
                            </span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-semibold">Ref. Opname</span>
                            <span class="font-mono font-medium text-slate-800 block mt-0.5 text-xs truncate">
                                {{ $adj->stockOpname ? $adj->stockOpname->nomor_opname : 'Manual' }}
                            </span>
                        </div>
                        <div class="col-span-2 pt-1 border-t border-slate-200/60 flex items-center justify-between text-slate-500 text-[11px]">
                            <span>Dibuat Oleh:</span>
                            <span class="font-medium text-slate-700">{{ $adj->user->name }}</span>
                        </div>
                    </div>

                    <div class="pt-0.5">
                        <a href="{{ route('stock-adjustments.show', $adj) }}" class="w-full block">
                            <x-button variant="outline" size="sm" class="w-full justify-center text-xs py-1.5">
                                Lihat Detail Adjustment
                            </x-button>
                        </a>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl border border-dashed border-slate-200 p-8 text-center text-slate-400 text-xs">
                    Belum ada transaksi adjustment stok.
                </div>
            @endforelse
        </div>

        <!-- 2. Desktop Table View (hidden sm:block) -->
        <div class="hidden sm:block">
            <x-table>
                <x-table.header>
                    <x-table.row>
                        <x-table.head>Nomor Adjustment</x-table.head>
                        <x-table.head>Tanggal</x-table.head>
                        <x-table.head>Referensi Opname</x-table.head>
                        <x-table.head>Dibuat Oleh</x-table.head>
                        <x-table.head>Status</x-table.head>
                        <x-table.head class="text-right">Aksi</x-table.head>
                    </x-table.row>
                </x-table.header>
                <x-table.body>
                    @forelse($adjustments as $adj)
                        <x-table.row>
                            <x-table.cell class="font-mono font-bold text-indigo-600">{{ $adj->nomor_adjustment }}</x-table.cell>
                            <x-table.cell class="text-slate-700 font-medium">{{ $adj->tanggal->format('d M Y') }}</x-table.cell>
                            <x-table.cell class="text-slate-600 font-mono text-xs">{{ $adj->stockOpname ? $adj->stockOpname->nomor_opname : 'Manual / Non-Opname' }}</x-table.cell>
                            <x-table.cell class="font-semibold text-slate-800">{{ $adj->user->name }}</x-table.cell>
                            <x-table.cell>
                                <x-badge variant="outline">
                                    {{ strtoupper($adj->status) }}
                                </x-badge>
                            </x-table.cell>
                            <x-table.cell class="text-right">
                                <a href="{{ route('stock-adjustments.show', $adj) }}">
                                    <x-button variant="outline" size="sm">Detail</x-button>
                                </a>
                            </x-table.cell>
                        </x-table.row>
                    @empty
                        <x-table.row>
                            <x-table.cell colspan="6" class="p-8 text-center text-slate-400">Belum ada transaksi adjustment stok.</x-table.cell>
                        </x-table.row>
                    @endforelse
                </x-table.body>
            </x-table>
        </div>

        @if($adjustments->hasPages())
            <div class="px-6 py-4 bg-white rounded-lg border border-slate-300">
                {{ $adjustments->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
