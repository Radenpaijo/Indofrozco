<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4 min-w-0">
            <x-back-button />
            <div class="min-w-0">
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    {{ __('Form Sesi Stock Opname Baru') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="w-full">
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-200/80 p-4 sm:p-6">
                <form action="{{ route('stock-opnames.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Tanggal Sesi <span class="text-rose-500 font-bold">*</span></label>
                            <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required class="w-full rounded-xl border-slate-300 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Gudang</label>
                            <select name="warehouse_id" id="opname-warehouse" class="w-full rounded-xl border-slate-300 text-sm">
                                <option value="">Semua Gudang</option>
                                @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Lokasi / Rak</label>
                            <select name="location_id" id="opname-location" class="w-full rounded-xl border-slate-300 text-sm">
                                <option value="">Semua Rak</option>
                                @foreach($locations as $loc)
                                    <option value="{{ $loc->id }}" data-warehouse-id="{{ $loc->warehouse_id }}">{{ $loc->code }} - {{ $loc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Catatan Opname</label>
                        <input type="text" name="catatan" class="w-full rounded-xl border-slate-300 text-sm" placeholder="Contoh: Opname rutin akhir bulan September">
                    </div>

                    <hr class="border-slate-200" />

                    <div class="space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5">
                            <div class="flex items-center gap-2">
                                <h3 class="font-bold text-slate-800 text-base">Detail Perhitungan Fisik</h3>
                                <span id="opname-count" class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                    {{ $barangs->count() }} barang
                                </span>
                            </div>

                            <div class="relative w-full sm:w-64">
                                <input type="text" id="opname-search" placeholder="Cari nama atau kode barang..." class="h-10 w-full pl-9 pr-3 rounded-xl border-slate-300 bg-white text-xs sm:text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>

                        <div class="-mx-4 sm:-mx-6 lg:mx-0 lg:w-full lg:overflow-x-auto lg:border lg:border-slate-300 lg:rounded-xl">
                            <table class="w-full text-left border-collapse block lg:table responsive-card-lg" id="opname-table">
                                <thead class="hidden lg:table-header-group">
                                    <tr class="bg-slate-100 border-b border-slate-300 text-[11px] font-bold uppercase tracking-wider text-slate-700">
                                        <th class="py-3.5 px-4 border-r border-slate-300">Barang / Item</th>
                                        <th class="py-3.5 px-4 border-r border-slate-300">Batch / Lot (Opsional)</th>
                                        <th class="py-3.5 px-4 border-r border-slate-300">Stok Sistem</th>
                                        <th class="py-3.5 px-4 border-r border-slate-300">Stok Fisik</th>
                                        <th class="py-3.5 px-4">Catatan</th>
                                    </tr>
                                </thead>
                                <tbody class="block lg:table-row-group divide-y divide-slate-200 border-y border-slate-200 lg:border-0 text-sm bg-white">
                                    @foreach($barangs as $index => $b)
                                        <tr x-data="{ open: false }"
                                            data-warehouse-id="{{ $b->location->warehouse_id ?? '' }}"
                                            data-location-id="{{ $b->location_id ?? '' }}"
                                            data-search="{{ strtolower($b->nama_barang . ' ' . $b->kode_barang) }}"
                                            class="opname-row flex flex-wrap items-center justify-between gap-x-2 gap-y-2.5 px-4 sm:px-6 py-2.5 bg-white lg:table-row lg:p-0 lg:bg-transparent hover:bg-slate-50 transition-colors">
                                            <!-- 1. Barang / Item -->
                                            <td class="order-1 flex-1 min-w-0 p-0 lg:py-3.5 lg:px-4 lg:border-r lg:border-slate-200">
                                                <input type="hidden" name="items[{{ $index }}][barang_id]" value="{{ $b->id }}">
                                                <span class="font-bold text-slate-800 text-sm block truncate">{{ $b->nama_barang }}</span>
                                                <span class="text-[11px] text-slate-400 block font-mono truncate">
                                                    {{ $b->kode_barang }}<span class="lg:hidden font-sans text-slate-500"> &bull; Sistem: <strong class="text-slate-700">{{ $b->stok }} {{ $b->satuan }}</strong></span>
                                                </span>
                                            </td>

                                            <!-- 2. Batch / Lot (Opsional - Collapse di Mobile) -->
                                            <td :class="{ 'hidden': !open, 'block': open }"
                                                class="hidden lg:table-cell order-3 basis-full pt-2.5 border-t border-slate-100 lg:border-t-0 lg:py-3.5 lg:px-4 lg:border-r lg:border-slate-200">
                                                <label class="block lg:hidden text-[11px] font-semibold text-slate-600 mb-1">Batch / Lot (Opsional)</label>
                                                <select name="items[{{ $index }}][batch_id]" class="h-10 w-full rounded-lg border-slate-300 bg-white text-xs">
                                                    <option value="">Semua Batch (Stok Total)</option>
                                                    @foreach($b->batches as $bt)
                                                        <option value="{{ $bt->id }}">{{ $bt->batch_number }} (Stok: {{ $bt->stok }})</option>
                                                    @endforeach
                                                </select>
                                            </td>

                                            <!-- 3. Stok Sistem (Desktop Only - di Mobile tampil di bawah kode) -->
                                            <td class="hidden lg:table-cell py-3.5 px-4 border-r border-slate-200 font-semibold text-slate-600 whitespace-nowrap">
                                                {{ $b->stok }} {{ $b->satuan }}
                                            </td>

                                            <!-- 4. Stok Fisik (+ Tombol Detail di Mobile) -->
                                            <td class="order-2 shrink-0 flex items-center gap-1.5 p-0 lg:table-cell lg:py-3.5 lg:px-4 lg:border-r lg:border-slate-200">
                                                <input type="number" step="0.01" min="0" name="items[{{ $index }}][stok_fisik]" value="{{ $b->stok }}" required
                                                    aria-label="Stok Fisik {{ $b->nama_barang }}"
                                                    class="h-9 lg:h-10 w-20 sm:w-24 lg:w-32 text-right lg:text-left rounded-lg border-slate-300 bg-white text-sm font-bold text-slate-900 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                                                <button type="button" @click="open = !open"
                                                    class="lg:hidden h-9 w-8 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-slate-50 text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition shrink-0"
                                                    :class="{ 'bg-indigo-50 border-indigo-200 text-indigo-600': open }"
                                                    title="Batch & Catatan">
                                                    <svg class="w-4 h-4 transition-transform duration-150" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </button>
                                            </td>

                                            <!-- 5. Catatan (Opsional - Collapse di Mobile) -->
                                            <td :class="{ 'hidden': !open, 'block': open }"
                                                class="hidden lg:table-cell order-4 basis-full lg:py-3.5 lg:px-4">
                                                <label class="block lg:hidden text-[11px] font-semibold text-slate-600 mb-1">Catatan</label>
                                                <input type="text" name="items[{{ $index }}][catatan]" class="h-10 w-full rounded-lg border-slate-300 bg-white text-xs" placeholder="Catatan kondisi barang...">
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr id="opname-empty" class="hidden">
                                        <td colspan="5" class="py-8 px-4 text-center text-xs text-slate-500 block lg:table-cell">
                                            Tidak ada barang yang cocok dengan filter Gudang / Rak / Pencarian.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                        <a href="{{ route('stock-opnames.index') }}" class="px-4 py-2 text-sm font-semibold text-slate-600 hover:text-slate-900">Batal</a>
                        <button type="submit" id="opname-submit-btn" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition shadow-md shadow-indigo-600/20">
                            Simpan Stock Opname
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const warehouseSelect = document.getElementById('opname-warehouse');
            const locationSelect = document.getElementById('opname-location');
            const searchInput = document.getElementById('opname-search');
            const countBadge = document.getElementById('opname-count');
            const emptyRow = document.getElementById('opname-empty');
            const rows = Array.from(document.querySelectorAll('.opname-row'));
            const locationOptions = locationSelect ? Array.from(locationSelect.querySelectorAll('option[data-warehouse-id]')) : [];

            function syncLocationOptions() {
                if (!warehouseSelect || !locationSelect) return;
                const whId = warehouseSelect.value;
                locationOptions.forEach(opt => {
                    const match = !whId || opt.dataset.warehouseId === whId;
                    opt.hidden = !match;
                    opt.disabled = !match;
                });
                const selectedOpt = locationSelect.options[locationSelect.selectedIndex];
                if (selectedOpt && selectedOpt.disabled) {
                    locationSelect.value = '';
                }
            }

            function applyFilters() {
                const whId = warehouseSelect ? warehouseSelect.value : '';
                const locId = locationSelect ? locationSelect.value : '';
                const query = searchInput ? searchInput.value.trim().toLowerCase() : '';

                let inScopeCount = 0;
                let visibleCount = 0;

                rows.forEach(row => {
                    const rowWh = row.dataset.warehouseId || '';
                    const rowLoc = row.dataset.locationId || '';
                    const rowSearch = row.dataset.search || '';

                    const inScope = (!whId || rowWh === whId) && (!locId || rowLoc === locId);
                    const matchesSearch = !query || rowSearch.includes(query);
                    const isVisible = inScope && matchesSearch;

                    row.style.display = isVisible ? '' : 'none';

                    // Hanya disable input untuk barang di luar filter Gudang/Rak yang dipilih
                    row.querySelectorAll('input, select').forEach(el => {
                        el.disabled = !inScope;
                    });

                    if (inScope) inScopeCount++;
                    if (isVisible) visibleCount++;
                });

                if (countBadge) {
                    if (query && visibleCount !== inScopeCount) {
                        countBadge.textContent = `${visibleCount} dari ${inScopeCount} barang`;
                    } else {
                        countBadge.textContent = `${inScopeCount} barang`;
                    }
                }

                if (emptyRow) {
                    emptyRow.classList.toggle('hidden', visibleCount > 0);
                }
            }

            if (warehouseSelect) {
                warehouseSelect.addEventListener('change', function() {
                    syncLocationOptions();
                    applyFilters();
                });
            }
            if (locationSelect) {
                locationSelect.addEventListener('change', applyFilters);
            }
            if (searchInput) {
                searchInput.addEventListener('input', applyFilters);
            }
        });
    </script>
</x-app-layout>
