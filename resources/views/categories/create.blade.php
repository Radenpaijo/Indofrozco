<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4 min-w-0">
            <x-back-button />
            <div class="min-w-0">
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    {{ __('Tambah Kategori Barang') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="w-full">
        <x-card class="p-6 sm:p-8">
            <form action="{{ route('categories.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="space-y-2">
                    <x-label for="code" required>Kode Kategori</x-label>
                    <x-input id="code" class="font-mono bg-slate-100 cursor-not-allowed text-slate-600" type="text" name="code" :value="old('code', $kodeOtomatis)" required readonly placeholder="CAT-001" />
                    <x-input-error :messages="$errors->get('code')" class="mt-2" />
                </div>

                <div class="space-y-2">
                    <x-label for="name" required>Nama Kategori</x-label>
                    <x-input id="name" type="text" name="name" :value="old('name')" required placeholder="Contoh: Bahan Baku Frozen" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div class="space-y-2">
                    <x-label for="status">Status</x-label>
                    <x-select id="status" name="status">
                        <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    </x-select>
                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                </div>

                <div class="space-y-2">
                    <x-label for="description">Keterangan</x-label>
                    <textarea id="description" name="description" rows="4" class="flex min-h-[80px] w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm placeholder:text-slate-400 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-950 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" placeholder="Catatan opsional...">{{ old('description') }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end space-x-3 pt-6 border-t border-slate-100">
                    <a href="{{ route('categories.index') }}">
                        <x-button type="button" variant="outline">Batal</x-button>
                    </a>
                    <x-button type="submit" variant="indigo">
                        Simpan Kategori
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
