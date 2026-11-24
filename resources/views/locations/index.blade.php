<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                <x-back-button :href="route('dashboard')" />
                <div class="min-w-0">
                    <h2 class="font-bold text-xl sm:text-2xl text-slate-800 leading-tight truncate">
                        {{ __('Lokasi / Rak Gudang') }}
                    </h2>
                </div>
            </div>
            @if(auth()->user()->hasPermission('locations_manage'))
                <a href="{{ route('locations.create') }}" class="w-full sm:w-auto shrink-0 self-start sm:self-center">
                    <x-button variant="indigo" class="w-full sm:w-auto justify-center shadow-sm">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Rak
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
                        <x-table.head class="w-12 text-center">No</x-table.head>
                        <x-table.head class="whitespace-nowrap">Kode</x-table.head>
                        <x-table.head>Nama Lokasi</x-table.head>
                        <x-table.head>Gudang</x-table.head>
                        <x-table.head class="text-center">Kapasitas Maksimal</x-table.head>
                        <x-table.head class="text-center">Terisi</x-table.head>
                        <x-table.head class="text-center">Sisa</x-table.head>
                        <x-table.head class="text-right">Aksi</x-table.head>
                    </x-table.row>
                </x-table.header>
                <x-table.body>
                    @forelse($locations as $index => $loc)
                        @php
                            $maxCap = $loc->capacity ?: 0;
                            $usedCap = $loc->used_capacity ?: 0;
                            $sisaCap = max(0, $maxCap - $usedCap);
                            $satuan = $loc->satuan_label;
                        @endphp
                        <x-table.row>
                            <x-table.cell class="text-center text-xs text-slate-500 font-medium">
                                {{ $locations->firstItem() + $index }}
                            </x-table.cell>
                            <x-table.cell class="font-mono font-bold text-indigo-600 text-xs whitespace-nowrap">
                                {{ $loc->code }}
                            </x-table.cell>
                            <x-table.cell>
                                <div class="font-semibold text-slate-900 text-xs sm:text-sm">{{ $loc->name }}</div>
                            </x-table.cell>
                            <x-table.cell class="text-slate-600 font-medium text-xs">
                                {{ $loc->warehouse ? $loc->warehouse->name : '-' }}
                            </x-table.cell>
                            <x-table.cell class="text-center font-semibold text-slate-800 text-xs whitespace-nowrap">
                                @if($maxCap > 0)
                                    <span>{{ number_format($maxCap) }}</span>
                                    <span class="text-[11px] font-bold text-slate-500 ml-0.5">{{ $satuan }}</span>
                                @else
                                    <span class="text-slate-400">Tanpa Batas</span>
                                @endif
                            </x-table.cell>
                            <x-table.cell class="text-center font-bold text-indigo-600 text-xs whitespace-nowrap">
                                <span>{{ number_format($usedCap) }}</span>
                                <span class="text-[11px] font-bold text-slate-500 ml-0.5">{{ $satuan }}</span>
                            </x-table.cell>
                            <x-table.cell class="text-center font-bold text-xs whitespace-nowrap {{ $sisaCap == 0 && $maxCap > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                @if($maxCap > 0)
                                    <span>{{ number_format($sisaCap) }}</span>
                                    <span class="text-[11px] font-bold text-slate-500 ml-0.5">{{ $satuan }}</span>
                                @else
                                    <span>-</span>
                                @endif
                            </x-table.cell>
                            <x-table.cell class="text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    @if(auth()->user()->hasPermission('locations_manage'))
                                        <a href="{{ route('locations.edit', $loc) }}"
                                            class="p-2 text-slate-500 hover:text-indigo-600 hover:bg-slate-100 rounded-md transition-colors border border-slate-300"
                                            title="Edit">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <x-modal-hapus name="{{ $loc->name }}"
                                            action="{{ route('locations.destroy', $loc) }}" />
                                    @endif
                                </div>
                            </x-table.cell>
                        </x-table.row>
                    @empty
                        <x-table.row>
                            <x-table.cell colspan="8" class="py-8 text-center text-slate-400">
                                Belum ada data lokasi rak.
                            </x-table.cell>
                        </x-table.row>
                    @endforelse
                </x-table.body>
            </x-table>
        </div>

        <!-- Mobile Card View -->
        <div class="block sm:hidden space-y-3">
            @forelse($locations as $loc)
                @php
                    $maxCap = $loc->capacity ?: 0;
                    $usedCap = $loc->used_capacity ?: 0;
                    $sisaCap = max(0, $maxCap - $usedCap);
                    $satuan = $loc->satuan_label;
                    $percentage = $maxCap > 0 ? min(100, round(($usedCap / $maxCap) * 100)) : 0;
                @endphp
                <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-2xs space-y-3">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <span class="inline-block px-2 py-0.5 font-mono text-[11px] font-bold text-indigo-700 bg-indigo-50 border border-indigo-200 rounded">
                                {{ $loc->code }}
                            </span>
                            <h3 class="font-bold text-slate-900 text-sm mt-1 truncate">
                                {{ $loc->name }}
                            </h3>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-700 border border-slate-200 shrink-0">
                            {{ $loc->warehouse ? $loc->warehouse->name : '-' }}
                        </span>
                    </div>

                    <!-- Capacity Info & Progress Bar -->
                    <div class="p-3 bg-slate-50 rounded-xl space-y-2 border border-slate-100">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500">Kapasitas Terisi:</span>
                            <span class="font-bold text-slate-800">
                                {{ number_format($usedCap) }} {{ $satuan }} / {{ $maxCap > 0 ? number_format($maxCap) . ' ' . $satuan : '∞' }}
                            </span>
                        </div>

                        @if($maxCap > 0)
                            <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
                                <div class="h-2 rounded-full transition-all duration-300 {{ $percentage >= 95 ? 'bg-rose-500' : ($percentage >= 75 ? 'bg-amber-500' : 'bg-emerald-500') }}" style="width: {{ $percentage }}%"></div>
                            </div>
                            <div class="flex items-center justify-between text-[11px] font-medium">
                                <span class="text-slate-400">{{ $percentage }}% Terpakai</span>
                                <span class="{{ $sisaCap == 0 ? 'text-rose-600 font-bold' : 'text-emerald-700 font-bold' }}">Sisa: {{ number_format($sisaCap) }} {{ $satuan }}</span>
                            </div>
                        @else
                            <div class="text-[11px] text-slate-400 italic">Tanpa batasan kuota kapasitas rak</div>
                        @endif
                    </div>

                    @if(auth()->user()->hasPermission('locations_manage'))
                        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                            <a href="{{ route('locations.edit', $loc) }}"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-slate-700 bg-slate-50 hover:bg-slate-100 border border-slate-300 rounded-lg transition-colors">
                                <svg class="h-3.5 w-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                <span>Edit</span>
                            </a>
                            <x-modal-hapus name="{{ $loc->name }}" action="{{ route('locations.destroy', $loc) }}" />
                        </div>
                    @endif
                </div>
            @empty
                <div class="bg-white rounded-xl border border-slate-200 p-8 text-center text-slate-400 text-xs">
                    Belum ada data lokasi rak.
                </div>
            @endforelse
        </div>

        @if($locations->hasPages())
            <div class="px-4 sm:px-6 py-3 sm:py-4 bg-white rounded-xl border border-slate-300">
                {{ $locations->links() }}
            </div>
        @endif
    </div>
</x-app-layout>