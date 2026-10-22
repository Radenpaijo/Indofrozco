<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-4 min-w-0">
                <x-back-button />
                <div class="min-w-0">
                    <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                        {{ __('Tambah Lokasi / Rak Gudang') }}
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">
                        Input data rak gudang baru dan kapasitas maksimalnya.
                    </p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="w-full">
        <x-card class="p-6 sm:p-8">
            <form action="{{ route('locations.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <x-label for="warehouse_id">Pilih Gudang *</x-label>
                        <a href="{{ route('warehouses.create') }}" target="_blank" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 hover:underline flex items-center">
                            + Tambah Gudang Baru
                        </a>
                    </div>
                    <x-select id="warehouse_id" name="warehouse_id" required>
                        <option value="">-- Pilih Gudang --</option>
                        @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}" {{ old('warehouse_id') == $wh->id ? 'selected' : '' }}>
                                {{ $wh->name }} ({{ $wh->code }})
                            </option>
                        @endforeach
                    </x-select>
                    @if($warehouses->isEmpty())
                        <p class="text-xs text-amber-600 font-semibold">⚠️ Belum ada Data Gudang. <a href="{{ route('warehouses.create') }}" target="_blank" class="underline text-indigo-600">Klik di sini untuk menambah Gudang Baru</a>.</p>
                    @endif
                    <x-input-error :messages="$errors->get('warehouse_id')" class="mt-2" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <x-label for="code">Kode Rak</x-label>
                        <x-input id="code" class="font-mono bg-slate-100 cursor-not-allowed text-slate-600" type="text" name="code" :value="old('code', $nextCode)" required readonly placeholder="RAK-001" />
                        <x-input-error :messages="$errors->get('code')" class="mt-2" />
                        <p class="text-[11px] text-slate-400">Kode dibuat otomatis oleh sistem.</p>
                    </div>

                    <div class="space-y-2">
                        <x-label for="name">Nama Rak / Baris</x-label>
                        <x-input id="name" type="text" name="name" :value="old('name')" required placeholder="Contoh: Rak Cold Storage A-01" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <x-label for="capacity">Kapasitas Maksimal</x-label>
                        <x-input id="capacity" type="number" step="0.01" name="capacity" :value="old('capacity')" placeholder="Contoh: 5000" />
                        <x-input-error :messages="$errors->get('capacity')" class="mt-2" />
                        <p class="text-[11px] text-slate-400">Kosongkan jika tidak ada batasan kapasitas.</p>
                    </div>

                    <div class="space-y-2">
                        <x-label for="status">Status</x-label>
                        <x-select id="status" name="status">
                            <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                        </x-select>
                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-6 border-t border-slate-100">
                    <a href="{{ route('locations.index') }}">
                        <x-button type="button" variant="outline">Batal</x-button>
                    </a>
                    <x-button type="submit" variant="indigo">
                        Simpan Rak
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
