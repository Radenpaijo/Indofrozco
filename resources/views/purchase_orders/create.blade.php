<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3 sm:gap-4 min-w-0">
            <x-back-button :href="route('purchase-orders.index')" />
            <h2 class="text-2xl font-bold text-[#1e295d] tracking-tight truncate">
                {{ __('Buat Purchase Order (PO)') }}
            </h2>
        </div>
    </x-slot>

    <!-- 1 Card Utama (Full Width, Tanpa Ditumpuk-tumpuk) -->
    <div class="w-full bg-white rounded-2xl border border-slate-200/80 shadow-2xs p-5 sm:p-8 space-y-6">
        <form method="POST" action="{{ route('purchase-orders.store') }}" class="space-y-6" id="po-form">
            @csrf

            <!-- If linked to a PR -->
            @if($linkedPR)
                <input type="hidden" name="pp_id" value="{{ $linkedPR->id }}">
                <div class="bg-blue-50/70 border border-blue-200 text-blue-900 px-4 py-3 rounded-xl flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold">Menghubungkan dengan PR: {{ $linkedPR->no_pr }}</span>
                        <p class="text-[11px] text-blue-700 mt-0.5">Dibuat oleh {{ $linkedPR->user->name ?? 'Gudang' }} pada {{ \Carbon\Carbon::parse($linkedPR->tanggal)->format('d F Y') }}</p>
                    </div>
                </div>
            @endif

            <div class="space-y-4">
                <div>
                    <label for="supplier_id" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Pilih Supplier (Vendor) <span class="text-rose-500 font-bold">*</span>
                    </label>
                    <select id="supplier_id" name="supplier_id" required class="h-11 w-full rounded-xl border-slate-300 bg-white text-xs sm:text-sm font-medium focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition" autofocus>
                        <option value="">-- Pilih Supplier --</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->nama_supplier }} (Kontak: {{ $supplier->kontak ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('supplier_id')" class="mt-1" />
                </div>

                <div>
                    <label for="syarat_pembayaran" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Syarat Pembayaran (Payment Terms)
                    </label>
                    <input type="text" id="syarat_pembayaran" name="syarat_pembayaran" value="{{ old('syarat_pembayaran', 'Net 30 Days') }}" class="h-11 w-full rounded-xl border-slate-300 bg-white text-xs sm:text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition" placeholder="Contoh: Net 30 Days / COD / DP 50%">
                </div>
            </div>

            <div>
                <div class="mb-3">
                    <h3 class="text-sm font-bold text-[#1e295d]">Item Barang & Detail Harga</h3>
                </div>

                <div class="w-full sm:overflow-x-auto sm:border sm:border-slate-200 sm:rounded-xl">
                        <table class="w-full text-left border-collapse block sm:table responsive-card-sm" id="items-table">
                            <thead class="hidden sm:table-header-group">
                                <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-600 whitespace-nowrap">
                                    <th class="py-3 px-4 border-r border-slate-200 min-w-[220px]">Barang <span class="text-rose-500 font-bold">*</span></th>
                                    <th class="py-3 px-4 border-r border-slate-200 min-w-[110px]">Jumlah Pesan <span class="text-rose-500 font-bold">*</span></th>
                                    <th class="py-3 px-4 border-r border-slate-200 min-w-[130px]">Harga Satuan (Rp) <span class="text-rose-500 font-bold">*</span></th>
                                    <th class="py-3 px-4 border-r border-slate-200 min-w-[130px]">Subtotal (Rp)</th>
                                    <th class="py-3 px-4 text-center w-16">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="block sm:table-row-group sm:divide-y sm:divide-slate-200 text-sm space-y-3 sm:space-y-0" id="items-tbody">
                                @if($linkedPR && $linkedPR->details->isNotEmpty())
                                    @foreach($linkedPR->details as $idx => $d)
                                        <tr class="item-row grid grid-cols-2 gap-3 p-3.5 sm:p-4 bg-slate-50/70 rounded-xl border border-slate-200/80 sm:table-row sm:p-0 sm:border-0 sm:rounded-none sm:bg-transparent sm:gap-0 hover:bg-slate-50/80 transition-colors">
                                            <td class="col-span-2 sm:col-auto p-0 sm:py-3.5 sm:px-4 sm:border-r sm:border-slate-200">
                                                <div class="flex items-center justify-between pb-2 mb-1 border-b border-slate-200/70 sm:hidden">
                                                    <span class="text-xs font-bold text-slate-800 font-sans item-badge">Item #{{ $idx + 1 }}</span>
                                                    <button type="button" class="text-xs font-semibold text-rose-500 hover:text-rose-700 remove-row-btn transition">
                                                        Hapus
                                                    </button>
                                                </div>
                                                <label class="block sm:hidden text-xs font-semibold text-slate-700 mb-1">Barang <span class="text-rose-500 font-bold">*</span></label>
                                                <select name="items[{{ $idx }}][barang_id]" required class="h-11 sm:h-10 w-full rounded-xl sm:rounded-lg border-slate-300 bg-white text-xs sm:text-sm font-semibold focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                                    <option value="">-- Pilih Barang --</option>
                                                    @foreach($barangs as $barang)
                                                        <option value="{{ $barang->id }}" {{ $d->barang_id == $barang->id ? 'selected' : '' }}>
                                                            {{ $barang->kode_barang }} - {{ $barang->nama_barang }} (Stok: {{ $barang->stok }} {{ $barang->satuan }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="col-span-1 sm:col-auto p-0 sm:py-3.5 sm:px-4 sm:border-r sm:border-slate-200">
                                                <label class="block sm:hidden text-xs font-semibold text-slate-700 mb-1">Jumlah Pesan <span class="text-rose-500 font-bold">*</span></label>
                                                <input type="number" name="items[{{ $idx }}][jumlah]" required min="1" value="{{ $d->jumlah }}" placeholder="Jumlah" class="h-11 sm:h-10 w-full rounded-xl sm:rounded-lg border-slate-300 bg-white text-sm font-bold text-slate-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 input-qty">
                                            </td>
                                            <td class="col-span-1 sm:col-auto p-0 sm:py-3.5 sm:px-4 sm:border-r sm:border-slate-200">
                                                <label class="block sm:hidden text-xs font-semibold text-slate-700 mb-1">Harga Satuan <span class="text-rose-500 font-bold">*</span></label>
                                                <input type="number" name="items[{{ $idx }}][harga_satuan]" required min="0" placeholder="Harga Satuan" class="h-11 sm:h-10 w-full rounded-xl sm:rounded-lg border-slate-300 bg-white text-sm font-bold text-slate-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 input-price">
                                            </td>
                                            <td class="col-span-1 sm:col-auto p-0 sm:py-3.5 sm:px-4 sm:border-r sm:border-slate-200 font-semibold text-slate-700">
                                                <label class="block sm:hidden text-xs font-semibold text-slate-700 mb-1">Subtotal</label>
                                                <span class="row-subtotal block py-2 sm:py-0 text-sm font-bold text-slate-900 font-mono">Rp 0</span>
                                            </td>
                                            <td class="hidden sm:table-cell py-3.5 px-4 text-center">
                                                <button type="button" class="h-8 w-8 inline-flex items-center justify-center text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition remove-row-btn" title="Hapus Baris">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr class="item-row grid grid-cols-2 gap-3 p-3.5 sm:p-4 bg-slate-50/70 rounded-xl border border-slate-200/80 sm:table-row sm:p-0 sm:border-0 sm:rounded-none sm:bg-transparent sm:gap-0 hover:bg-slate-50/80 transition-colors">
                                        <td class="col-span-2 sm:col-auto p-0 sm:py-3.5 sm:px-4 sm:border-r sm:border-slate-200">
                                            <div class="flex items-center justify-between pb-2 mb-1 border-b border-slate-200/70 sm:hidden">
                                                <span class="text-xs font-bold text-slate-800 font-sans item-badge">Item #1</span>
                                                <button type="button" class="text-xs font-semibold text-rose-500 hover:text-rose-700 remove-row-btn transition">
                                                    Hapus
                                                </button>
                                            </div>
                                            <label class="block sm:hidden text-xs font-semibold text-slate-700 mb-1">Barang <span class="text-rose-500 font-bold">*</span></label>
                                            <select name="items[0][barang_id]" required class="h-11 sm:h-10 w-full rounded-xl sm:rounded-lg border-slate-300 bg-white text-xs sm:text-sm font-semibold focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                                <option value="">-- Pilih Barang --</option>
                                                @foreach($barangs as $barang)
                                                    <option value="{{ $barang->id }}">
                                                        {{ $barang->kode_barang }} - {{ $barang->nama_barang }} (Stok: {{ $barang->stok }} {{ $barang->satuan }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="col-span-1 sm:col-auto p-0 sm:py-3.5 sm:px-4 sm:border-r sm:border-slate-200">
                                            <label class="block sm:hidden text-xs font-semibold text-slate-700 mb-1">Jumlah Pesan <span class="text-rose-500 font-bold">*</span></label>
                                            <input type="number" name="items[0][jumlah]" required min="1" placeholder="Jumlah" class="h-11 sm:h-10 w-full rounded-xl sm:rounded-lg border-slate-300 bg-white text-sm font-bold text-slate-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 input-qty">
                                        </td>
                                        <td class="col-span-1 sm:col-auto p-0 sm:py-3.5 sm:px-4 sm:border-r sm:border-slate-200">
                                            <label class="block sm:hidden text-xs font-semibold text-slate-700 mb-1">Harga Satuan <span class="text-rose-500 font-bold">*</span></label>
                                            <input type="number" name="items[0][harga_satuan]" required min="0" placeholder="Harga Satuan" class="h-11 sm:h-10 w-full rounded-xl sm:rounded-lg border-slate-300 bg-white text-sm font-bold text-slate-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 input-price">
                                        </td>
                                        <td class="col-span-1 sm:col-auto p-0 sm:py-3.5 sm:px-4 sm:border-r sm:border-slate-200 font-semibold text-slate-700">
                                            <label class="block sm:hidden text-xs font-semibold text-slate-700 mb-1">Subtotal</label>
                                            <span class="row-subtotal block py-2 sm:py-0 text-sm font-bold text-slate-900 font-mono">Rp 0</span>
                                        </td>
                                        <td class="hidden sm:table-cell py-3.5 px-4 text-center">
                                            <button type="button" class="h-8 w-8 inline-flex items-center justify-center text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition remove-row-btn" title="Hapus Baris">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        <button type="button" id="add-row-btn" class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 hover:text-blue-800 transition py-1">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah Item Barang
                        </button>
                    </div>
                </div>

                <!-- Kalkulasi Pajak & Total PO -->
                <div class="pt-4 border-t border-slate-200 space-y-4">
                    <h4 class="text-xs font-bold text-[#1e295d] uppercase tracking-wider">Rincian Pembayaran & Pajak (PPN)</h4>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Catatan Tambahan</label>
                            <textarea name="catatan" rows="4" class="w-full rounded-xl border-slate-300 bg-white text-xs sm:text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition" placeholder="Instruksi pengiriman, lokasi bongkar muat, dll...">{{ old('catatan') }}</textarea>
                        </div>

                        <div class="space-y-3 p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs sm:text-sm">
                            <div class="flex justify-between items-center">
                                <span class="text-slate-600 font-medium">Subtotal Item:</span>
                                <span class="font-bold text-slate-800 font-mono" id="display-subtotal">Rp 0</span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-slate-600 font-medium">Potongan Diskon (Rp):</span>
                                <input type="number" step="0.01" min="0" id="diskon" name="diskon" value="{{ old('diskon', 0) }}" class="w-32 h-9 text-right rounded-lg border-slate-300 text-xs font-semibold focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-slate-600 font-medium">Pajak PPN (%):</span>
                                <div class="flex items-center space-x-1.5">
                                    <input type="number" step="0.1" min="0" max="100" id="pajak_persen" name="pajak_persen" value="{{ old('pajak_persen', 11.0) }}" class="w-20 h-9 text-right rounded-lg border-slate-300 text-xs font-bold focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                    <span class="text-xs font-bold text-slate-500">%</span>
                                </div>
                            </div>

                            <div class="flex justify-between items-center text-slate-600 text-xs border-t border-slate-200/60 pt-2">
                                <span>Nominal PPN:</span>
                                <span class="font-bold text-slate-700 font-mono" id="display-pajak-nominal">Rp 0</span>
                            </div>

                            <div class="flex justify-between items-center text-base font-extrabold text-blue-700 border-t border-slate-200 pt-3">
                                <span>TOTAL PO:</span>
                                <span class="font-mono text-lg" id="display-total-harga">Rp 0</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                    <a href="{{ route('purchase-orders.index') }}" class="px-5 py-2.5 text-xs font-semibold text-slate-600 hover:text-slate-800 rounded-xl hover:bg-slate-100 transition">
                        Batal
                    </a>
                    <button type="submit" class="h-11 px-6 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan & Terbitkan PO
                    </button>
                </div>
            </form>
    </div>

    <!-- Javascript to handle dynamic calculations & mouse-free keyboard navigation -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('po-form');
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

            tbody.addEventListener('input', calculateTotals);
            diskonInput.addEventListener('input', calculateTotals);
            pajakPersenInput.addEventListener('input', calculateTotals);

            function reindexRows() {
                const rows = document.querySelectorAll('.item-row');
                rows.forEach((row, index) => {
                    const badge = row.querySelector('.item-badge');
                    if (badge) {
                        badge.textContent = `Item #${index + 1}`;
                    }
                    const select = row.querySelector('select');
                    if (select) select.name = `items[${index}][barang_id]`;
                    const qtyInput = row.querySelector('.input-qty');
                    if (qtyInput) qtyInput.name = `items[${index}][jumlah]`;
                    const priceInput = row.querySelector('.input-price');
                    if (priceInput) priceInput.name = `items[${index}][harga_satuan]`;
                });
                rowIndex = rows.length;
            }

            function addNewRow(focusFirst = true) {
                const firstRow = document.querySelector('.item-row');
                if (!firstRow) return;

                const newRow = firstRow.cloneNode(true);
                
                const select = newRow.querySelector('select');
                select.selectedIndex = 0;

                const qtyInput = newRow.querySelector('.input-qty');
                qtyInput.value = '';

                const priceInput = newRow.querySelector('.input-price');
                priceInput.value = '';

                newRow.querySelector('.row-subtotal').textContent = 'Rp 0';

                tbody.appendChild(newRow);
                registerDeleteBtn(newRow.querySelector('.remove-row-btn'));
                reindexRows();
                calculateTotals();

                if (focusFirst) {
                    setTimeout(() => select.focus(), 20);
                }
            }

            if (addBtn) addBtn.addEventListener('click', () => addNewRow(true));

            function registerDeleteBtn(btn) {
                if (!btn) return;
                btn.addEventListener('click', function() {
                    const rowsCount = document.querySelectorAll('.item-row').length;
                    if (rowsCount > 1) {
                        const row = btn.closest('.item-row');
                        const prev = row.previousElementSibling;
                        const next = row.nextElementSibling;
                        row.remove();
                        reindexRows();
                        calculateTotals();
                        if (prev) {
                            const inp = prev.querySelector('.input-price') || prev.querySelector('select');
                            if (inp) inp.focus();
                        } else if (next) {
                            const inp = next.querySelector('select, input');
                            if (inp) inp.focus();
                        }
                    } else {
                        if (window.toast) {
                            window.toast.warning('Minimal harus ada 1 item pesanan.', 'Peringatan');
                        } else {
                            alert('Minimal harus ada 1 item pesanan.');
                        }
                    }
                });
            }

            document.querySelectorAll('.remove-row-btn').forEach(registerDeleteBtn);

            // ==========================================
            // MOUSE-FREE KEYBOARD NAVIGATION ENGINE
            // ==========================================
            function getNavigableInputs() {
                return Array.from(form.querySelectorAll(
                    'input:not([type="hidden"]):not([disabled]):not([tabindex="-1"]), select:not([disabled]):not([tabindex="-1"]), textarea:not([disabled]):not([tabindex="-1"])'
                )).filter(el => el.offsetParent !== null);
            }

            const initialSelect = form.querySelector('.item-row select');
            if (initialSelect) {
                initialSelect.focus();
            }

            form.addEventListener('keydown', function(e) {
                // 1. Submit Shortcut: Ctrl + Enter
                if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                    e.preventDefault();
                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.click();
                    } else {
                        form.requestSubmit();
                    }
                    return;
                }

                // 2. Add Row Shortcut: Alt + A or Insert
                if ((e.altKey && (e.key === 'a' || e.key === 'A')) || e.key === 'Insert') {
                    e.preventDefault();
                    addNewRow(true);
                    return;
                }

                // 3. Delete Row Shortcut: Shift + Delete or Alt + Delete
                if ((e.shiftKey || e.altKey) && (e.key === 'Delete' || (e.altKey && e.key === 'Backspace'))) {
                    const row = document.activeElement ? document.activeElement.closest('.item-row') : null;
                    if (row) {
                        e.preventDefault();
                        const deleteBtn = row.querySelector('.remove-row-btn');
                        if (deleteBtn) deleteBtn.click();
                        return;
                    }
                }

                // 4. Enter Key: Forward navigation & auto-add row on last price
                if (e.key === 'Enter' && !e.shiftKey && !e.ctrlKey && !e.altKey && !e.metaKey) {
                    const target = e.target;

                    if (target.tagName === 'BUTTON' || target.tagName === 'A' || target.tagName === 'TEXTAREA') {
                        return;
                    }

                    const row = target.closest('.item-row');
                    const isPriceField = target.matches('.input-price');

                    if (row && isPriceField) {
                        e.preventDefault();
                        const rows = Array.from(tbody.querySelectorAll('.item-row'));
                        const isLastRow = rows[rows.length - 1] === row;

                        if (isLastRow) {
                            addNewRow(true);
                        } else {
                            const nextRow = row.nextElementSibling;
                            if (nextRow) {
                                const nextInput = nextRow.querySelector('select:not([disabled]), input:not([disabled])');
                                if (nextInput) {
                                    nextInput.focus();
                                    if (nextInput.select) nextInput.select();
                                }
                            }
                        }
                        return;
                    }

                    if (target.matches('input, select')) {
                        e.preventDefault();
                        const inputs = getNavigableInputs();
                        const currentIndex = inputs.indexOf(target);
                        if (currentIndex >= 0 && currentIndex < inputs.length - 1) {
                            const nextInput = inputs[currentIndex + 1];
                            nextInput.focus();
                            if (nextInput.select && typeof nextInput.select === 'function') {
                                nextInput.select();
                            }
                        }
                        return;
                    }
                }

                // 5. Shift + Enter: Move backward
                if (e.key === 'Enter' && e.shiftKey && !e.ctrlKey && !e.altKey) {
                    const target = e.target;
                    if (target.matches('input, select')) {
                        e.preventDefault();
                        const inputs = getNavigableInputs();
                        const currentIndex = inputs.indexOf(target);
                        if (currentIndex > 0) {
                            const prevInput = inputs[currentIndex - 1];
                            prevInput.focus();
                            if (prevInput.select && typeof prevInput.select === 'function') {
                                prevInput.select();
                            }
                        }
                        return;
                    }
                }
            });

            // Initial calculation on load
            calculateTotals();
        });
    </script>
</x-app-layout>
