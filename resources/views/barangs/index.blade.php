<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                <x-back-button :href="route('dashboard')" />
                <div class="min-w-0">
                    <h2 class="font-bold text-xl sm:text-2xl text-slate-800 leading-tight truncate">
                        {{ __('Katalog Barang') }}
                    </h2>
                </div>
            </div>
            @if(auth()->user()->hasPermission('barangs_create'))
                <a href="{{ route('barangs.create') }}" class="w-full sm:w-auto shrink-0 self-start sm:self-center">
                    <x-button variant="indigo" class="w-full sm:w-auto justify-center shadow-sm">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Barang
                    </x-button>
                </a>
            @endif
        </div>
    </x-slot>

    <div class="space-y-4 sm:space-y-6">
        <!-- Filter Panel -->
        <x-card class="p-3.5 sm:p-5">
            <form method="GET" action="{{ route('barangs.index') }}" x-data="{ showAdvanced: {{ request()->anyFilled(['category_id', 'unit_id', 'status']) ? 'true' : 'false' }} }">
                <div class="flex flex-col sm:flex-row gap-2.5">
                    <div class="flex-grow">
                        <x-input type="text" name="search" id="search" placeholder="Cari kode / nama barang..." value="{{ request('search') }}" class="w-full text-xs" />
                    </div>
                    <div class="flex items-center gap-2">
                        <!-- Toggle Filter di Mobile -->
                        <button type="button" @click="showAdvanced = !showAdvanced" 
                            class="sm:hidden flex-1 px-3 py-2 text-xs font-semibold rounded-lg border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 flex items-center justify-center gap-1.5 transition-colors">
                            <svg class="h-3.5 w-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            <span>Filter</span>
                            @if(request()->anyFilled(['category_id', 'unit_id', 'status']))
                                <span class="h-2 w-2 rounded-full bg-indigo-600"></span>
                            @endif
                        </button>

                        <x-button type="submit" variant="default" class="text-xs px-4 flex-1 sm:flex-none justify-center">Cari</x-button>

                        @if(request()->anyFilled(['search', 'category_id', 'unit_id', 'status']))
                            <a href="{{ route('barangs.index') }}">
                                <x-button type="button" variant="outline" class="text-xs px-3">Reset</x-button>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Dropdown Filter: Toggle di mobile, grid di desktop -->
                <div :class="{ 'hidden': !showAdvanced, 'grid': showAdvanced }" class="hidden sm:grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 pt-3 mt-3 border-t border-slate-100">
                    <div>
                        <x-label for="category_id" class="text-xs font-semibold text-slate-600">Kelompok Barang</x-label>
                        <x-select name="category_id" id="category_id" class="mt-1 w-full text-xs">
                            <option value="">Semua Kelompok</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </x-select>
                    </div>
                    <div>
                        <x-label for="unit_id" class="text-xs font-semibold text-slate-600">Satuan</x-label>
                        <x-select name="unit_id" id="unit_id" class="mt-1 w-full text-xs">
                            <option value="">Semua Satuan</option>
                            @foreach($units as $u)
                                <option value="{{ $u->id }}" {{ request('unit_id') == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }}
                                </option>
                            @endforeach
                        </x-select>
                    </div>
                </div>
            </form>
        </x-card>

        <!-- 1. Mobile Card View (block sm:hidden) -->
        <div class="space-y-3 sm:hidden">
            @forelse($barangs as $index => $barang)
                <div class="bg-white rounded-xl border border-slate-200 shadow-2xs p-4 space-y-3 {{ !$barang->is_active ? 'opacity-70 bg-slate-50/50' : '' }}">
                    <div class="flex items-center justify-between gap-2">
                        <span class="font-mono font-bold text-xs text-indigo-600 bg-indigo-50 border border-indigo-100 rounded px-2 py-0.5">
                            {{ $barang->kode_barang }}
                        </span>
                        @if(!$barang->is_active)
                            <x-badge variant="secondary" class="text-[10px] py-0 px-1.5">NONAKTIF</x-badge>
                        @else
                            <x-badge variant="outline" class="text-[10px] py-0 px-1.5 text-emerald-700 bg-emerald-50 border-emerald-200">AKTIF</x-badge>
                        @endif
                    </div>

                    <div>
                        <h4 class="font-bold text-slate-800 text-sm leading-snug">{{ $barang->nama_barang }}</h4>
                        <div class="flex items-center gap-1.5 text-[11px] text-slate-500 mt-1">
                            <span>{{ $barang->category ? $barang->category->name : '-' }}</span>
                            <span>&bull;</span>
                            <span>{{ $barang->unit ? $barang->unit->name : $barang->satuan }}</span>
                        </div>
                        @if($barang->description)
                            <p class="text-[11px] text-slate-400 mt-1 line-clamp-2">{{ $barang->description }}</p>
                        @endif
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-1 border-t border-slate-100">
                        <a href="{{ route('barangs.show', $barang->id) }}" class="flex-1">
                            <x-button variant="outline" size="sm" class="w-full text-xs justify-center py-1.5">
                                Detail
                            </x-button>
                        </a>
                        @if(auth()->user()->hasPermission('barangs_edit'))
                            <a href="{{ route('barangs.edit', $barang->id) }}">
                                <x-button variant="outline" size="sm" class="px-2.5 py-1.5 text-xs text-slate-600">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </x-button>
                            </a>
                        @endif
                        @if(auth()->user()->hasPermission('barangs_delete'))
                            <x-modal-hapus name="{{ $barang->nama_barang }}" action="{{ route('barangs.destroy', $barang->id) }}" />
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl border border-dashed border-slate-200 p-8 text-center text-slate-400 text-xs">
                    Belum ada data barang.
                </div>
            @endforelse
        </div>

        <!-- 2. Desktop Table View (hidden sm:block) -->
        <div class="hidden sm:block">
            <x-table>
                <x-table.header>
                    <x-table.row>
                        <x-table.head class="w-12 text-center hidden sm:table-cell">No</x-table.head>
                        <x-table.head class="whitespace-nowrap">Kode</x-table.head>
                        <x-table.head>Nama Barang</x-table.head>
                        <x-table.head class="hidden sm:table-cell">Kelompok</x-table.head>
                        <x-table.head class="hidden md:table-cell">Satuan</x-table.head>
                        <x-table.head class="hidden lg:table-cell">Keterangan</x-table.head>
                        <x-table.head class="text-right">Aksi</x-table.head>
                    </x-table.row>
                </x-table.header>
                <x-table.body>
                    @forelse($barangs as $index => $barang)
                        <x-table.row class="{{ !$barang->is_active ? 'opacity-60 bg-slate-50/40' : '' }}">
                            <x-table.cell class="text-center text-xs text-slate-500 font-medium hidden sm:table-cell">
                                {{ $barangs->firstItem() + $index }}
                            </x-table.cell>
                            <x-table.cell class="font-mono font-bold text-indigo-600 text-xs whitespace-nowrap">
                                {{ $barang->kode_barang }}
                            </x-table.cell>
                            <x-table.cell>
                                <div class="flex flex-col">
                                    <div class="flex items-center space-x-2">
                                        <span class="font-semibold text-slate-900 text-xs sm:text-sm">{{ $barang->nama_barang }}</span>
                                        @if(!$barang->is_active)
                                            <x-badge variant="secondary" class="text-[9px] py-0 px-1">NONAKTIF</x-badge>
                                        @endif
                                    </div>
                                    <!-- Mobile metadata subtitle -->
                                    <div class="flex items-center gap-1.5 text-[11px] text-slate-500 sm:hidden mt-0.5">
                                        <span>{{ $barang->category ? $barang->category->name : '-' }}</span>
                                        <span>&bull;</span>
                                        <span>{{ $barang->unit ? $barang->unit->name : $barang->satuan }}</span>
                                    </div>
                                </div>
                            </x-table.cell>
                            <x-table.cell class="text-slate-600 hidden sm:table-cell text-xs">
                                {{ $barang->category ? $barang->category->name : '-' }}
                            </x-table.cell>
                            <x-table.cell class="text-slate-600 hidden md:table-cell text-xs">
                                {{ $barang->unit ? $barang->unit->name : $barang->satuan }}
                            </x-table.cell>
                            <x-table.cell class="text-slate-500 text-xs hidden lg:table-cell">
                                {{ $barang->description ?: '-' }}
                            </x-table.cell>
                            <x-table.cell class="text-right whitespace-nowrap">
                                <div class="flex items-center justify-end space-x-1.5 sm:space-x-2">
                                    <a href="{{ route('barangs.stock-card', $barang->id) }}" class="p-1.5 text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-md transition-colors border border-blue-200" title="Kartu Stok Biru">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('barangs.show', $barang->id) }}">
                                        <x-button variant="outline" size="sm" class="text-xs px-2 sm:px-3 py-1">Detail</x-button>
                                    </a>
                                    @if(auth()->user()->hasPermission('barangs_edit'))
                                        <a href="{{ route('barangs.edit', $barang->id) }}" class="p-1.5 text-slate-500 hover:text-indigo-600 hover:bg-slate-100 rounded-md transition-colors border border-slate-300" title="Edit">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                    @endif
                                    @if(auth()->user()->hasPermission('barangs_delete'))
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
        </div>

        @if($barangs->hasPages())
            <div class="px-6 py-4 bg-white rounded-lg border border-slate-300">
                {{ $barangs->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
