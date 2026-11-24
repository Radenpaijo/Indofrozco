<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3 sm:gap-4 min-w-0">
            <x-back-button :href="route('locations.index')" />
            <div class="min-w-0">
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    {{ __('Edit Lokasi / Rak Gudang') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="w-full">
        <x-card class="p-6 sm:p-8">
            <form action="{{ route('locations.update', $location) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="space-y-2">
                    <x-label for="warehouse_id" required>Pilih Gudang</x-label>
                    <x-select id="warehouse_id" name="warehouse_id" required>
                        @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}" {{ old('warehouse_id', $location->warehouse_id) == $wh->id ? 'selected' : '' }}>
                                {{ $wh->name }} ({{ $wh->code }})
                            </option>
                        @endforeach
                    </x-select>
                    <x-input-error :messages="$errors->get('warehouse_id')" class="mt-2" />
                </div>

                <div class="space-y-2">
                    <x-label for="code" required>Kode Rak</x-label>
                    <x-input id="code" class="font-mono bg-slate-100 cursor-not-allowed text-slate-600" type="text" name="code" :value="old('code', $location->code)" required readonly />
                    <x-input-error :messages="$errors->get('code')" class="mt-2" />
                </div>

                <div class="space-y-2">
                    <x-label for="name" required>Nama Rak / Baris</x-label>
                    <x-input id="name" type="text" name="name" :value="old('name', $location->name)" required autofocus />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <x-label for="capacity">Kapasitas Maksimal & Satuan</x-label>
                        <a href="{{ route('units.create') }}" target="_blank" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 hover:underline">
                            + Tambah Satuan
                        </a>
                    </div>
                    @php
                        $currentSatuan = old('satuan', $location->satuan_label);
                        $satuanAdaDiMaster = $units->contains('name', $currentSatuan);
                    @endphp
                    <div class="flex gap-2">
                        <div class="flex-1">
                            <x-input id="capacity" type="number" step="0.01" name="capacity" :value="old('capacity', $location->capacity)" placeholder="Contoh: 5000" />
                        </div>
                        <div class="w-40">
                            <x-select id="satuan" name="satuan" required>
                                @if(!$satuanAdaDiMaster)
                                    <option value="" selected>-- Pilih Satuan --</option>
                                @endif
                                @foreach($units as $unit)
                                    <option value="{{ $unit->name }}" {{ $currentSatuan === $unit->name ? 'selected' : '' }}>
                                        {{ $unit->name }}
                                    </option>
                                @endforeach
                            </x-select>
                        </div>
                    </div>
                    @if(!$satuanAdaDiMaster && $currentSatuan)
                        <p class="text-[11px] text-amber-600 font-medium">
                            Satuan sebelumnya ("{{ $currentSatuan }}") tidak ada di Master Data Satuan. Pilih satuan yang tersedia atau tambahkan terlebih dahulu.
                        </p>
                    @endif
                    <x-input-error :messages="$errors->get('capacity')" class="mt-1" />
                    <x-input-error :messages="$errors->get('satuan')" class="mt-1" />
                    <p class="text-[11px] text-indigo-600 font-semibold">Stok terisi saat ini: {{ number_format($location->used_capacity ?? 0) }} {{ $location->satuan_label }}</p>
                </div>

                <div class="space-y-2">
                    <x-label for="status">Status</x-label>
                    <x-select id="status" name="status">
                        <option value="active" {{ old('status', $location->status) === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ old('status', $location->status) === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    </x-select>
                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end space-x-3 pt-6 border-t border-slate-100">
                    <a href="{{ route('locations.index') }}">
                        <x-button type="button" variant="outline">Batal</x-button>
                    </a>
                    <x-button type="submit" variant="indigo">
                        Simpan Perubahan
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
