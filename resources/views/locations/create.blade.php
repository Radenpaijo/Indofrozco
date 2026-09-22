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

    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
            <form action="{{ route('locations.store') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <x-input-label for="warehouse_id" :value="__('Pilih Gudang')" />
                    <select id="warehouse_id" name="warehouse_id" required class="block mt-1 w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5">
                        <option value="">-- Pilih Gudang --</option>
                        @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}" {{ old('warehouse_id') == $wh->id ? 'selected' : '' }}>
                                {{ $wh->name }} ({{ $wh->code }})
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('warehouse_id')" class="mt-2" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="code" :value="__('Kode Rak')" />
                        <x-text-input id="code" class="block mt-1 w-full font-mono bg-slate-100 cursor-not-allowed text-slate-600" type="text" name="code" :value="old('code', $nextCode)" required readonly placeholder="RAK-001" />
                        <x-input-error :messages="$errors->get('code')" class="mt-2" />
                        <p class="text-[11px] text-slate-400 mt-1">Kode dibuat otomatis oleh sistem.</p>
                    </div>

                    <div>
                        <x-input-label for="name" :value="__('Nama Rak / Baris')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required placeholder="Contoh: Rak Cold Storage A-01" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="capacity" :value="__('Kapasitas Maksimal')" />
                        <x-text-input id="capacity" class="block mt-1 w-full" type="number" step="0.01" name="capacity" :value="old('capacity')" placeholder="Contoh: 5000" />
                        <x-input-error :messages="$errors->get('capacity')" class="mt-2" />
                        <p class="text-[11px] text-slate-400 mt-1">Kosongkan jika tidak ada batasan kapasitas.</p>
                    </div>

                    <div>
                        <x-input-label for="status" :value="__('Status')" />
                        <select id="status" name="status" class="block mt-1 w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5">
                            <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-6 border-t border-slate-100">
                    <a href="{{ route('locations.index') }}" class="inline-flex items-center px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition-all">
                        Batal
                    </a>
                    <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-md transition-all">
                        Simpan Rak
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
