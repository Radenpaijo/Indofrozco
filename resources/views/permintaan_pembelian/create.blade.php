<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4 min-w-0">
            <x-back-button />
            <div class="min-w-0">
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    {{ __('Buat Purchase Requisition (PR)') }}
                </h2>
                <p class="text-xs text-slate-500 mt-1">Formulir pengajuan pengadaan / pembelian stok barang.</p>
            </div>
        </div>
    </x-slot>

    <div class="w-full">

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <form method="POST" action="{{ route('permintaan-pembelian.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <h4 class="text-md font-bold text-gray-800 mb-2">Item Barang Requisition</h4>
                        <p class="text-xs text-gray-400 mb-4">Pilih barang-barang yang stoknya habis atau menipis untuk diajukan pembelian.</p>

                        <div class="overflow-x-auto border border-slate-300 rounded-xl">
                            <table class="w-full text-left border-collapse" id="items-table">
                                <thead>
                                    <tr class="bg-slate-100 border-b border-slate-300 text-[11px] font-bold uppercase tracking-wider text-slate-700">
                                        <th class="py-3.5 px-4 border-r border-slate-300">Barang</th>
                                        <th class="py-3.5 px-4 border-r border-slate-300">Jumlah Pengajuan</th>
                                        <th class="py-3.5 px-4 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 text-sm" id="items-tbody">
                                    <tr class="item-row hover:bg-slate-50">
                                        <td class="py-3.5 px-4 border-r border-slate-200">
                                            <select name="items[0][barang_id]" required class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                                <option value="">-- Pilih Barang --</option>
                                                @foreach($barangs as $barang)
                                                    <option value="{{ $barang->id }}" {{ request('barang_id') == $barang->id ? 'selected' : '' }}>
                                                        {{ $barang->kode_barang }} - {{ $barang->nama_barang }} (Stok: {{ $barang->stok }} {{ $barang->satuan }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="py-3.5 px-4 border-r border-slate-200">
                                            <input type="number" name="items[0][jumlah]" required min="1" placeholder="Masukkan jumlah" class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            <button type="button" class="inline-flex items-center px-2.5 py-1.5 text-xs font-semibold text-rose-600 hover:text-white hover:bg-rose-600 border border-rose-200 hover:border-rose-600 rounded-lg transition-all duration-150 shadow-sm remove-row-btn">
                                                <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            <button type="button" id="add-row-btn" class="inline-flex items-center px-3.5 py-2 border border-indigo-600 text-indigo-600 text-xs font-bold rounded-xl hover:bg-indigo-50 transition-colors">
                                <svg class="h-3.5 w-3.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Tambah Item
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-6 border-t border-slate-200">
                        <a href="{{ route('permintaan-pembelian.index') }}">
                            <x-button variant="outline">Batal</x-button>
                        </a>
                        <x-button type="submit" variant="indigo">
                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Simpan & Kirim PR
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Javascript to handle dynamic rows -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tbody = document.getElementById('items-tbody');
            const addBtn = document.getElementById('add-row-btn');
            let rowIndex = 1;

            addBtn.addEventListener('click', function() {
                const firstRow = document.querySelector('.item-row');
                if (!firstRow) return;

                const newRow = firstRow.cloneNode(true);
                
                const select = newRow.querySelector('select');
                select.name = `items[${rowIndex}][barang_id]`;
                select.selectedIndex = 0; // Deselect any pre-selected query items

                const input = newRow.querySelector('input');
                input.name = `items[${rowIndex}][jumlah]`;
                input.value = '';

                tbody.appendChild(newRow);
                rowIndex++;

                registerDeleteBtn(newRow.querySelector('.remove-row-btn'));
            });

            function registerDeleteBtn(btn) {
                btn.addEventListener('click', function() {
                    const rowsCount = document.querySelectorAll('.item-row').length;
                    if (rowsCount > 1) {
                        btn.closest('.item-row').remove();
                    } else {
                        alert('Minimal harus ada 1 item diajukan.');
                    }
                });
            }

            document.querySelectorAll('.remove-row-btn').forEach(registerDeleteBtn);
        });
    </script>
</x-app-layout>
