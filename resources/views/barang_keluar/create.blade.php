<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3 min-w-0">
            <x-back-button :href="route('barang-keluar.index')" />
            <div class="min-w-0">
                <h2 class="font-bold text-xl sm:text-2xl text-slate-800 leading-tight truncate">
                    {{ __('Rilis Pengeluaran Manual') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="w-full">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-8">
            <form id="barang-keluar-form" method="POST" action="{{ route('barang-keluar.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="barang_id" :value="__('Pilih Barang')" :required="true" />
                        <select id="barang_id" name="barang_id" required class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" autofocus>
                            <option value="">-- Pilih Barang --</option>
                            @foreach($barangs as $barang)
                                <option value="{{ $barang->id }}" {{ old('barang_id') == $barang->id ? 'selected' : '' }}>
                                    {{ $barang->kode_barang }} - {{ $barang->nama_barang }} ({{ $barang->lokasi_gudang }}{{ $barang->kode_rak ? ' • ' . $barang->kode_rak : '' }} | Stok: {{ $barang->stok }} {{ $barang->satuan }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('barang_id')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="jumlah" :value="__('Jumlah Pengeluaran')" :required="true" />
                        <x-text-input id="jumlah" class="block mt-1 w-full" type="number" name="jumlah" :value="old('jumlah')" required min="1" placeholder="Masukkan jumlah rilis" />
                        <x-input-error :messages="$errors->get('jumlah')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="tanggal" :value="__('Tanggal Pengeluaran')" :required="true" />
                        <x-text-input id="tanggal" class="block mt-1 w-full" type="date" name="tanggal" :value="old('tanggal', date('Y-m-d'))" required />
                        <x-input-error :messages="$errors->get('tanggal')" class="mt-2" />
                    </div>

                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-100">
                        <a href="{{ route('barang-keluar.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm font-semibold rounded-lg transition-colors">
                            Batal
                        </a>
                        <button type="submit" class="inline-flex items-center px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow transition-colors">
                            Rilis Barang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Mouse-free keyboard navigation script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('barang-keluar-form');
            if (!form) return;

            function getNavigableInputs() {
                return Array.from(form.querySelectorAll(
                    'input:not([type="hidden"]):not([disabled]):not([tabindex="-1"]), select:not([disabled]):not([tabindex="-1"]), textarea:not([disabled]):not([tabindex="-1"])'
                )).filter(el => el.offsetParent !== null);
            }

            const initial = document.getElementById('barang_id');
            if (initial) initial.focus();

            form.addEventListener('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                    e.preventDefault();
                    form.requestSubmit();
                    return;
                }

                if (e.key === 'Enter' && !e.shiftKey && !e.ctrlKey && !e.altKey && !e.metaKey) {
                    const target = e.target;
                    if (target.tagName === 'BUTTON' || target.tagName === 'A') return;

                    if (target.matches('input, select, textarea')) {
                        e.preventDefault();
                        const inputs = getNavigableInputs();
                        const idx = inputs.indexOf(target);
                        if (idx >= 0 && idx < inputs.length - 1) {
                            inputs[idx + 1].focus();
                            if (inputs[idx + 1].select && inputs[idx + 1].type !== 'date') inputs[idx + 1].select();
                        } else if (idx === inputs.length - 1) {
                            form.requestSubmit();
                        }
                    }
                }

                if (e.key === 'Enter' && e.shiftKey && !e.ctrlKey && !e.altKey) {
                    const target = e.target;
                    if (target.matches('input, select, textarea')) {
                        e.preventDefault();
                        const inputs = getNavigableInputs();
                        const idx = inputs.indexOf(target);
                        if (idx > 0) {
                            inputs[idx - 1].focus();
                            if (inputs[idx - 1].select && inputs[idx - 1].type !== 'date') inputs[idx - 1].select();
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>
