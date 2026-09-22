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
                <a href="{{ route('barangs.create') }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-md transition-all shrink-0">
                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Barang
                </a>
            @endif
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Filter Panel -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <form method="GET" action="{{ route('barangs.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label for="search" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Pencarian</label>
                    <input type="text" name="search" id="search" placeholder="Cari kode / nama barang..." value="{{ request('search') }}" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                </div>
                <div>
                    <label for="category_id" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Kelompok Barang</label>
                    <select name="category_id" id="category_id" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                        <option value="">Semua Kelompok</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="unit_id" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Satuan</label>
                    <select name="unit_id" id="unit_id" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                        <option value="">Semua Satuan</option>
                        @foreach($units as $u)
                            <option value="{{ $u->id }}" {{ request('unit_id') == $u->id ? 'selected' : '' }}>
                                {{ $u->name }} ({{ $u->code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end space-x-2">
                    <button type="submit" class="flex-1 inline-flex justify-center items-center px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-xl transition-all shadow-sm">
                        Filter
                    </button>
                    @if(request()->anyFilled(['search', 'category_id', 'unit_id']))
                        <a href="{{ route('barangs.index') }}" class="inline-flex items-center px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition-all">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/70 border-b border-slate-200 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            <th class="py-3.5 px-6 w-12 text-center">No</th>
                            <th class="py-3.5 px-6">Kode Barang</th>
                            <th class="py-3.5 px-6">Nama Barang</th>
                            <th class="py-3.5 px-6">Kelompok</th>
                            <th class="py-3.5 px-6">Satuan</th>
                            <th class="py-3.5 px-6">Keterangan</th>
                            <th class="py-3.5 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse($barangs as $index => $barang)
                            <tr class="hover:bg-slate-50/50 transition-colors {{ !$barang->is_active ? 'opacity-60 bg-slate-50/20' : '' }}">
                                <td class="py-4 px-6 text-center text-xs text-slate-400 font-medium">
                                    {{ $barangs->firstItem() + $index }}
                                </td>
                                <td class="py-4 px-6 font-mono font-bold text-indigo-600 text-xs">
                                    {{ $barang->kode_barang }}
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center space-x-2">
                                        <span class="font-semibold text-slate-900">{{ $barang->nama_barang }}</span>
                                        @if(!$barang->is_active)
                                            <span class="px-1.5 py-0.5 text-[10px] font-bold bg-slate-100 text-slate-500 rounded border border-slate-200">NONAKTIF</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-slate-600">
                                    {{ $barang->category ? $barang->category->name : '-' }}
                                </td>
                                <td class="py-4 px-6 text-slate-600">
                                    {{ $barang->unit ? $barang->unit->name : $barang->satuan }}
                                </td>
                                <td class="py-4 px-6 text-slate-500 text-xs">
                                    {{ $barang->description ?: '-' }}
                                </td>
                                <td class="py-4 px-6 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('barangs.show', $barang->id) }}" class="px-2.5 py-1 text-xs font-semibold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors" title="Detail">
                                            Detail
                                        </a>
                                        @if(auth()->user()->hasRole(['admin', 'gudang']))
                                            <a href="{{ route('barangs.edit', $barang->id) }}" class="p-2 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Edit">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                            <x-modal-hapus name="{{ $barang->nama_barang }}" action="{{ route('barangs.destroy', $barang->id) }}" />
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400">
                                    Belum ada data barang.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($barangs->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $barangs->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
