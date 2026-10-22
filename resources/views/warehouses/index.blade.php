<x-app-layout>

    <x-slot name="header">

        {{-- Header: Kembali + Judul | Tambah --}}
        <div class="flex items-center justify-between gap-4">

            {{-- Kiri --}}
            <div class="flex items-center gap-4 min-w-0">

                <x-back-button />

                <div class="min-w-0">
                    <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                        {{ __('Master Data Gudang') }}
                    </h2>

                    <p class="text-xs text-slate-500 mt-1">
                        Manajemen gudang penyimpanan dan alokasi rak.
                    </p>
                </div>

            </div>

            {{-- Kanan --}}
            <a href="{{ route('warehouses.create') }}" class="shrink-0">
                <x-button variant="indigo">
                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Gudang
                </x-button>
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <x-table>
            <x-table.header>
                <x-table.row>
                    <x-table.head>Kode Gudang</x-table.head>
                    <x-table.head>Nama Gudang</x-table.head>
                    <x-table.head>Tipe Gudang</x-table.head>
                    <x-table.head class="text-center">Total Lokasi/Rak</x-table.head>
                    <x-table.head class="text-center">Status</x-table.head>
                    <x-table.head class="text-right">Aksi</x-table.head>
                </x-table.row>
            </x-table.header>
            <x-table.body>
                @forelse($warehouses as $wh)
                    <x-table.row>
                        <x-table.cell class="font-mono font-bold text-indigo-600 text-xs">{{ $wh->code }}</x-table.cell>
                        <x-table.cell class="font-semibold text-slate-900">{{ $wh->name }}</x-table.cell>
                        <x-table.cell>
                            <x-badge variant="secondary">
                                {{ $wh->warehouseType ? $wh->warehouseType->name : '-' }}
                            </x-badge>
                        </x-table.cell>
                        <x-table.cell class="text-center">
                            <x-badge variant="secondary">
                                {{ $wh->locations_count }} Rak
                            </x-badge>
                        </x-table.cell>
                        <x-table.cell class="text-center">
                            @if($wh->status === 'active')
                                <x-badge variant="outline">Aktif</x-badge>
                            @else
                                <x-badge variant="secondary">Nonaktif</x-badge>
                            @endif
                        </x-table.cell>
                        <x-table.cell class="text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('warehouses.edit', $wh) }}" class="p-2 text-slate-500 hover:text-indigo-600 hover:bg-slate-100 rounded-md transition-colors border border-slate-300" title="Edit">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <x-modal-hapus name="{{ $wh->name }}" action="{{ route('warehouses.destroy', $wh) }}" />
                            </div>
                        </x-table.cell>
                    </x-table.row>
                @empty
                    <x-table.row>
                        <x-table.cell colspan="6" class="py-8 text-center text-slate-400">Belum ada data gudang.</x-table.cell>
                    </x-table.row>
                @endforelse
            </x-table.body>
        </x-table>

        @if($warehouses->hasPages())
            <div class="px-6 py-4 bg-white rounded-lg border border-slate-300">
                {{ $warehouses->links() }}
            </div>
        @endif
    </div>

    </div>

</x-app-layout>