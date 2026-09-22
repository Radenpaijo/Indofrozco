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
            <a
                href="{{ route('warehouses.create') }}"
                class="inline-flex items-center px-4 py-2.5
                       bg-indigo-600 hover:bg-indigo-700
                       text-white text-xs font-semibold
                       rounded-xl shadow-md
                       transition-all shrink-0"
            >
                <svg
                    class="h-4 w-4 mr-2"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 4v16m8-8H4"
                    />
                </svg>

                Tambah Gudang
            </a>

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

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">

            <div class="overflow-x-auto">

                <table class="w-full text-left border-collapse">

                    {{-- Table Header --}}
                    <thead>

                        <tr
                            class="bg-slate-50/70 border-b border-slate-200
                                   text-[11px] font-bold uppercase
                                   tracking-wider text-slate-500"
                        >

                            <th class="py-3.5 px-6">
                                Kode Gudang
                            </th>

                            <th class="py-3.5 px-6">
                                Nama Gudang
                            </th>

                            <th class="py-3.5 px-6">
                                Tipe Gudang
                            </th>

                            <th class="py-3.5 px-6 text-center">
                                Total Lokasi/Rak
                            </th>

                            <th class="py-3.5 px-6 text-center">
                                Status
                            </th>

                            <th class="py-3.5 px-6 text-right">
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    {{-- Table Body --}}
                    <tbody class="divide-y divide-slate-100 text-sm">

                        @forelse($warehouses as $wh)

                            <tr class="hover:bg-slate-50/50 transition-colors">

                                {{-- Kode Gudang --}}
                                <td class="py-4 px-6 font-mono font-bold text-indigo-600 text-xs">
                                    {{ $wh->code }}
                                </td>


                                {{-- Nama Gudang --}}
                                <td class="py-4 px-6 font-semibold text-slate-900">
                                    {{ $wh->name }}
                                </td>


                                {{-- Tipe Gudang --}}
                                <td class="py-4 px-6">

                                    <span
                                        class="inline-flex items-center px-2.5 py-1
                                               text-xs font-semibold rounded-lg
                                               bg-indigo-50 text-indigo-700
                                               border border-indigo-200"
                                    >
                                        {{ $wh->warehouseType ? $wh->warehouseType->name : '-' }}
                                    </span>

                                </td>


                                {{-- Total Lokasi --}}
                                <td class="py-4 px-6 text-center">

                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5
                                               rounded-full text-xs font-semibold
                                               bg-slate-100 text-slate-800
                                               border border-slate-200"
                                    >
                                        {{ $wh->locations_count }} Rak
                                    </span>

                                </td>


                                {{-- Status --}}
                                <td class="py-4 px-6 text-center">

                                    @if($wh->status === 'active')

                                        <span
                                            class="inline-flex items-center px-2.5 py-1
                                                   rounded-lg text-xs font-bold
                                                   bg-emerald-500/10
                                                   text-emerald-600
                                                   border border-emerald-500/20"
                                        >
                                            Aktif
                                        </span>

                                    @else

                                        <span
                                            class="inline-flex items-center px-2.5 py-1
                                                   rounded-lg text-xs font-bold
                                                   bg-slate-100 text-slate-500
                                                   border border-slate-200"
                                        >
                                            Nonaktif
                                        </span>

                                    @endif

                                </td>


                                {{-- Aksi --}}
                                <td class="py-4 px-6 text-right">

                                    <div class="flex items-center justify-end space-x-2">

                                        {{-- Edit --}}
                                        <a
                                            href="{{ route('warehouses.edit', $wh) }}"
                                            class="p-2 text-slate-500
                                                   hover:text-indigo-600
                                                   hover:bg-indigo-50
                                                   rounded-lg transition-colors"
                                            title="Edit"
                                        >

                                            <svg
                                                class="h-4 w-4"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                                />
                                            </svg>

                                        </a>


                                        {{-- Hapus --}}
                                        <x-modal-hapus
                                            name="{{ $wh->name }}"
                                            action="{{ route('warehouses.destroy', $wh) }}"
                                        />

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="6"
                                    class="py-8 text-center text-slate-400"
                                >
                                    Belum ada data gudang.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- Pagination --}}
            @if($warehouses->hasPages())

                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $warehouses->links() }}
                </div>

            @endif

        </div>

    </div>

</x-app-layout>
