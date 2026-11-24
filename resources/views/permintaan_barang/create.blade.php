<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3 sm:gap-4 min-w-0">
            <x-back-button :href="route('permintaan-barang.index')" />
            <h2 class="text-2xl font-bold text-[#1e295d] tracking-tight truncate">
                {{ __('Ajukan Permintaan Barang') }}
            </h2>
        </div>
    </x-slot>

    <!-- 1 Card Utama (Full Width, Tanpa Ditumpuk-tumpuk) -->
    <div class="w-full bg-white rounded-2xl border border-slate-200/80 shadow-2xs p-5 sm:p-8 space-y-6">
        <form id="permintaan-barang-form" method="POST" action="{{ route('permintaan-barang.store') }}" class="space-y-6">
            @csrf

            <div>
                <div class="mb-3">
                    <h3 class="text-sm font-bold text-[#1e295d]">Pilih Item & Jumlah</h3>
                </div>

                <div class="w-full sm:overflow-x-auto sm:border sm:border-slate-200 sm:rounded-xl">
                        <table class="w-full text-left border-collapse block sm:table responsive-card-sm" id="items-table">
                            <thead class="hidden sm:table-header-group">
                                <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-600 whitespace-nowrap">
                                    <th class="py-3 px-4 border-r border-slate-200 min-w-[240px]">Barang <span class="text-rose-500 font-bold">*</span></th>
                                    <th class="py-3 px-4 border-r border-slate-200 min-w-[150px]">Jumlah Diminta <span class="text-rose-500 font-bold">*</span></th>
                                    <th class="py-3 px-4 text-center w-16">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="block sm:table-row-group sm:divide-y sm:divide-slate-200 text-sm space-y-3 sm:space-y-0" id="items-tbody">
                                <tr class="item-row grid grid-cols-1 gap-3 p-3.5 sm:p-4 bg-slate-50/70 rounded-xl border border-slate-200/80 sm:table-row sm:p-0 sm:border-0 sm:rounded-none sm:bg-transparent sm:gap-0 hover:bg-slate-50/80 transition-colors">
                                    <td class="block sm:hidden p-0">
                                        <div class="flex items-center justify-between pb-2 mb-1 border-b border-slate-200/70">
                                            <span class="text-xs font-bold text-slate-800 font-sans item-badge">Item #1</span>
                                            <button type="button" class="text-xs font-semibold text-rose-500 hover:text-rose-700 remove-row-btn transition">
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                    <td class="p-0 sm:py-3.5 sm:px-4 sm:border-r sm:border-slate-200">
                                        <label class="block sm:hidden text-xs font-semibold text-slate-700 mb-1">Barang <span class="text-rose-500 font-bold">*</span></label>
                                        <select name="items[0][barang_id]" required class="h-11 sm:h-10 w-full rounded-xl sm:rounded-lg border-slate-300 bg-white text-xs sm:text-sm font-semibold focus:border-blue-500 focus:ring-1 focus:ring-blue-500" autofocus>
                                            <option value="">-- Pilih Barang --</option>
                                            @foreach($barangs as $barang)
                                                <option value="{{ $barang->id }}">{{ $barang->kode_barang }} - {{ $barang->nama_barang }} ({{ $barang->lokasi_gudang }}{{ $barang->kode_rak ? ' • ' . $barang->kode_rak : '' }} | Stok: {{ $barang->stok }} {{ $barang->satuan }})</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="p-0 sm:py-3.5 sm:px-4 sm:border-r sm:border-slate-200">
                                        <label class="block sm:hidden text-xs font-semibold text-slate-700 mb-1">Jumlah Diminta <span class="text-rose-500 font-bold">*</span></label>
                                        <input type="number" name="items[0][jumlah]" required min="1" placeholder="Masukkan jumlah" class="h-11 sm:h-10 w-full rounded-xl sm:rounded-lg border-slate-300 bg-white text-sm font-bold text-slate-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                    </td>
                                    <td class="hidden sm:table-cell py-3.5 px-4 text-center">
                                        <button type="button" class="h-8 w-8 inline-flex items-center justify-center text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition remove-row-btn" title="Hapus Baris" tabindex="-1">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        <button type="button" id="add-row-btn" class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 hover:text-blue-800 transition py-1">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah Item
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                    <a href="{{ route('permintaan-barang.index') }}" class="px-5 py-2.5 text-xs font-semibold text-slate-600 hover:text-slate-800 rounded-xl hover:bg-slate-100 transition">
                        Batal
                    </a>
                    <button type="submit" class="h-11 px-6 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                        Kirim Permintaan
                    </button>
                </div>
            </form>
    </div>

    <!-- Javascript to handle dynamic rows & mouse-free keyboard navigation -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('permintaan-barang-form');
            const tbody = document.getElementById('items-tbody');
            const addBtn = document.getElementById('add-row-btn');
            let rowIndex = 1;

            function reindexRows() {
                const rows = document.querySelectorAll('.item-row');
                rows.forEach((row, index) => {
                    const badge = row.querySelector('.item-badge');
                    if (badge) {
                        badge.textContent = `Item #${index + 1}`;
                    }
                    const select = row.querySelector('select');
                    if (select) select.name = `items[${index}][barang_id]`;
                    const input = row.querySelector('input');
                    if (input) input.name = `items[${index}][jumlah]`;
                });
                rowIndex = rows.length;
            }

            function addNewRow(focusFirst = true) {
                const firstRow = document.querySelector('.item-row');
                if (!firstRow) return;

                const newRow = firstRow.cloneNode(true);
                
                const select = newRow.querySelector('select');
                select.value = '';

                const input = newRow.querySelector('input');
                input.value = '';

                tbody.appendChild(newRow);
                registerDeleteBtn(newRow.querySelector('.remove-row-btn'));
                reindexRows();

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
                        if (prev) {
                            const inp = prev.querySelector('input') || prev.querySelector('select');
                            if (inp) inp.focus();
                        } else if (next) {
                            const inp = next.querySelector('select, input');
                            if (inp) inp.focus();
                        }
                    } else {
                        if (window.toast) {
                            window.toast.warning('Minimal harus ada 1 item permintaan.', 'Peringatan');
                        } else {
                            alert('Minimal harus ada 1 item permintaan.');
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

            // Autofocus on first item select
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

                // 4. Enter Key: Forward navigation & auto-add row on last quantity
                if (e.key === 'Enter' && !e.shiftKey && !e.ctrlKey && !e.altKey && !e.metaKey) {
                    const target = e.target;

                    if (target.tagName === 'BUTTON' || target.tagName === 'A') {
                        return;
                    }

                    const row = target.closest('.item-row');
                    const isQtyField = target.matches('input[type="number"]') || (target.name && target.name.includes('[jumlah]'));

                    if (row && isQtyField) {
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

                    if (target.matches('input, select, textarea')) {
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
                    if (target.matches('input, select, textarea')) {
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
        });
    </script>
</x-app-layout>
