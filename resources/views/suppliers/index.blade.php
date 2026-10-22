<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-4 min-w-0">
                <x-back-button />
                <div class="min-w-0">
                    <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                        {{ __('Katalog Master Supplier') }}
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">Daftar mitra pemasok & vendor bahan baku.</p>
                </div>
            </div>
            @if(auth()->user()->hasRole(['admin', 'purchasing']))
                <a href="{{ route('suppliers.create') }}" class="shrink-0">
                    <x-button variant="indigo">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Supplier
                    </x-button>
                </a>
            @endif
        </div>
    </x-slot>

    <div class="w-full space-y-6">
            <!-- Filter & Search Panel -->
            <x-card class="p-5">
                <form method="GET" action="{{ route('suppliers.index') }}" class="flex gap-4">
                    <div class="flex-1">
                        <x-label for="search">Pencarian</x-label>
                        <x-input type="text" name="search" id="search" placeholder="Cari nama supplier, kontak, alamat..." value="{{ request('search') }}" class="mt-1" />
                    </div>
                    <div class="flex items-end space-x-2">
                        <x-button type="submit" variant="default">Filter</x-button>
                        <a href="{{ route('suppliers.index') }}">
                            <x-button type="button" variant="outline">Reset</x-button>
                        </a>
                    </div>
                </form>
            </x-card>

            <!-- Table Card -->
            <x-table>
                <x-table.header>
                    <x-table.row>
                        <x-table.head>Nama Supplier</x-table.head>
                        <x-table.head>Kontak / Telepon</x-table.head>
                        <x-table.head>Alamat</x-table.head>
                        <x-table.head>Status</x-table.head>
                        <x-table.head class="text-right">Aksi</x-table.head>
                    </x-table.row>
                </x-table.header>
                <x-table.body>
                    @forelse($suppliers as $supplier)
                        <x-table.row class="{{ !$supplier->is_active ? 'opacity-60 bg-slate-50/40' : '' }}">
                            <x-table.cell class="font-semibold text-slate-900">
                                {{ $supplier->nama_supplier }}
                            </x-table.cell>
                            <x-table.cell class="text-slate-600">
                                {{ $supplier->kontak ?? '-' }}
                            </x-table.cell>
                            <x-table.cell class="text-slate-500">
                                {{ $supplier->alamat ?? '-' }}
                            </x-table.cell>
                            <x-table.cell>
                                @if($supplier->is_active)
                                    <x-badge variant="outline">Aktif</x-badge>
                                @else
                                    <x-badge variant="secondary">Nonaktif</x-badge>
                                @endif
                            </x-table.cell>
                            <x-table.cell class="text-right whitespace-nowrap">
                                @if(auth()->user()->hasRole(['admin', 'purchasing']))
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('suppliers.edit', $supplier->id) }}" class="p-2 text-slate-500 hover:text-indigo-600 hover:bg-slate-100 rounded-md transition-colors border border-slate-300" title="Edit">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <x-modal-hapus name="{{ $supplier->nama_supplier }}" action="{{ route('suppliers.destroy', $supplier->id) }}" />
                                    </div>
                                @else
                                    <span class="text-gray-400 text-xs">-</span>
                                @endif
                            </x-table.cell>
                        </x-table.row>
                    @empty
                        <x-table.row>
                            <x-table.cell colspan="5" class="py-8 text-center text-slate-400">
                                Supplier tidak ditemukan.
                            </x-table.cell>
                        </x-table.row>
                    @endforelse
                </x-table.body>
            </x-table>

            @if($suppliers->hasPages())
                <div class="px-6 py-4 bg-white rounded-lg border border-slate-300">
                    {{ $suppliers->links() }}
                </div>
            @endif
            </div>
        </div>
    </div>
</x-app-layout>
