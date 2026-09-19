<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Master Barang: ') }} {{ $barang->nama_barang }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <form method="POST" action="{{ route('barangs.update', $barang->id) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="kode_barang" :value="__('Kode Barang (Unique)')" />
                            <x-text-input id="kode_barang" class="block mt-1 w-full bg-gray-50" type="text" name="kode_barang" :value="old('kode_barang', $barang->kode_barang)" required />
                            <x-input-error :messages="$errors->get('kode_barang')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="nama_barang" :value="__('Nama Barang')" />
                            <x-text-input id="nama_barang" class="block mt-1 w-full" type="text" name="nama_barang" :value="old('nama_barang', $barang->nama_barang)" required />
                            <x-input-error :messages="$errors->get('nama_barang')" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <x-input-label for="satuan" :value="__('Satuan')" />
                            <x-text-input id="satuan" class="block mt-1 w-full" type="text" name="satuan" :value="old('satuan', $barang->satuan)" required />
                            <x-input-error :messages="$errors->get('satuan')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="lokasi_gudang" :value="__('Lokasi Gudang')" />
                            <select id="lokasi_gudang" name="lokasi_gudang" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="Gudang Kering" {{ old('lokasi_gudang', $barang->lokasi_gudang) === 'Gudang Kering' ? 'selected' : '' }}>Gudang Kering</option>
                                <option value="Cold Storage" {{ old('lokasi_gudang', $barang->lokasi_gudang) === 'Cold Storage' ? 'selected' : '' }}>Cold Storage</option>
                            </select>
                            <x-input-error :messages="$errors->get('lokasi_gudang')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="kode_rak" :value="__('Nomor / Kode Rak')" />
                            <x-text-input id="kode_rak" class="block mt-1 w-full" type="text" name="kode_rak" :value="old('kode_rak', $barang->kode_rak)" placeholder="Contoh: Rak CS-01, Rak A-02" />
                            <x-input-error :messages="$errors->get('kode_rak')" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <x-input-label for="stok" :value="__('Stok Saat Ini')" />
                            <x-text-input id="stok" class="block mt-1 w-full" type="number" name="stok" :value="old('stok', $barang->stok)" required />
                            <x-input-error :messages="$errors->get('stok')" class="mt-2" />
                            <p class="text-xs text-gray-400 mt-1">Stok aktual di gudang.</p>
                        </div>

                        <div>
                            <x-input-label for="stok_minimum" :value="__('Batas Stok Minimum')" />
                            <x-text-input id="stok_minimum" class="block mt-1 w-full" type="number" name="stok_minimum" :value="old('stok_minimum', $barang->stok_minimum)" required />
                            <x-input-error :messages="$errors->get('stok_minimum')" class="mt-2" />
                            <p class="text-xs text-gray-400 mt-1">Alert akan aktif jika stok <= batas ini.</p>
                        </div>

                        <div>
                            <x-input-label for="batas_umur_hari" :value="__('Batas Umur Simpan (Hari)')" />
                            <x-text-input id="batas_umur_hari" class="block mt-1 w-full" type="number" name="batas_umur_hari" :value="old('batas_umur_hari', $barang->batas_umur_hari)" required />
                            <x-input-error :messages="$errors->get('batas_umur_hari')" class="mt-2" />
                            <p class="text-xs text-gray-400 mt-1">Isi 0 untuk tanpa batas (FIFO umum).</p>
                        </div>
                    </div>

                    <div class="block">
                        <label for="is_active" class="inline-flex items-center">
                            <input id="is_active" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="is_active" value="1" {{ old('is_active', $barang->is_active) ? 'checked' : '' }}>
                            <span class="ms-2 text-sm text-gray-600">{{ __('Katalog Aktif') }}</span>
                        </label>
                    </div>

                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-100">
                        <a href="{{ route('barangs.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm font-semibold rounded-lg transition-colors">
                            Batal
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow transition-colors">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
