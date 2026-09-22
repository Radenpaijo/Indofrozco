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
                <a href="{{ route('suppliers.create') }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-md transition-all shrink-0">
                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Supplier
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Filter & Search Panel -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <form method="GET" action="{{ route('suppliers.index') }}" class="flex gap-4">
                    <div class="flex-1">
                        <label for="search" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Pencarian</label>
                        <input type="text" name="search" id="search" placeholder="Cari nama supplier, kontak, alamat..." value="{{ request('search') }}" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>
                    <div class="flex items-end space-x-2">
                        <button type="submit" class="inline-flex justify-center items-center px-6 py-2 bg-gray-800 hover:bg-gray-700 text-white text-sm font-semibold rounded-lg transition-colors">
                            Filter
                        </button>
                        <a href="{{ route('suppliers.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg transition-colors">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <!-- Table Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead>
                            <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <th class="px-6 py-4">Nama Supplier</th>
                                <th class="px-6 py-4">Kontak / Telepon</th>
                                <th class="px-6 py-4">Alamat</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse($suppliers as $supplier)
                                <tr class="hover:bg-gray-50/50 transition-colors {{ !$supplier->is_active ? 'opacity-60 bg-gray-50/20' : '' }}">
                                    <td class="px-6 py-4 font-semibold text-gray-900">
                                        {{ $supplier->nama_supplier }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        {{ $supplier->kontak ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-500">
                                        {{ $supplier->alamat ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold {{ $supplier->is_active ? 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                                            {{ $supplier->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap">
                                        @if(auth()->user()->hasRole(['admin', 'purchasing']))
                                            <div class="flex items-center justify-end space-x-2">
                                                <a href="{{ route('suppliers.edit', $supplier->id) }}" class="p-2 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Edit">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>
                                                <x-modal-hapus name="{{ $supplier->nama_supplier }}" action="{{ route('suppliers.destroy', $supplier->id) }}" />
                                            </div>
                                        @else
                                            <span class="text-gray-400 text-xs">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                        Supplier tidak ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($suppliers->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                        {{ $suppliers->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
