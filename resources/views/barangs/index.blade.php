<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Katalog Master Barang') }}
            </h2>
            @if(auth()->user()->hasRole(['admin', 'gudang']))
                <a href="{{ route('barangs.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow transition-colors">
                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Barang
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Filter & Search Panel -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <form method="GET" action="{{ route('barangs.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="search" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Pencarian</label>
                        <input type="text" name="search" id="search" placeholder="Cari nama / kode / rak..." value="{{ request('search') }}" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>
                    <div>
                        <label for="lokasi" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Lokasi Gudang</label>
                        <select name="lokasi" id="lokasi" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">Semua Lokasi</option>
                            <option value="Gudang Kering" {{ request('lokasi') === 'Gudang Kering' ? 'selected' : '' }}>Gudang Kering</option>
                            <option value="Cold Storage" {{ request('lokasi') === 'Cold Storage' ? 'selected' : '' }}>Cold Storage</option>
                        </select>
                    </div>
                    <div>
                        <label for="status" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Status Stok</label>
                        <select name="status" id="status" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">Semua Status</option>
                            <option value="low_stock" {{ request('status') === 'low_stock' ? 'selected' : '' }}>Stok Menipis (Low Stock)</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
                        </select>
                    </div>
                    <div class="flex items-end space-x-2">
                        <button type="submit" class="flex-1 inline-flex justify-center items-center px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white text-sm font-semibold rounded-lg transition-colors">
                            Filter
                        </button>
                        <a href="{{ route('barangs.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg transition-colors">
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
                                <th class="px-6 py-4">Kode Barang</th>
                                <th class="px-6 py-4">Nama Barang</th>
                                <th class="px-6 py-4">Satuan</th>
                                <th class="px-6 py-4">Stok Saat Ini</th>
                                <th class="px-6 py-4">Batas Stok Minimum</th>
                                <th class="px-6 py-4">Gudang & Posisi Rak</th>
                                <th class="px-6 py-4">Batas Umur (FIFO)</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse($barangs as $barang)
                                <tr class="hover:bg-gray-50/50 transition-colors {{ !$barang->is_active ? 'opacity-60 bg-gray-50/20' : '' }}">
                                    <td class="px-6 py-4 font-mono font-semibold text-gray-600">
                                        {{ $barang->kode_barang }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-2">
                                            <span class="font-medium text-gray-900">{{ $barang->nama_barang }}</span>
                                            @if(!$barang->is_active)
                                                <span class="px-1.5 py-0.5 text-[10px] font-bold bg-gray-100 text-gray-500 rounded">NON-AKTIF</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-500">
                                        {{ $barang->satuan }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($barang->is_low_stock)
                                            <span class="inline-flex items-center font-bold text-rose-600">
                                                {{ $barang->stok }}
                                                <span class="ml-1.5 px-1.5 py-0.5 text-[10px] bg-rose-50 text-rose-700 rounded-full font-bold">LOW</span>
                                            </span>
                                        @else
                                            <span class="font-semibold text-gray-900">{{ $barang->stok }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-gray-500">
                                        {{ $barang->stok_minimum }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col space-y-1.5">
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold w-fit {{ $barang->lokasi_gudang === 'Cold Storage' ? 'bg-sky-100 text-sky-800 border border-sky-200' : 'bg-amber-100 text-amber-800 border border-amber-200' }}">
                                                {{ $barang->lokasi_gudang }}
                                            </span>
                                            @if($barang->kode_rak)
                                                <span class="px-2 py-0.5 rounded-md text-xs font-mono font-bold text-slate-700 bg-slate-100 border border-slate-200 w-fit flex items-center">
                                                    <svg class="h-3 w-3 mr-1 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                                    </svg>
                                                    Rak: {{ $barang->kode_rak }}
                                                </span>
                                            @else
                                                <span class="text-[11px] text-gray-400 italic">Belum ada rak</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        {{ $barang->batas_umur_hari > 0 ? "{$barang->batas_umur_hari} Hari" : 'Tanpa Batas' }}
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-3 whitespace-nowrap">
                                        <a href="{{ route('barangs.show', $barang->id) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold text-xs transition-colors">Detail & Batches</a>
                                        
                                        @if(auth()->user()->hasRole(['admin', 'gudang']))
                                            <a href="{{ route('barangs.edit', $barang->id) }}" class="text-amber-600 hover:text-amber-900 font-semibold text-xs transition-colors">Edit</a>
                                            <form method="POST" action="{{ route('barangs.destroy', $barang->id) }}" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus barang ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-rose-600 hover:text-rose-900 font-semibold text-xs transition-colors">Hapus</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                        Barang tidak ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($barangs->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                        {{ $barangs->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
