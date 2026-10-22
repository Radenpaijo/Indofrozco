<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-4 min-w-0">
                <x-back-button />
                <div class="min-w-0">
                    <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                        {{ __('Stock Opname (Perhitungan Stok Fisik)') }}
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">Audit dan rekonsiliasi jumlah fisik persediaan barang.</p>
                </div>
            </div>
            <a href="{{ route('stock-opnames.create') }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-md transition-all shrink-0">
                <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Buat Sesi Opname
            </a>
        </div>
    </x-slot>

    <div class="w-full space-y-6">

        <x-table>
            <x-table.header>
                <x-table.row>
                    <x-table.head>Nomor Opname</x-table.head>
                    <x-table.head>Tanggal</x-table.head>
                    <x-table.head>Gudang / Lokasi</x-table.head>
                    <x-table.head>Petugas</x-table.head>
                    <x-table.head>Status</x-table.head>
                    <x-table.head class="text-right">Aksi</x-table.head>
                </x-table.row>
            </x-table.header>
            <x-table.body>
                @forelse($opnames as $op)
                    <x-table.row>
                        <x-table.cell class="font-mono font-bold text-indigo-600">{{ $op->nomor_opname }}</x-table.cell>
                        <x-table.cell class="text-slate-700 font-medium">{{ $op->tanggal->format('d M Y') }}</x-table.cell>
                        <x-table.cell class="text-slate-600">{{ $op->warehouse->name ?? 'Semua Gudang' }} {{ $op->location ? '('.$op->location->name.')' : '' }}</x-table.cell>
                        <x-table.cell class="font-semibold text-slate-800">{{ $op->user->name }}</x-table.cell>
                        <x-table.cell>
                            <x-badge variant="outline">
                                {{ strtoupper($op->status) }}
                            </x-badge>
                        </x-table.cell>
                        <x-table.cell class="text-right">
                            <a href="{{ route('stock-opnames.show', $op) }}">
                                <x-button variant="outline" size="sm">Detail</x-button>
                            </a>
                        </x-table.cell>
                    </x-table.row>
                @empty
                    <x-table.row>
                        <x-table.cell colspan="6" class="p-8 text-center text-slate-400">Belum ada riwayat stock opname.</x-table.cell>
                    </x-table.row>
                @endforelse
            </x-table.body>
        </x-table>

        @if($opnames->hasPages())
            <div class="px-6 py-4 bg-white rounded-lg border border-slate-300">
                {{ $opnames->links() }}
            </div>
        @endif
    </div>
    </div>
</x-app-layout>
