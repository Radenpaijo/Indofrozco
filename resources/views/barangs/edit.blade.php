<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4 min-w-0">
            <x-back-button />
            <div class="min-w-0">
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    {{ __('Edit Barang') }}
                </h2>
                <p class="text-xs text-slate-500 mt-1">Perbarui data dasar barang {{ $barang->nama_barang }}.</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
            <form method="POST" action="{{ route('barangs.update', $barang->id) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Row 1: Kode Barang & Nama Barang -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="kode_barang" :value="__('Kode Barang *')" />
                        <x-text-input id="kode_barang" class="block mt-1 w-full font-mono bg-slate-50" type="text" name="kode_barang" :value="old('kode_barang', $barang->kode_barang)" required autofocus />
                        <x-input-error :messages="$errors->get('kode_barang')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="nama_barang" :value="__('Nama Barang *')" />
                        <x-text-input id="nama_barang" class="block mt-1 w-full" type="text" name="nama_barang" :value="old('nama_barang', $barang->nama_barang)" required />
                        <x-input-error :messages="$errors->get('nama_barang')" class="mt-2" />
                    </div>
                </div>

                <!-- Row 2: Kelompok Barang & Satuan -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="category_id" :value="__('Kelompok Barang *')" />
                        <select id="category_id" name="category_id" required class="block mt-1 w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5">
                            <option value="">-- Pilih Kelompok Barang --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $barang->category_id) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="unit_id" :value="__('Satuan *')" />
                        <select id="unit_id" name="unit_id" required class="block mt-1 w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5">
                            <option value="">-- Pilih Satuan --</option>
                            @foreach($units as $u)
                                <option value="{{ $u->id }}" {{ old('unit_id', $barang->unit_id) == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }} ({{ $u->code }})
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="satuan" id="satuan_hidden" value="{{ old('satuan', $barang->satuan) }}">
                        <x-input-error :messages="$errors->get('unit_id')" class="mt-2" />
                        <x-input-error :messages="$errors->get('satuan')" class="mt-2" />
                    </div>
                </div>

                <!-- Row 3: Batas Stok Minimum -->
                <div>
                    <x-input-label for="stok_minimum" :value="__('Batas Stok Minimum')" />
                    <x-text-input id="stok_minimum" class="block mt-1 w-full" type="number" name="stok_minimum" :value="old('stok_minimum', $barang->stok_minimum)" min="0" />
                    <x-input-error :messages="$errors->get('stok_minimum')" class="mt-2" />
                    <p class="text-[11px] text-slate-400 mt-1">Sistem memberikan peringatan ketika stok mencapai atau berada di bawah batas minimum.</p>
                </div>

                <!-- Row 4: Deskripsi / Catatan Barang -->
                <div>
                    <x-input-label for="description" :value="__('Deskripsi / Catatan Barang')" />
                    <textarea id="description" name="description" rows="3" class="block mt-1 w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ old('description', $barang->description) }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>

                <!-- Status Active Checkbox -->
                <div class="block">
                    <label for="is_active" class="inline-flex items-center cursor-pointer">
                        <input id="is_active" type="checkbox" class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500 h-4 w-4" name="is_active" value="1" {{ old('is_active', $barang->is_active) ? 'checked' : '' }}>
                        <span class="ms-2 text-xs font-semibold text-slate-700">{{ __('Status Katalog Aktif') }}</span>
                    </label>
                </div>

                <!-- Form Action Buttons -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-slate-100">
                    <a href="{{ route('barangs.index') }}" class="inline-flex items-center px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition-all">
                        Batal
                    </a>
                    <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-md transition-all">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('unit_id').addEventListener('change', function() {
            var selectedText = this.options[this.selectedIndex].text;
            if(selectedText && selectedText !== '-- Pilih Satuan --') {
                var codeMatch = selectedText.match(/\(([^)]+)\)/);
                if (codeMatch) {
                    document.getElementById('satuan_hidden').value = codeMatch[1];
                } else {
                    document.getElementById('satuan_hidden').value = selectedText;
                }
            }
        });
    </script>
</x-app-layout>
