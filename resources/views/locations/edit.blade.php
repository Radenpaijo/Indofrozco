<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-4 min-w-0">
                <x-back-button />
                <div class="min-w-0">
                    <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                        {{ __('Edit Lokasi / Rak Gudang') }}
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">Perbarui data lokasi rak {{ $location->name }}.</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="w-full">
        <x-card class="p-6 sm:p-8">
            <form action="{{ route('locations.update', $location) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="space-y-2">
                    <x-label for="warehouse_id">Pilih Gudang</x-label>
                    <x-select id="warehouse_id" name="warehouse_id" required>
                        @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}" {{ old('warehouse_id', $location->warehouse_id) == $wh->id ? 'selected' : '' }}>
                                {{ $wh->name }} ({{ $wh->code }})
                            </option>
                        @endforeach
                    </x-select>
                    <x-input-error :messages="$errors->get('warehouse_id')" class="mt-2" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <x-label for="code">Kode Rak</x-label>
                        <x-input id="code" class="font-mono bg-slate-50" type="text" name="code" :value="old('code', $location->code)" required autofocus />
                        <x-input-error :messages="$errors->get('code')" class="mt-2" />
                    </div>

                    <div class="space-y-2">
                        <x-label for="name">Nama Rak / Baris</x-label>
                        <x-input id="name" type="text" name="name" :value="old('name', $location->name)" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <x-label for="capacity">Kapasitas Maksimal</x-label>
                        <x-input id="capacity" type="number" step="0.01" name="capacity" :value="old('capacity', $location->capacity)" placeholder="Contoh: 5000" />
                        <x-input-error :messages="$errors->get('capacity')" class="mt-2" />
                        <p class="text-[11px] text-indigo-600 font-semibold">Stok terisi saat ini: {{ number_format($location->used_capacity ?? 0) }}</p>
                    </div>

                    <div class="space-y-2">
                        <x-label for="status">Status</x-label>
                        <x-select id="status" name="status">
                            <option value="active" {{ old('status', $location->status) === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ old('status', $location->status) === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                        </x-select>
                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                    </div>
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
