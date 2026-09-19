<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Buat Purchase Requisition (PR)') }}
            </h2>
            <a href="{{ route('permintaan-pembelian.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg transition-colors">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Error messages -->
            @if(session('error'))
                <div class="bg-rose-50 border-l-4 border-rose-500 p-4 rounded shadow-sm mb-6">
                    <span class="text-rose-800 font-medium">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <form method="POST" action="{{ route('permintaan-pembelian.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <h4 class="text-md font-bold text-gray-800 mb-2">Item Barang Requisition</h4>
                        <p class="text-xs text-gray-400 mb-4">Pilih barang-barang yang stoknya habis atau menipis untuk diajukan pembelian.</p>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-100" id="items-table">
                                <thead>
                                    <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        <th class="px-4 py-3">Barang</th>
                                        <th class="px-4 py-3">Jumlah Pengajuan</th>
                                        <th class="px-4 py-3 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-sm" id="items-tbody">
                                    <tr class="item-row">
                                        <td class="px-4 py-3">
                                            <select name="items[0][barang_id]" required class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                                <option value="">-- Pilih Barang --</option>
                                                @foreach($barangs as $barang)
                                                    <option value="{{ $barang->id }}" {{ request('barang_id') == $barang->id ? 'selected' : '' }}>
                                                        {{ $barang->kode_barang }} - {{ $barang->nama_barang }} (Stok: {{ $barang->stok }} {{ $barang->satuan }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" name="items[0][jumlah]" required min="1" placeholder="Masukkan jumlah" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <button type="button" class="text-rose-600 hover:text-rose-900 font-semibold text-xs remove-row-btn">Hapus</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            <button type="button" id="add-row-btn" class="inline-flex items-center px-3 py-1.5 border border-indigo-600 text-indigo-600 text-xs font-bold rounded-lg hover:bg-indigo-50 transition-colors">
                                <svg class="h-3 w-3 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                [+] Tambah Item
                            </button>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-100">
                        <a href="{{ route('permintaan-pembelian.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm font-semibold rounded-lg transition-colors">
                            Batal
                        </a>
                        <button type="submit" class="inline-flex items-center px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow transition-colors">
                            Simpan & Kirim PR
                        </button>
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
