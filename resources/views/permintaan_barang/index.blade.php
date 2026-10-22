<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-4 min-w-0">
                <x-back-button />
                <div class="min-w-0">
                    <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                        {{ __('Permintaan Barang (Produksi ➔ Gudang)') }}
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">Daftar permohonan pengambilan barang untuk keperluan produksi.</p>
                </div>
            </div>
            @if(auth()->user()->hasRole(['admin', 'produksi']))
                <a href="{{ route('permintaan-barang.create') }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-md transition-all shrink-0">
                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Ajukan Permintaan
                </a>
            @endif
        </div>
    </x-slot>

    <div class="w-full space-y-6">

        <!-- Table Card -->
        <x-table>
            <x-table.header>
                <x-table.row>
                    <x-table.head>No. Permintaan</x-table.head>
                    <x-table.head>Tanggal Diajukan</x-table.head>
                    <x-table.head>Pemohon (Produksi)</x-table.head>
                    <x-table.head>Jumlah Item</x-table.head>
                    <x-table.head>Status</x-table.head>
                    <x-table.head class="text-right">Aksi</x-table.head>
                </x-table.row>
            </x-table.header>
            <x-table.body>
                @forelse($permintaans as $permintaan)
                    <x-table.row>
                        <x-table.cell class="font-mono font-semibold text-slate-800">
                            {{ $permintaan->no_permintaan }}
                        </x-table.cell>
                        <x-table.cell class="text-slate-600">
                            {{ \Carbon\Carbon::parse($permintaan->tanggal)->format('d M Y H:i') }}
                        </x-table.cell>
                        <x-table.cell class="text-slate-900 font-medium">
                            {{ $permintaan->user->name ?? '-' }}
                        </x-table.cell>
                        <x-table.cell class="text-slate-600">
                            {{ $permintaan->details->count() }} Item
                        </x-table.cell>
                        <x-table.cell>
                            @if($permintaan->status === 'pending')
                                <x-badge variant="outline">PENDING</x-badge>
                            @elseif($permintaan->status === 'selesai')
                                <x-badge variant="secondary">DISETUJUI (SELESAI)</x-badge>
                            @elseif($permintaan->status === 'ditolak')
                                <x-badge variant="destructive">DITOLAK</x-badge>
                            @else
                                <x-badge variant="outline">{{ strtoupper($permintaan->status) }}</x-badge>
                            @endif
                        </x-table.cell>
                        <x-table.cell class="text-right">
                            <a href="{{ route('permintaan-barang.show', $permintaan->id) }}">
                                <x-button variant="outline" size="sm">Detail Permintaan</x-button>
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

        @if($permintaans->hasPages())
            <div class="px-6 py-4 bg-white rounded-lg border border-slate-300">
                {{ $permintaans->links() }}
            </div>
        @endif
    </div>
    </div>
</x-app-layout>
