<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-4 min-w-0">
                <x-back-button />
                <div class="min-w-0">
                    <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                        {{ __('Katalog Barang') }}
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">Kelola data dasar bahan baku dan barang.</p>
                </div>
            </div>
            @if(auth()->user()->hasRole(['admin', 'gudang']))
                <a href="{{ route('barangs.create') }}" class="shrink-0">
                    <x-button variant="indigo">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Barang
                    </x-button>
                </a>
            @endif
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Filter Panel -->
        <x-card class="p-5">
            <form method="GET" action="{{ route('barangs.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <x-label for="search">Pencarian</x-label>
                    <x-input type="text" name="search" id="search" placeholder="Cari kode / nama barang..." value="{{ request('search') }}" class="mt-1" />
                </div>
                <div>
                    <x-label for="category_id">Kelompok Barang</x-label>
                    <x-select name="category_id" id="category_id" class="mt-1">
                        <option value="">Semua Kelompok</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </x-select>
                </div>
                <div>
                    <x-label for="unit_id">Satuan</x-label>
                    <x-select name="unit_id" id="unit_id" class="mt-1">
                        <option value="">Semua Satuan</option>
                        @foreach($units as $u)
                            <option value="{{ $u->id }}" {{ request('unit_id') == $u->id ? 'selected' : '' }}>
                                {{ $u->name }} ({{ $u->code }})
                            </option>
                        @endforeach
                    </x-select>
                </div>
                <div class="flex items-end space-x-2">
                    <x-button type="submit" variant="default" class="flex-1">Filter</x-button>
                    @if(request()->anyFilled(['search', 'category_id', 'unit_id']))
                        <a href="{{ route('barangs.index') }}">
                            <x-button type="button" variant="outline">Reset</x-button>
                        </a>
                    @endif
                </div>
            </form>
        </x-card>

        <!-- Table Card -->
        <x-table>
            <x-table.header>
                <x-table.row>
                    <x-table.head class="w-12 text-center">No</x-table.head>
                    <x-table.head>Kode Barang</x-table.head>
                    <x-table.head>Nama Barang</x-table.head>
                    <x-table.head>Kelompok</x-table.head>
                    <x-table.head>Satuan</x-table.head>
                    <x-table.head>Keterangan</x-table.head>
                    <x-table.head class="text-right">Aksi</x-table.head>
                </x-table.row>
            </x-table.header>
            <x-table.body>
                @forelse($barangs as $index => $barang)
                    <x-table.row class="{{ !$barang->is_active ? 'opacity-60 bg-slate-50/40' : '' }}">
                        <x-table.cell class="text-center text-xs text-slate-500 font-medium">
                            {{ $barangs->firstItem() + $index }}
                        </x-table.cell>
                        <x-table.cell class="font-mono font-bold text-indigo-600 text-xs">
                            {{ $barang->kode_barang }}
                        </x-table.cell>
                        <x-table.cell>
                            <div class="flex items-center space-x-2">
                                <span class="font-semibold text-slate-900">{{ $barang->nama_barang }}</span>
                                @if(!$barang->is_active)
                                    <x-badge variant="secondary">NONAKTIF</x-badge>
                                @endif
                            </div>
                        </x-table.cell>
                        <x-table.cell class="text-slate-600">
                            {{ $barang->category ? $barang->category->name : '-' }}
                        </x-table.cell>
                        <x-table.cell class="text-slate-600">
                            {{ $barang->unit ? $barang->unit->name : $barang->satuan }}
                        </x-table.cell>
                        <x-table.cell class="text-slate-500 text-xs">
                            {{ $barang->description ?: '-' }}
                        </x-table.cell>
                        <x-table.cell class="text-right whitespace-nowrap">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('barangs.show', $barang->id) }}">
                                    <x-button variant="outline" size="sm">Detail</x-button>
                                </a>
                                @if(auth()->user()->hasRole(['admin', 'gudang']))
                                    <a href="{{ route('barangs.edit', $barang->id) }}" class="p-2 text-slate-500 hover:text-indigo-600 hover:bg-slate-100 rounded-md transition-colors border border-slate-300" title="Edit">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <x-modal-hapus name="{{ $barang->nama_barang }}" action="{{ route('barangs.destroy', $barang->id) }}" />
                                @endif
                            </div>
                        </x-table.cell>
                    </x-table.row>
                @empty
                    <x-table.row>
                        <x-table.cell colspan="7" class="py-8 text-center text-slate-400">
                            Belum ada data barang.
                        </x-table.cell>
                    </x-table.row>
                @endforelse
            </x-table.body>
        </x-table>

        @if($barangs->hasPages())
            <div class="px-6 py-4 bg-white rounded-lg border border-slate-300">
                {{ $barangs->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
