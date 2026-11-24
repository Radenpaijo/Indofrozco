<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                <x-back-button :href="route('dashboard')" />
                <div class="min-w-0">
                    <h2 class="font-bold text-xl sm:text-2xl text-slate-800 leading-tight truncate">
                        {{ __('Katalog Supplier') }}
                    </h2>
                </div>
            </div>
            @if(auth()->user()->hasPermission('suppliers_manage'))
                <a href="{{ route('suppliers.create') }}" class="w-full sm:w-auto shrink-0 self-start sm:self-center">
                    <x-button variant="indigo" class="w-full sm:w-auto justify-center shadow-sm">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Supplier
                    </x-button>
                </a>
            @endif
        </div>
    </x-slot>

    <div class="w-full space-y-4 sm:space-y-6">
            <!-- Filter & Search Panel -->
            <x-card class="p-3.5 sm:p-5">
                <form method="GET" action="{{ route('suppliers.index') }}" class="flex flex-col sm:flex-row gap-2.5">
                    <div class="flex-1">
                        <x-input type="text" name="search" id="search" placeholder="Cari nama supplier, kontak, alamat..." value="{{ request('search') }}" class="w-full text-xs" />
                    </div>
                    <div class="flex items-center gap-2">
                        <x-button type="submit" variant="default" class="text-xs px-4 flex-1 sm:flex-none justify-center">Cari</x-button>
                        @if(request()->filled('search'))
                            <a href="{{ route('suppliers.index') }}">
                                <x-button type="button" variant="outline" class="text-xs px-3">Reset</x-button>
                            </a>
                        @endif
                    </div>
                </form>
            </x-card>

            <!-- Desktop Table View -->
            <div class="hidden sm:block">
                <x-table>
                    <x-table.header>
                        <x-table.row>
                            <x-table.head>Nama Supplier</x-table.head>
                            <x-table.head class="hidden sm:table-cell">Kontak / Telepon</x-table.head>
                            <x-table.head class="hidden md:table-cell">Alamat</x-table.head>
                            <x-table.head>Status</x-table.head>
                            <x-table.head class="text-right">Aksi</x-table.head>
                        </x-table.row>
                    </x-table.header>
                    <x-table.body>
                        @forelse($suppliers as $supplier)
                            <x-table.row class="{{ !$supplier->is_active ? 'opacity-60 bg-slate-50/40' : '' }}">
                                <x-table.cell>
                                    <div class="font-semibold text-slate-900 text-xs sm:text-sm">{{ $supplier->nama_supplier }}</div>
                                </x-table.cell>
                                <x-table.cell class="text-slate-600 hidden sm:table-cell text-xs">
                                    {{ $supplier->kontak ?? '-' }}
                                </x-table.cell>
                                <x-table.cell class="text-slate-500 hidden md:table-cell text-xs">
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
                                    @if(auth()->user()->hasPermission('suppliers_manage'))
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
            </div>

            <!-- Mobile Card View -->
            <div class="block sm:hidden space-y-3">
                @forelse($suppliers as $supplier)
                    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-2xs space-y-3 {{ !$supplier->is_active ? 'opacity-70 bg-slate-50/50' : '' }}">
                        <div class="flex items-start justify-between gap-2">
                            <h3 class="font-bold text-slate-900 text-sm">
                                {{ $supplier->nama_supplier }}
                            </h3>
                            <div>
                                @if($supplier->is_active)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                        Nonaktif
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="space-y-1.5 text-xs text-slate-600 pt-2 border-t border-slate-100">
                            @if($supplier->kontak)
                                <div class="flex items-center gap-2 text-slate-700">
                                    <svg class="h-3.5 w-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    <a href="tel:{{ $supplier->kontak }}" class="text-indigo-600 font-semibold hover:underline">{{ $supplier->kontak }}</a>
                                </div>
                            @endif

                            @if($supplier->alamat)
                                <div class="flex items-start gap-2 text-slate-500">
                                    <svg class="h-3.5 w-3.5 text-slate-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span>{{ $supplier->alamat }}</span>
                                </div>
                            @endif
                        </div>

                        @if(auth()->user()->hasPermission('suppliers_manage'))
                            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                                <a href="{{ route('suppliers.edit', $supplier->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-slate-700 bg-slate-50 hover:bg-slate-100 border border-slate-300 rounded-lg transition-colors">
                                    <svg class="h-3.5 w-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    <span>Edit</span>
                                </a>
                                <x-modal-hapus name="{{ $supplier->nama_supplier }}" action="{{ route('suppliers.destroy', $supplier->id) }}" />
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="bg-white rounded-xl border border-slate-200 p-8 text-center text-slate-400 text-xs">
                        Supplier tidak ditemukan.
                    </div>
                @endforelse
            </div>

            @if($suppliers->hasPages())
                <div class="px-4 sm:px-6 py-3 sm:py-4 bg-white rounded-xl border border-slate-300">
                    {{ $suppliers->links() }}
                </div>
            @endif
    </div>
</x-app-layout>
