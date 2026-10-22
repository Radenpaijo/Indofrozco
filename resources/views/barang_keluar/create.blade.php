<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4 min-w-0">
            <x-back-button />
            <div class="min-w-0">
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    {{ __('Rilis Pengeluaran Manual (Barang Keluar)') }}
                </h2>
                <p class="text-xs text-slate-500 mt-1">Pengeluaran stok barang secara manual dari gudang.</p>
            </div>
        </div>
    </x-slot>

    <div class="w-full">

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <form method="POST" action="{{ route('barang-keluar.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="barang_id" :value="__('Pilih Barang')" />
                        <select id="barang_id" name="barang_id" required class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">-- Pilih Barang --</option>
                            @foreach($barangs as $barang)
                                <option value="{{ $barang->id }}" {{ old('barang_id') == $barang->id ? 'selected' : '' }}>
                                    {{ $barang->kode_barang }} - {{ $barang->nama_barang }} ({{ $barang->lokasi_gudang }}{{ $barang->kode_rak ? ' • ' . $barang->kode_rak : '' }} | Stok: {{ $barang->stok }} {{ $barang->satuan }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('barang_id')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="jumlah" :value="__('Jumlah Pengeluaran')" />
                        <x-text-input id="jumlah" class="block mt-1 w-full" type="number" name="jumlah" :value="old('jumlah')" required min="1" placeholder="Masukkan jumlah rilis" />
                        <x-input-error :messages="$errors->get('jumlah')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="tanggal" :value="__('Tanggal Pengeluaran')" />
                        <x-text-input id="tanggal" class="block mt-1 w-full" type="date" name="tanggal" :value="old('tanggal', date('Y-m-d'))" required />
                        <x-input-error :messages="$errors->get('tanggal')" class="mt-2" />
                    </div>

                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-100">
                        <a href="{{ route('barang-keluar.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm font-semibold rounded-lg transition-colors">
                            Batal
                        </a>
                        <button type="submit" class="inline-flex items-center px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow transition-colors">
                            Rilis Barang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
