<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                <x-back-button :href="route('dashboard')" />
                <div class="min-w-0">
                    <h2 class="font-bold text-xl sm:text-2xl text-slate-800 leading-tight truncate">
                        {{ __('Satuan Barang') }}
                    </h2>
                </div>
            </div>
            @if(auth()->user()->hasPermission('units_manage'))
                <a href="{{ route('units.create') }}" class="w-full sm:w-auto shrink-0 self-start sm:self-center">
                    <x-button variant="indigo" class="w-full sm:w-auto justify-center shadow-sm">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Satuan
                    </x-button>
                </a>
            @endif
        </div>
    </x-slot>

    <div class="space-y-4 sm:space-y-6">
        <!-- Desktop Table View -->
        <div class="hidden sm:block">
            <x-table>
                <x-table.header>
                    <x-table.row>
                        <x-table.head class="whitespace-nowrap">Kode</x-table.head>
                        <x-table.head>Nama Satuan</x-table.head>
                        <x-table.head class="text-center">Total Barang</x-table.head>
                        <x-table.head class="text-center">Status</x-table.head>
                        <x-table.head class="hidden md:table-cell">Keterangan</x-table.head>
                        <x-table.head class="text-right">Aksi</x-table.head>
                    </x-table.row>
                </x-table.header>
                <x-table.body>
                    @forelse($units as $unit)
                        <x-table.row>
                            <x-table.cell class="font-mono font-bold text-indigo-600 text-xs whitespace-nowrap">{{ $unit->code }}</x-table.cell>
                            <x-table.cell>
                                <div class="font-semibold text-slate-900 text-xs sm:text-sm">{{ $unit->name }}</div>
                            </x-table.cell>
                            <x-table.cell class="text-center">
                                <x-badge variant="secondary">
                                    {{ $unit->barangs_count }} Barang
                                </x-badge>
                            </x-table.cell>
                            <x-table.cell class="text-center">
                                @if($unit->status === 'active')
                                    <x-badge variant="outline" class="text-[10px] py-0 px-1.5">Aktif</x-badge>
                                @else
                                    <x-badge variant="secondary" class="text-[10px] py-0 px-1.5">Nonaktif</x-badge>
                                @endif
                            </x-table.cell>
                            <x-table.cell class="text-slate-500 text-xs hidden md:table-cell">{{ $unit->description ?? '-' }}</x-table.cell>
                            <x-table.cell class="text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    @if(auth()->user()->hasPermission('units_manage'))
                                        <a href="{{ route('units.edit', $unit) }}" class="p-2 text-slate-500 hover:text-indigo-600 hover:bg-slate-100 rounded-md transition-colors border border-slate-300" title="Edit">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <x-modal-hapus name="{{ $unit->name }}" action="{{ route('units.destroy', $unit) }}" />
                                    @endif
                                </div>
                            </x-table.cell>
                        </x-table.row>
                    @empty
                        <x-table.row>
                            <x-table.cell colspan="6" class="py-8 text-center text-slate-400">Belum ada data satuan barang.</x-table.cell>
                        </x-table.row>
                    @endforelse
                </x-table.body>
            </x-table>
        </div>

        <!-- Mobile Card View -->
        <div class="block sm:hidden space-y-3">
            @forelse($units as $unit)
                <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-2xs space-y-3">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <span class="inline-block px-2 py-0.5 font-mono text-[11px] font-bold text-indigo-700 bg-indigo-50 border border-indigo-200 rounded">
                                {{ $unit->code }}
                            </span>
                            <h3 class="font-bold text-slate-900 text-sm mt-1 truncate">
                                {{ $unit->name }}
                            </h3>
                        </div>
                        <div>
                            @if($unit->status === 'active')
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

                    <div class="flex items-center justify-between text-xs text-slate-600 pt-2 border-t border-slate-100">
                        <span class="text-slate-400">Total Barang:</span>
                        <span class="font-semibold text-slate-800">{{ $unit->barangs_count }} Item</span>
                    </div>

                    @if($unit->description)
                        <p class="text-xs text-slate-500 bg-slate-50 p-2 rounded-lg">
                            {{ $unit->description }}
                        </p>
                    @endif

                    @if(auth()->user()->hasPermission('units_manage'))
                        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                            <a href="{{ route('units.edit', $unit) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-slate-700 bg-slate-50 hover:bg-slate-100 border border-slate-300 rounded-lg transition-colors">
                                <svg class="h-3.5 w-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                <span>Edit</span>
                            </a>
                            <x-modal-hapus name="{{ $unit->name }}" action="{{ route('units.destroy', $unit) }}" />
                        </div>
                    @endif
                </div>
            @empty
                <div class="bg-white rounded-xl border border-slate-200 p-8 text-center text-slate-400 text-xs">
                    Belum ada data satuan barang.
                </div>
            @endforelse
        </div>

        @if($units->hasPages())
            <div class="px-4 sm:px-6 py-3 sm:py-4 bg-white rounded-xl border border-slate-300">
                {{ $units->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
