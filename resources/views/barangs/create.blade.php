<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4 min-w-0">
            <x-back-button />
            <div class="min-w-0">
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    {{ __('Tambah Barang') }}
                </h2>
                <p class="text-xs text-slate-500 mt-1">Input data dasar barang yang digunakan dalam operasional gudang.</p>
            </div>
        </div>
    </x-slot>

    <div class="w-full">
        <x-card class="p-6 sm:p-8">
            <form method="POST" action="{{ route('barangs.store') }}" class="space-y-6">
                @csrf

                <!-- Row 1: Kode Barang & Nama Barang -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-label for="kode_barang">Kode Barang *</x-label>
                        <x-input id="kode_barang" class="mt-1 font-mono bg-slate-100 cursor-not-allowed text-slate-600" type="text" name="kode_barang" :value="old('kode_barang', $nextKodeBarang)" required readonly placeholder="BRG-001" />
                        <x-input-error :messages="$errors->get('kode_barang')" class="mt-2" />
                        <p class="text-[11px] text-slate-400 mt-1">Kode dibuat otomatis oleh sistem.</p>
                    </div>

                    <div>
                        <x-label for="nama_barang">Nama Barang *</x-label>
                        <x-input id="nama_barang" class="mt-1" type="text" name="nama_barang" :value="old('nama_barang')" required placeholder="Contoh: Udang Windu Segar" />
                        <x-input-error :messages="$errors->get('nama_barang')" class="mt-2" />
                    </div>
                </div>

                <!-- Row 2: Kelompok Barang, Satuan, Batas Stok Minimum -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <div class="flex items-center justify-between">
                            <x-label for="category_id">Kelompok Barang *</x-label>
                            <a href="{{ route('categories.create') }}" target="_blank" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 hover:underline flex items-center">
                                + Tambah Kelompok
                            </a>
                        </div>
                        <x-select id="category_id" name="category_id" required class="mt-1">
                            <option value="">-- Pilih Kelompok Barang --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </x-select>
                        @if($categories->isEmpty())
                            <p class="text-xs text-amber-600 mt-1 font-semibold">⚠️ Belum ada Kelompok Barang. <a href="{{ route('categories.create') }}" target="_blank" class="underline text-indigo-600">Klik di sini untuk menambah Kelompok Baru</a>.</p>
                        @endif
                        <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                    </div>

                    <div>
                        <div class="flex items-center justify-between">
                            <x-label for="unit_id">Satuan *</x-label>
                            <a href="{{ route('units.create') }}" target="_blank" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 hover:underline flex items-center">
                                + Tambah Satuan
                            </a>
                        </div>
                        <x-select id="unit_id" name="unit_id" required class="mt-1">
                            <option value="">-- Pilih Satuan --</option>
                            @foreach($units as $u)
                                <option value="{{ $u->id }}" {{ old('unit_id') == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }} ({{ $u->code }})
                                </option>
                            @endforeach
                        </x-select>
                        @if($units->isEmpty())
                            <p class="text-xs text-amber-600 mt-1 font-semibold">⚠️ Belum ada Satuan. <a href="{{ route('units.create') }}" target="_blank" class="underline text-indigo-600">Klik di sini untuk menambah Satuan Baru</a>.</p>
                        @endif
                        <input type="hidden" name="satuan" id="satuan_hidden" value="{{ old('satuan', 'Kg') }}">
                        <x-input-error :messages="$errors->get('unit_id')" class="mt-2" />
                        <x-input-error :messages="$errors->get('satuan')" class="mt-2" />
                    </div>

                    <div>
                        <x-label for="stok_minimum">Batas Stok Minimum</x-label>
                        <x-input id="stok_minimum" class="mt-1" type="number" name="stok_minimum" :value="old('stok_minimum', 10)" min="0" placeholder="10" />
                        <x-input-error :messages="$errors->get('stok_minimum')" class="mt-2" />
                        <p class="text-[11px] text-slate-400 mt-1">Peringatan saat stok di bawah batas.</p>
                    </div>
                </div>

                <!-- Row 3: Deskripsi / Catatan Barang -->
                <div>
                    <x-label for="description">Deskripsi / Catatan Barang</x-label>
                    <textarea id="description" name="description" rows="3" class="block mt-1 w-full rounded-md border border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2" placeholder="Keterangan opsional mengenai barang ini...">{{ old('description') }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>

                <!-- Form Action Buttons -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-slate-200">
                    <a href="{{ route('barangs.index') }}">
                        <x-button type="button" variant="outline">Batal</x-button>
                    </a>
                    <x-button type="submit" variant="indigo">Simpan Barang</x-button>
                </div>
            </form>
        </x-card>
    </div>

    <script>
        document.getElementById('unit_id').addEventListener('change', function() {
            var selectedText = this.options[this.selectedIndex].text;
            if(selectedText && selectedText !== '-- Pilih Satuan --') {
                var unitName = selectedText.split(' (')[0].trim();
                document.getElementById('satuan_hidden').value = unitName;
            }
        });
    </script>
</x-app-layout>
