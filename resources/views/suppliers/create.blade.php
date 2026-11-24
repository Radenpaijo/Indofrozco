<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4 min-w-0">
            <x-back-button />
            <div class="min-w-0">
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    {{ __('Tambah Supplier') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="w-full">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <form method="POST" action="{{ route('suppliers.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="nama_supplier" :value="__('Nama Supplier')" :required="true" />
                        <x-text-input id="nama_supplier" class="block mt-1 w-full" type="text" name="nama_supplier" :value="old('nama_supplier')" required autofocus placeholder="Contoh: PT Sumber Pangan Utama" />
                        <x-input-error :messages="$errors->get('nama_supplier')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="kontak" :value="__('Kontak / Telepon / HP')" />
                        <x-text-input id="kontak" class="block mt-1 w-full" type="text" name="kontak" :value="old('kontak')" placeholder="Contoh: 08123456789 atau (031) 555-555" />
                        <x-input-error :messages="$errors->get('kontak')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="alamat" :value="__('Alamat Lengkap')" />
                        <textarea id="alamat" name="alamat" rows="4" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Masukkan alamat lengkap perusahaan supplier...">{{ old('alamat') }}</textarea>
                        <x-input-error :messages="$errors->get('alamat')" class="mt-2" />
                    </div>

                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-100">
                        <a href="{{ route('suppliers.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm font-semibold rounded-lg transition-colors">
                            Batal
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow transition-colors">
                            Simpan Supplier
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
