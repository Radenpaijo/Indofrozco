<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-4 min-w-0">
                <x-back-button />
                <div class="min-w-0">
                    <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                        {{ __('Stock Adjustment (Penyesuaian Stok Resmi)') }}
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">Penyesuaian kuantitas stok akibat barang rusak/hilang.</p>
                </div>
            </div>
            <a href="{{ route('stock-adjustments.create') }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-md transition-all shrink-0">
                <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Buat Adjustment Stok
            </a>
        </div>
    </x-slot>

    <div class="w-full space-y-6">

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

        @if($adjustments->hasPages())
            <div class="px-6 py-4 bg-white rounded-lg border border-slate-300">
                {{ $adjustments->links() }}
            </div>
        @endif
    </div>
    </div>
</x-app-layout>
