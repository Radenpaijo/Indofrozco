<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3 sm:gap-4 min-w-0">
            <x-back-button :href="route('barangs.index')" />
            <div class="min-w-0">
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    {{ __('Tambah Barang') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="w-full">
        <x-card class="p-6 sm:p-8">
            <form method="POST" action="{{ route('barangs.store') }}" class="space-y-6">
                @csrf

                <div>
                    <x-label for="kode_barang">Kode Barang *</x-label>
                    <x-input id="kode_barang" class="mt-1 font-mono bg-slate-100 cursor-not-allowed text-slate-600" type="text" name="kode_barang" :value="old('kode_barang', $nextKodeBarang)" required readonly placeholder="BRG-001" />
                    <x-input-error :messages="$errors->get('kode_barang')" class="mt-2" />
                </div>

                <div>
                    <x-label for="nama_barang">Nama Barang *</x-label>
                    <x-input id="nama_barang" class="mt-1" type="text" name="nama_barang" :value="old('nama_barang')" required placeholder="Contoh: Udang Windu Segar" />
                    <x-input-error :messages="$errors->get('nama_barang')" class="mt-2" />
                </div>

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
                                {{ $u->name }}
                            </option>
                        @endforeach
                    </x-select>
                    <input type="hidden" name="satuan" id="satuan_hidden" value="{{ old('satuan') }}">
                    <x-input-error :messages="$errors->get('unit_id')" class="mt-2" />
                    <x-input-error :messages="$errors->get('satuan')" class="mt-2" />
                </div>

                <div>
                    <x-label for="stok_minimum">Batas Stok Minimum</x-label>
                    <x-input id="stok_minimum" class="mt-1" type="number" name="stok_minimum" :value="old('stok_minimum', 10)" min="0" placeholder="10" />
                    <div id="stok_minimum_alert" class="hidden mt-1.5 text-xs text-rose-600 font-medium"></div>
                    <x-input-error :messages="$errors->get('stok_minimum')" class="mt-2" />
                </div>

                <div>
                    <div class="flex items-center justify-between">
                        <x-label for="location_id">Lokasi Rak Penyimpanan</x-label>
                        <a href="{{ route('locations.create') }}" target="_blank" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 hover:underline flex items-center">
                            + Tambah Rak Baru
                        </a>
                    </div>
                    <x-select id="location_id" name="location_id" class="mt-1">
                        <option value="">-- Pilih Lokasi Rak --</option>
                        @foreach($locations as $loc)
                            @php
                                $maxCap = $loc->capacity;
                                $usedCap = $loc->used_capacity;
                                $sisaCap = $loc->sisa_kapasitas;
                                $satuanLoc = $loc->satuan_label;
                                $capLabel = ($maxCap !== null && (float)$maxCap > 0)
                                    ? ' • [Sisa: ' . number_format($sisaCap) . ' ' . $satuanLoc . ' / Maks: ' . number_format($maxCap) . ' ' . $satuanLoc . ']'
                                    : ' • [Kapasitas: Tanpa Batas]';
                            @endphp
                            <option value="{{ $loc->id }}"
                                {{ old('location_id') == $loc->id ? 'selected' : '' }}
                                data-code="{{ $loc->code }}"
                                data-name="{{ $loc->name }}"
                                data-warehouse="{{ $loc->warehouse->name ?? 'Gudang' }}"
                                data-capacity="{{ $maxCap !== null ? (float)$maxCap : -1 }}"
                                data-used="{{ (float)$usedCap }}"
                                data-sisa="{{ $sisaCap !== null ? (float)$sisaCap : -1 }}"
                                data-satuan="{{ $satuanLoc }}">
                                {{ $loc->code }} - {{ $loc->name }} ({{ $loc->warehouse->name ?? 'Gudang' }}){{ $capLabel }}
                            </option>
                        @endforeach
                    </x-select>

                    <div id="rack-info-card" class="hidden mt-1.5 text-xs font-medium"></div>
                    <x-input-error :messages="$errors->get('location_id')" class="mt-2" />
                </div>

                <div>
                    <x-label for="description">Deskripsi / Catatan Barang</x-label>
                    <textarea id="description" name="description" rows="4" class="block mt-1 w-full rounded-md border border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-3 py-2" placeholder="Keterangan opsional mengenai barang ini...">{{ old('description') }}</textarea>
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

        function updateRackCapacityNotice() {
            const locSelect = document.getElementById('location_id');
            const stokInput = document.getElementById('stok_minimum');
            const infoCard = document.getElementById('rack-info-card');
            const stokAlert = document.getElementById('stok_minimum_alert');

            if (!locSelect || !infoCard) return;

            const selectedOpt = locSelect.options[locSelect.selectedIndex];
            const reset = () => {
                infoCard.classList.add('hidden');
                infoCard.textContent = '';
                if (stokAlert) {
                    stokAlert.classList.add('hidden');
                    stokAlert.textContent = '';
                }
                locSelect.classList.remove('border-rose-500', 'focus:border-rose-500', 'focus:ring-rose-500');
                if (stokInput) stokInput.classList.remove('border-rose-500', 'focus:border-rose-500', 'focus:ring-rose-500');
            };

            if (!selectedOpt || !selectedOpt.value) {
                reset();
                return;
            }

            const code = selectedOpt.getAttribute('data-code') || '';
            const capacity = parseFloat(selectedOpt.getAttribute('data-capacity'));
            const sisa = parseFloat(selectedOpt.getAttribute('data-sisa'));
            const satuanLoc = selectedOpt.getAttribute('data-satuan') || '';
            const unitSuffix = satuanLoc ? ' ' + satuanLoc : '';
            const stokMin = parseFloat(stokInput ? stokInput.value : 0) || 0;

            if (capacity < 0) {
                reset();
                return;
            }

            const isExceeded = stokMin > capacity;
            const isFull = sisa <= 0;
            const isOverRemaining = !isExceeded && !isFull && stokMin > sisa;

            reset();

            if (isExceeded) {
                locSelect.classList.add('border-rose-500', 'focus:border-rose-500', 'focus:ring-rose-500');
                if (stokInput) stokInput.classList.add('border-rose-500', 'focus:border-rose-500', 'focus:ring-rose-500');
                infoCard.className = 'mt-1.5 text-xs font-medium text-rose-600';
                infoCard.textContent = `Batas stok minimum (${stokMin.toLocaleString('id-ID')}) melebihi kapasitas maksimal rak ${code} (${capacity.toLocaleString('id-ID')}${unitSuffix}).`;
                if (stokAlert) {
                    stokAlert.classList.remove('hidden');
                    stokAlert.textContent = `Melebihi kapasitas maksimal rak ${code} (${capacity.toLocaleString('id-ID')}${unitSuffix})`;
                }
            } else if (isFull) {
                infoCard.className = 'mt-1.5 text-xs font-medium text-rose-600';
                infoCard.textContent = `Rak ${code} saat ini sudah penuh (Sisa: 0${unitSuffix}).`;
            } else if (isOverRemaining) {
                infoCard.className = 'mt-1.5 text-xs font-medium text-amber-600';
                infoCard.textContent = `Sisa ruang rak ${code} (${sisa.toLocaleString('id-ID')}${unitSuffix}) lebih kecil dari batas stok minimum (${stokMin.toLocaleString('id-ID')}).`;
            }
        }

        const locSelect = document.getElementById('location_id');
        const stokInput = document.getElementById('stok_minimum');

        if (locSelect) {
            locSelect.addEventListener('change', updateRackCapacityNotice);
        }

        if (stokInput) {
            stokInput.addEventListener('input', updateRackCapacityNotice);
            stokInput.addEventListener('change', updateRackCapacityNotice);
        }

        // Form submit validation safeguard
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                if (locSelect && stokInput) {
                    const selectedOpt = locSelect.options[locSelect.selectedIndex];
                    if (selectedOpt && selectedOpt.value) {
                        const capacity = parseFloat(selectedOpt.getAttribute('data-capacity'));
                        const code = selectedOpt.getAttribute('data-code');
                        const stokMin = parseFloat(stokInput.value) || 0;
                        if (capacity > 0 && stokMin > capacity) {
                            e.preventDefault();
                            const msg = 'Batas stok minimum (' + stokMin.toLocaleString('id-ID') + ') melebihi kapasitas maksimal rak ' + code + ' (' + capacity.toLocaleString('id-ID') + '). Silakan sesuaikan batas stok minimum atau pilih lokasi rak yang lain.';
                            if (window.toast) {
                                window.toast.error(msg, 'Gagal Menyimpan Barang', 6000);
                            } else {
                                alert('Gagal Menyimpan Barang!\n\n' + msg);
                            }
                            stokInput.focus();
                            return false;
                        }
                    }
                }
            });
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', updateRackCapacityNotice);
        if (document.readyState === 'complete' || document.readyState === 'interactive') {
            updateRackCapacityNotice();
        }
    </script>
</x-app-layout>
