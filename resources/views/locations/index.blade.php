<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-4 min-w-0">
                <x-back-button />
                <div class="min-w-0">
                    <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                        {{ __('Lokasi / Rak Gudang') }}
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">Kelola posisi rak dan pemantauan kapasitas simpan.</p>
                </div>
            </div>
            @if(auth()->user()->hasRole(['admin', 'gudang']))
                <a href="{{ route('locations.create') }}"
                    class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-md transition-all shrink-0">
                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Rak
                </a>
            @endif
        </div>
    </x-slot>

    <div class="space-y-6">
        {{-- Table --}}
        <x-table>
            <x-table.header>
                <x-table.row>
                    <x-table.head class="w-12 text-center">No</x-table.head>
                    <x-table.head>Kode Lokasi</x-table.head>
                    <x-table.head>Nama Lokasi</x-table.head>
                    <x-table.head>Gudang</x-table.head>
                    <x-table.head class="text-center">Kapasitas Maksimal</x-table.head>
                    <x-table.head class="text-center">Kapasitas Terisi</x-table.head>
                    <x-table.head class="text-center">Sisa Kapasitas</x-table.head>
                    <x-table.head class="text-right">Aksi</x-table.head>
                </x-table.row>
            </x-table.header>
            <x-table.body>
                @forelse($locations as $index => $loc)
                    @php
                        $maxCap = $loc->capacity ?: 0;
                        $usedCap = $loc->used_capacity ?: 0;
                        $sisaCap = max(0, $maxCap - $usedCap);
                    @endphp
                    <x-table.row>
                        <x-table.cell class="text-center text-xs text-slate-500 font-medium">
                            {{ $locations->firstItem() + $index }}
                        </x-table.cell>
                        <x-table.cell class="font-mono font-bold text-indigo-600 text-xs">
                            {{ $loc->code }}
                        </x-table.cell>
                        <x-table.cell class="font-semibold text-slate-900">
                            {{ $loc->name }}
                        </x-table.cell>
                        <x-table.cell class="text-slate-600 font-medium">
                            {{ $loc->warehouse ? $loc->warehouse->name : '-' }}
                        </x-table.cell>
                        <x-table.cell class="text-center font-semibold text-slate-800">
                            {{ $maxCap > 0 ? number_format($maxCap) : 'Tanpa Batas' }}
                        </x-table.cell>
                        <x-table.cell class="text-center font-bold text-indigo-600">
                            {{ number_format($usedCap) }}
                        </x-table.cell>
                        <x-table.cell class="text-center font-bold {{ $sisaCap == 0 && $maxCap > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                            {{ $maxCap > 0 ? number_format($sisaCap) : '-' }}
                        </x-table.cell>
                        <x-table.cell class="text-right">
                            <div class="flex items-center justify-end space-x-2">
                                @if(auth()->user()->hasRole(['admin', 'gudang']))
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

        @if($locations->hasPages())
            <div class="px-6 py-4 bg-white rounded-lg border border-slate-300">
                {{ $locations->links() }}
            </div>
        @endif
    </div>
</x-app-layout>