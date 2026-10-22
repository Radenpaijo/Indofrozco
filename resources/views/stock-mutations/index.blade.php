<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-4 min-w-0">
                <x-back-button />
                <div class="min-w-0">
                    <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                        {{ __('Mutasi Lokasi / Rak Gudang') }}
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">Pemindahan stok fisik antar lokasi gudang dan rak.</p>
                </div>
            </div>
            <a href="{{ route('stock-mutations.create') }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-md transition-all shrink-0">
                <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Mutasi Stok Baru
            </a>
        </div>
    </x-slot>

    <div class="w-full space-y-6">

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

        @if($mutations->hasPages())
            <div class="px-6 py-4 bg-white rounded-lg border border-slate-300">
                {{ $mutations->links() }}
            </div>
        @endif
    </div>
    </div>
</x-app-layout>
