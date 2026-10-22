<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4 min-w-0">
            <x-back-button />
            <div class="min-w-0">
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    {{ __('Buat Purchase Order (PO)') }}
                </h2>
                <p class="text-xs text-slate-500 mt-1">Formulir pemesanan barang resmi ke vendor/supplier.</p>
            </div>
        </div>
    </x-slot>

    <div class="w-full">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-8">
                <form method="POST" action="{{ route('purchase-orders.store') }}" class="space-y-6" id="po-form">
                    @csrf

                    <!-- If linked to a PR -->
                    @if($linkedPR)
                        <input type="hidden" name="pp_id" value="{{ $linkedPR->id }}">
                        <div class="bg-indigo-50 border-l-4 border-indigo-500 p-4 rounded-r-xl shadow-sm flex items-center justify-between mb-6">
                            <div>
                                <span class="text-sm font-bold text-indigo-900">Menghubungkan dengan PR: {{ $linkedPR->no_pr }}</span>
                                <p class="text-xs text-indigo-700 mt-0.5">Dibuat oleh {{ $linkedPR->user->name ?? 'Gudang' }} pada {{ \Carbon\Carbon::parse($linkedPR->tanggal)->format('d F Y') }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="supplier_id" :value="__('Pilih Supplier (Vendor)')" />
                            <select id="supplier_id" name="supplier_id" required class="block mt-1 w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="">-- Pilih Supplier --</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->nama_supplier }} (Kontak: {{ $supplier->kontak ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('supplier_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="syarat_pembayaran" :value="__('Syarat Pembayaran (Payment Terms)')" />
                            <input type="text" id="syarat_pembayaran" name="syarat_pembayaran" value="{{ old('syarat_pembayaran', 'Net 30 Days') }}" class="block mt-1 w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Contoh: Net 30 Days / COD / DP 50%">
                        </div>
                    </div>

                    <div>
                        <h4 class="text-md font-bold text-slate-800 mb-2">Item Barang & Detail Harga</h4>
                        <p class="text-xs text-slate-400 mb-4">Masukkan kuantitas dan harga satuan penawaran dari supplier.</p>

                        <div class="overflow-x-auto border border-slate-300 rounded-xl">
                            <table class="w-full text-left border-collapse" id="items-table">
                                <thead>
                                    <tr class="bg-slate-100 border-b border-slate-300 text-[11px] font-bold uppercase tracking-wider text-slate-700">
                                        <th class="py-3.5 px-4 border-r border-slate-300">Barang</th>
                                        <th class="py-3.5 px-4 border-r border-slate-300">Jumlah Pesan</th>
                                        <th class="py-3.5 px-4 border-r border-slate-300">Harga Satuan (Rp)</th>
                                        <th class="py-3.5 px-4 border-r border-slate-300">Subtotal (Rp)</th>
                                        <th class="py-3.5 px-4 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 text-sm" id="items-tbody">
                                    @if($linkedPR && $linkedPR->details->isNotEmpty())
                                        @foreach($linkedPR->details as $idx => $d)
                                            <tr class="item-row hover:bg-slate-50">
                                                <td class="py-3.5 px-4 border-r border-slate-200">
                                                    <select name="items[{{ $idx }}][barang_id]" required class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                                        <option value="">-- Pilih Barang --</option>
                                                        @foreach($barangs as $barang)
                                                            <option value="{{ $barang->id }}" {{ $d->barang_id == $barang->id ? 'selected' : '' }}>
                                                                {{ $barang->kode_barang }} - {{ $barang->nama_barang }} (Stok: {{ $barang->stok }} {{ $barang->satuan }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="py-3.5 px-4 border-r border-slate-200">
                                                    <input type="number" name="items[{{ $idx }}][jumlah]" required min="1" value="{{ $d->jumlah }}" placeholder="Jumlah" class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm input-qty">
                                                </td>
                                                <td class="py-3.5 px-4 border-r border-slate-200">
                                                    <input type="number" name="items[{{ $idx }}][harga_satuan]" required min="0" placeholder="Harga Satuan" class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm input-price">
                                                </td>
                                                <td class="py-3.5 px-4 border-r border-slate-200 font-semibold text-slate-700 row-subtotal">
                                                    Rp 0
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
                                        @endforeach
                                    @else
                                        <tr class="item-row hover:bg-slate-50">
                                            <td class="py-3.5 px-4 border-r border-slate-200">
                                                <select name="items[0][barang_id]" required class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                                    <option value="">-- Pilih Barang --</option>
                                                    @foreach($barangs as $barang)
                                                        <option value="{{ $barang->id }}">
                                                            {{ $barang->kode_barang }} - {{ $barang->nama_barang }} (Stok: {{ $barang->stok }} {{ $barang->satuan }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="py-3.5 px-4 border-r border-slate-200">
                                                <input type="number" name="items[0][jumlah]" required min="1" placeholder="Jumlah" class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm input-qty">
                                            </td>
                                            <td class="py-3.5 px-4 border-r border-slate-200">
                                                <input type="number" name="items[0][harga_satuan]" required min="0" placeholder="Harga Satuan" class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm input-price">
                                            </td>
                                            <td class="py-3.5 px-4 border-r border-slate-200 font-semibold text-slate-700 row-subtotal">
                                                Rp 0
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
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            <button type="button" id="add-row-btn" class="inline-flex items-center px-3.5 py-2 border border-indigo-600 text-indigo-600 text-xs font-bold rounded-xl hover:bg-indigo-50 transition-colors">
                                <svg class="h-3.5 w-3.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Tambah Item Barang
                            </button>
                        </div>
                    </div>

                    <!-- Kalkulasi Pajak & Total PO -->
                    <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6 space-y-4">
                        <h4 class="text-sm font-bold uppercase tracking-wider text-slate-700 border-b border-slate-200 pb-2">Rincian Pembayaran & Pajak (PPN)</h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Catatan Tambahan</label>
                                <textarea name="catatan" rows="4" class="w-full rounded-xl border-slate-300 text-sm" placeholder="Catatan instruksi pengiriman, lokasi bongkar muat, dll...">{{ old('catatan') }}</textarea>
                            </div>

                            <div class="space-y-3 bg-white p-4 rounded-xl border border-slate-200 text-sm">
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-600 font-medium">Subtotal Item:</span>
                                    <span class="font-bold text-slate-800" id="display-subtotal">Rp 0</span>
                                </div>

                                <div class="flex justify-between items-center">
                                    <span class="text-slate-600 font-medium">Potongan Diskon (Rp):</span>
                                    <input type="number" step="0.01" min="0" id="diskon" name="diskon" value="{{ old('diskon', 0) }}" class="w-36 text-right rounded-lg border-slate-300 text-xs font-semibold">
                                </div>

                                <div class="flex justify-between items-center">
                                    <span class="text-slate-600 font-medium">Pajak PPN (%):</span>
                                    <div class="flex items-center space-x-2">
                                        <input type="number" step="0.1" min="0" max="100" id="pajak_persen" name="pajak_persen" value="{{ old('pajak_persen', 11.0) }}" class="w-20 text-right rounded-lg border-slate-300 text-xs font-bold">
                                        <span class="text-xs font-bold text-slate-500">%</span>
                                    </div>
                                </div>

                                <div class="flex justify-between items-center text-slate-600 text-xs border-t border-slate-100 pt-2">
                                    <span>Nominal PPN:</span>
                                    <span class="font-bold text-slate-700" id="display-pajak-nominal">Rp 0</span>
                                </div>

                                <div class="flex justify-between items-center text-base font-extrabold text-indigo-700 border-t border-slate-200 pt-3">
                                    <span>TOTAL PO (AKHIR):</span>
                                    <span id="display-total-harga">Rp 0</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-6 border-t border-slate-100">
                        <a href="{{ route('purchase-orders.index') }}" class="inline-flex items-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 text-sm font-semibold rounded-xl transition-colors">
                            Batal
                        </a>
                        <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-md shadow-indigo-600/20 transition-colors">
                            Simpan & Terbitkan PO
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Javascript Calculation & Row Add/Remove -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tbody = document.getElementById('items-tbody');
            const addBtn = document.getElementById('add-row-btn');
            const diskonInput = document.getElementById('diskon');
            const pajakPersenInput = document.getElementById('pajak_persen');
            let rowIndex = {{ $linkedPR ? $linkedPR->details->count() : 1 }};

            function formatRupiah(number) {
                return 'Rp ' + new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 }).format(number);
            }

            function calculateTotals() {
                let subtotalSum = 0;
                document.querySelectorAll('.item-row').forEach(row => {
                    const qty = parseFloat(row.querySelector('.input-qty').value) || 0;
                    const price = parseFloat(row.querySelector('.input-price').value) || 0;
                    const rowSubtotal = qty * price;
                    subtotalSum += rowSubtotal;

                    row.querySelector('.row-subtotal').textContent = formatRupiah(rowSubtotal);
                });

                const diskon = parseFloat(diskonInput.value) || 0;
                const subtotalAfterDiskon = Math.max(0, subtotalSum - diskon);
                const pajakPersen = parseFloat(pajakPersenInput.value) || 0;
                const pajakNominal = (subtotalAfterDiskon * pajakPersen) / 100;
                const grandTotal = subtotalAfterDiskon + pajakNominal;

                document.getElementById('display-subtotal').textContent = formatRupiah(subtotalSum);
                document.getElementById('display-pajak-nominal').textContent = formatRupiah(pajakNominal);
                document.getElementById('display-total-harga').textContent = formatRupiah(grandTotal);
            }

            // Register Listeners
            tbody.addEventListener('input', calculateTotals);
            diskonInput.addEventListener('input', calculateTotals);
            pajakPersenInput.addEventListener('input', calculateTotals);

            addBtn.addEventListener('click', function() {
                const firstRow = document.querySelector('.item-row');
                if (!firstRow) return;

                const newRow = firstRow.cloneNode(true);
                
                const select = newRow.querySelector('select');
                select.name = `items[${rowIndex}][barang_id]`;
                select.selectedIndex = 0;

                const qtyInput = newRow.querySelector('.input-qty');
                qtyInput.name = `items[${rowIndex}][jumlah]`;
                qtyInput.value = '';

                const priceInput = newRow.querySelector('.input-price');
                priceInput.name = `items[${rowIndex}][harga_satuan]`;
                priceInput.value = '';

                newRow.querySelector('.row-subtotal').textContent = 'Rp 0';

                tbody.appendChild(newRow);
                rowIndex++;

                registerDeleteBtn(newRow.querySelector('.remove-row-btn'));
                calculateTotals();
            });

            function registerDeleteBtn(btn) {
                btn.addEventListener('click', function() {
                    const rowsCount = document.querySelectorAll('.item-row').length;
                    if (rowsCount > 1) {
                        btn.closest('.item-row').remove();
                        calculateTotals();
                    } else {
                        alert('Minimal harus ada 1 item PO.');
                    }
                });
            }

            document.querySelectorAll('.remove-row-btn').forEach(registerDeleteBtn);
            calculateTotals();
        });
    </script>
</x-app-layout>
