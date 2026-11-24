<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                <x-back-button :href="route('dashboard')" />
                <div class="min-w-0">
                    <h2 class="font-bold text-xl sm:text-2xl text-slate-800 leading-tight truncate">
                        {{ __('Mutasi Lokasi / Rak') }}
                    </h2>
                </div>
            </div>
            <a href="{{ route('stock-mutations.create') }}" class="w-full sm:w-auto shrink-0 self-start sm:self-center">
                <x-button variant="indigo" class="w-full sm:w-auto justify-center shadow-sm">
                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Mutasi Stok Baru
                </x-button>
            </a>
        </div>
    </x-slot>

    <div class="w-full space-y-4 sm:space-y-6">

        <!-- 1. Mobile Card View (block sm:hidden) -->
        <div class="space-y-3 sm:hidden">
            @forelse($mutations as $mut)
                <div class="bg-white rounded-xl border border-slate-200 shadow-2xs p-4 space-y-3">
                    <div class="flex items-center justify-between gap-2">
                        <span class="font-mono font-bold text-xs text-indigo-600 bg-indigo-50 border border-indigo-100 rounded px-2 py-0.5">
                            {{ $mut->nomor_mutasi }}
                        </span>
                        <x-badge variant="outline" class="text-[10px]">
                            {{ strtoupper($mut->status) }}
                        </x-badge>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-xs py-2 px-3 bg-slate-50 rounded-lg border border-slate-100">
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-semibold">Tanggal</span>
                            <span class="font-medium text-slate-800 block mt-0.5">
                                {{ $mut->tanggal->format('d M Y') }}
                            </span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-semibold">Total Item</span>
                            <span class="font-semibold text-slate-800 block mt-0.5">
                                {{ $mut->details->count() }} Barang
                            </span>
                        </div>
                        <div class="col-span-2 pt-1 border-t border-slate-200/60 flex items-center justify-between text-slate-500 text-[11px]">
                            <span>Dibuat Oleh:</span>
                            <span class="font-medium text-slate-700">{{ $mut->user->name }}</span>
                        </div>
                    </div>

                    <div class="pt-0.5">
                        <a href="{{ route('stock-mutations.show', $mut) }}" class="w-full block">
                            <x-button variant="outline" size="sm" class="w-full justify-center text-xs py-1.5">
                                Lihat Detail Mutasi
                            </x-button>
                        </a>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl border border-dashed border-slate-200 p-8 text-center text-slate-400 text-xs">
                    Belum ada riwayat mutasi barang antar rak.
                </div>
            @endforelse
        </div>

        <!-- 2. Desktop Table View (hidden sm:block) -->
        <div class="hidden sm:block">
            <x-table>
                <x-table.header>
                    <x-table.row>
                        <x-table.head>Nomor Mutasi</x-table.head>
                        <x-table.head>Tanggal</x-table.head>
                        <x-table.head>Item Mutasi</x-table.head>
                        <x-table.head>Dibuat Oleh</x-table.head>
                        <x-table.head>Status</x-table.head>
                        <x-table.head class="text-right">Aksi</x-table.head>
                    </x-table.row>
                </x-table.header>
                <x-table.body>
                    @forelse($mutations as $mut)
                        <x-table.row>
                            <x-table.cell class="font-mono font-bold text-indigo-600">{{ $mut->nomor_mutasi }}</x-table.cell>
                            <x-table.cell class="text-slate-700 font-medium">{{ $mut->tanggal->format('d M Y') }}</x-table.cell>
                            <x-table.cell class="font-semibold text-slate-800">{{ $mut->details->count() }} Barang</x-table.cell>
                            <x-table.cell class="font-semibold text-slate-800">{{ $mut->user->name }}</x-table.cell>
                            <x-table.cell>
                                <x-badge variant="outline">
                                    {{ strtoupper($mut->status) }}
                                </x-badge>
                            </x-table.cell>
                            <x-table.cell class="text-right">
                                <a href="{{ route('stock-mutations.show', $mut) }}">
                                    <x-button variant="outline" size="sm">Detail</x-button>
                                </a>
                            </x-table.cell>
                        </x-table.row>
                    @empty
                        <x-table.row>
                            <x-table.cell colspan="6" class="p-8 text-center text-slate-400">Belum ada riwayat mutasi barang antar rak.</x-table.cell>
                        </x-table.row>
                    @endforelse
                </x-table.body>
            </x-table>
        </div>

        @if($mutations->hasPages())
            <div class="px-6 py-4 bg-white rounded-lg border border-slate-300">
                {{ $mutations->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
