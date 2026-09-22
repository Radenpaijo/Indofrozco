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
                <a href="{{ route('locations.create') }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-md transition-all shrink-0">
                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Rak
                </a>
            @endif
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Tab Navigation: Gudang & Lokasi -->
        <div class="flex items-center space-x-2 border-b border-slate-200/80 pb-3">
            <a href="{{ route('warehouses.index') }}" class="px-4 py-2 text-xs font-bold rounded-xl transition-all {{ request()->routeIs('warehouses.*') ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                Gudang
            </a>
            <a href="{{ route('warehouse-types.index') }}" class="px-4 py-2 text-xs font-bold rounded-xl transition-all {{ request()->routeIs('warehouse-types.*') ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                Tipe Gudang
            </a>
            <a href="{{ route('locations.index') }}" class="px-4 py-2 text-xs font-bold rounded-xl transition-all {{ request()->routeIs('locations.*') ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                Lokasi / Rak
            </a>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/70 border-b border-slate-200 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            <th class="py-3.5 px-6 w-12 text-center">No</th>
                            <th class="py-3.5 px-6">Kode Lokasi</th>
                            <th class="py-3.5 px-6">Nama Lokasi</th>
                            <th class="py-3.5 px-6">Gudang</th>
                            <th class="py-3.5 px-6 text-center">Kapasitas Maksimal</th>
                            <th class="py-3.5 px-6 text-center">Kapasitas Terisi</th>
                            <th class="py-3.5 px-6 text-center">Sisa Kapasitas</th>
                            <th class="py-3.5 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse($locations as $index => $loc)
                            @php
                                $maxCap = $loc->capacity ?: 0;
                                $usedCap = $loc->used_capacity ?: 0;
                                $sisaCap = max(0, $maxCap - $usedCap);
                            @endphp
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="py-4 px-6 text-center text-xs text-slate-400 font-medium">
                                    {{ $locations->firstItem() + $index }}
                                </td>
                                <td class="py-4 px-6 font-mono font-bold text-indigo-600 text-xs">
                                    {{ $loc->code }}
                                </td>
                                <td class="py-4 px-6 font-semibold text-slate-900">
                                    {{ $loc->name }}
                                </td>
                                <td class="py-4 px-6 text-slate-600 font-medium">
                                    {{ $loc->warehouse ? $loc->warehouse->name : '-' }}
                                </td>
                                <td class="py-4 px-6 text-center font-semibold text-slate-800">
                                    {{ $maxCap > 0 ? number_format($maxCap) : 'Tanpa Batas' }}
                                </td>
                                <td class="py-4 px-6 text-center font-bold text-indigo-600">
                                    {{ number_format($usedCap) }}
                                </td>
                                <td class="py-4 px-6 text-center font-bold {{ $sisaCap == 0 && $maxCap > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                    {{ $maxCap > 0 ? number_format($sisaCap) : '-' }}
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        @if(auth()->user()->hasRole(['admin', 'gudang']))
                                            <a href="{{ route('locations.edit', $loc) }}" class="p-2 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Edit">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                            <x-modal-hapus name="{{ $loc->name }}" action="{{ route('locations.destroy', $loc) }}" />
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-slate-400">
                                    Belum ada data lokasi rak.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($locations->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $locations->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>