<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4 min-w-0">
            <x-back-button />
            <div class="min-w-0">
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    {{ __('Tambah Gudang') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="w-full">
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
            <form action="{{ route('warehouses.store') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <x-input-label for="code" :value="__('Kode Gudang')" :required="true" />
                    <x-text-input id="code" class="block mt-1 w-full font-mono bg-slate-100 cursor-not-allowed text-slate-600" type="text" name="code" :value="old('code', $nextCode)" required readonly placeholder="GDG-001" />
                    <x-input-error :messages="$errors->get('code')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="name" :value="__('Nama Gudang')" :required="true" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required placeholder="Contoh: Cold Storage 01" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <div class="flex items-center justify-between">
                        <x-input-label for="warehouse_type_id" :value="__('Tipe Gudang *')" />
                        <a href="{{ route('warehouse-types.create') }}" target="_blank" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 hover:underline flex items-center">
                            + Tambah Tipe Gudang
                        </a>
                    </div>
                    <select id="warehouse_type_id" name="warehouse_type_id" required class="block mt-1 w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5">
                        <option value="">-- Pilih Tipe Gudang --</option>
                        @foreach($types as $type)
                            <option value="{{ $type->id }}" {{ old('warehouse_type_id') === $type->id ? 'selected' : '' }}>
                                {{ $type->name }} ({{ $type->code }})
                            </option>
                        @endforeach
                    </select>
                    @if($types->isEmpty())
                        <p class="text-xs text-amber-600 mt-1 font-semibold">⚠️ Belum ada Tipe Gudang. <a href="{{ route('warehouse-types.create') }}" target="_blank" class="underline text-indigo-600">Klik di sini untuk menambah Tipe Gudang Baru</a>.</p>
                    @endif
                    <x-input-error :messages="$errors->get('warehouse_type_id')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="status" :value="__('Status')" />
                    <select id="status" name="status" class="block mt-1 w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5">
                        <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="description" :value="__('Keterangan')" />
                    <textarea id="description" name="description" rows="4" class="block mt-1 w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Catatan opsional mengenai gudang ini...">{{ old('description') }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end space-x-3 pt-6 border-t border-slate-100">
                    <a href="{{ route('warehouses.index') }}" class="inline-flex items-center px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition-all">
                        Batal
                    </a>
                    <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-md transition-all">
                        Simpan Gudang
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
