<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-bold text-xl sm:text-2xl text-slate-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            <span class="text-xs text-slate-500">{{ now()->translatedFormat('d F Y') }}</span>
        </div>
    </x-slot>

    <div class="space-y-5">
        <!-- Ringkasan Utama -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl border border-slate-200 p-4 sm:p-5">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-slate-500">Total Jenis Barang</span>
                    <span class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </span>
                </div>
                <p class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($totalItems) }}</p>
                <p class="text-xs text-slate-500 mt-1.5">Total stok fisik: <span class="font-semibold text-slate-700">{{ number_format($totalStockQty) }}</span></p>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-4 sm:p-5">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-slate-500">Stok Perlu Restock</span>
                    <span class="w-8 h-8 rounded-lg {{ $lowStockCount > 0 ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-emerald-600' }} flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            @if($lowStockCount > 0)
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            @endif
                        </svg>
                    </span>
                </div>
                <p class="text-2xl font-bold {{ $lowStockCount > 0 ? 'text-rose-600' : 'text-slate-900' }} mt-1">{{ number_format($lowStockCount) }}</p>
                <p class="text-xs mt-1.5 {{ $lowStockCount > 0 ? 'text-rose-600 font-medium' : 'text-emerald-600 font-medium' }}">
                    {{ $lowStockCount > 0 ? 'Di bawah batas minimum' : 'Semua stok aman' }}
                </p>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-4 sm:p-5">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-slate-500">Barang Masuk (30 Hari)</span>
                    <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                        </svg>
                    </span>
                </div>
                <p class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($incoming30dQty) }}</p>
                <p class="text-xs text-slate-500 mt-1.5">{{ number_format($incoming30dCount) }} transaksi masuk</p>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-4 sm:p-5">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-slate-500">Barang Keluar (30 Hari)</span>
                    <span class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                        </svg>
                    </span>
                </div>
                <p class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($outgoing30dQty) }}</p>
                <p class="text-xs text-slate-500 mt-1.5">{{ number_format($outgoing30dCount) }} transaksi keluar</p>
            </div>
        </div>

        @if(auth()->user()->hasPermission('reports') || auth()->user()->hasPermission('purchase_orders'))
            <!-- Ringkasan Keuangan & Selisih Persediaan -->
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden" x-data="{ period: 'all' }">
                <div class="px-4 sm:px-5 py-3.5 border-b border-slate-100 flex flex-wrap items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <h3 class="font-semibold text-slate-800 text-sm">Keuangan & Selisih Persediaan</h3>
                        <span class="hidden sm:inline-block px-2 py-0.5 rounded-full text-[11px] font-medium bg-indigo-50 text-indigo-700 border border-indigo-100">
                            Dibayar &minus; Keluar &minus; Rusak/Susut
                        </span>
                    </div>

                    <div class="inline-flex rounded-lg bg-slate-100 p-0.5 text-xs font-medium">
                        <button type="button"
                                @click="period = 'all'"
                                :class="period === 'all' ? 'bg-white text-slate-800 shadow-2xs' : 'text-slate-500 hover:text-slate-700'"
                                class="px-3 py-1 rounded-md transition">
                            Semua Waktu
                        </button>
                        <button type="button"
                                @click="period = 'month'"
                                :class="period === 'month' ? 'bg-white text-slate-800 shadow-2xs' : 'text-slate-500 hover:text-slate-700'"
                                class="px-3 py-1 rounded-md transition">
                            Bulan Ini
                        </button>
                    </div>
                </div>

                @foreach(['all' => $financeAll, 'month' => $financeMonth] as $pKey => $fin)
                    <div class="p-4 sm:p-5 space-y-4" x-show="period === '{{ $pKey }}'" @if($pKey === 'month') x-cloak @endif>
                        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3.5">
                            <!-- 1. Total Dibayar -->
                            <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-4">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-xs font-medium text-slate-500">Total Dibayar (PO)</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">{{ number_format($fin['po_count']) }} PO</span>
                                </div>
                                <p class="text-lg sm:text-xl font-bold text-slate-900 font-mono mt-1.5 break-words">
                                    Rp {{ number_format($fin['total_paid'], 0, ',', '.') }}
                                </p>
                            </div>

                            <!-- 2. Nilai Barang Dikeluarkan -->
                            <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-4">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-xs font-medium text-slate-500">Nilai Dikeluarkan</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">{{ number_format($fin['issued_qty'], 0, ',', '.') }} unit</span>
                                </div>
                                <p class="text-lg sm:text-xl font-bold text-emerald-700 font-mono mt-1.5 break-words">
                                    Rp {{ number_format($fin['issued_value'], 0, ',', '.') }}
                                </p>
                            </div>

                            <!-- 3. Kerugian Rusak / Hilang / Susut -->
                            <div class="rounded-xl border {{ $fin['total_loss_value'] > 0 ? 'border-rose-200 bg-rose-50/40' : 'border-slate-200 bg-slate-50/50' }} p-4">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-xs font-medium {{ $fin['total_loss_value'] > 0 ? 'text-rose-700' : 'text-slate-500' }}">Rusak / Hilang / Susut</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $fin['total_loss_value'] > 0 ? 'bg-rose-100 text-rose-700 border border-rose-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                        {{ number_format($fin['loss_percent'], 2, ',', '.') }}%
                                    </span>
                                </div>
                                <p class="text-lg sm:text-xl font-bold {{ $fin['total_loss_value'] > 0 ? 'text-rose-600' : 'text-slate-900' }} font-mono mt-1.5 break-words">
                                    Rp {{ number_format($fin['total_loss_value'], 0, ',', '.') }}
                                </p>
                                <div class="mt-2 pt-2 border-t border-slate-200/70 flex flex-wrap gap-x-3 gap-y-1 text-[11px] text-slate-500">
                                    <span>Rusak: <strong class="text-slate-700">{{ number_format($fin['loss_adj_qty'], 2, ',', '.') }}</strong></span>
                                    <span>Opname: <strong class="text-slate-700">{{ number_format($fin['loss_opname_qty'], 2, ',', '.') }}</strong></span>
                                    <span>Susut: <strong class="text-slate-700">{{ number_format($fin['loss_susut_qty'], 2, ',', '.') }} Kg</strong></span>
                                </div>
                            </div>

                            <!-- 4. Selisih Bersih -->
                            <div class="rounded-xl border border-indigo-200 bg-indigo-50/40 p-4">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-xs font-semibold text-indigo-700">Selisih Sisa Nilai</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-white text-indigo-700 border border-indigo-200">Netto</span>
                                </div>
                                <p class="text-lg sm:text-xl font-bold {{ $fin['net_variance'] < 0 ? 'text-rose-600' : 'text-indigo-700' }} font-mono mt-1.5 break-words">
                                    Rp {{ number_format($fin['net_variance'], 0, ',', '.') }}
                                </p>
                                <p class="text-[11px] text-slate-500 mt-2 pt-2 border-t border-indigo-100">
                                    Nilai stok fisik saat ini: <strong class="text-slate-800 font-mono">Rp {{ number_format($financeCurrentStockValue, 0, ',', '.') }}</strong>
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Kondisi Stok & Kapasitas Rak -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
            <div class="lg:col-span-7 bg-white rounded-xl border border-slate-200 flex flex-col">
                <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-semibold text-slate-800 text-sm">Kondisi Stok Barang</h3>
                    @if(auth()->user()->hasPermission('barangs'))
                        <a href="{{ route('barangs.index') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-700">
                            Semua Barang &rarr;
                        </a>
                    @endif
                </div>

                <div class="p-5 flex-1">
                    @if($stockOverviewItems->isEmpty())
                        <div class="py-6 text-center text-xs text-slate-500">
                            Belum ada data barang.
                        </div>
                    @else
                        <div class="md:hidden space-y-2.5">
                            @foreach($stockOverviewItems as $b)
                                @php
                                    $isHabis = $b->stok <= 0;
                                    $isMenipis = !$isHabis && $b->stok <= $b->stok_minimum;
                                @endphp
                                <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-200/80 space-y-2 text-xs">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="min-w-0">
                                            @if(auth()->user()->hasPermission('barangs'))
                                                <a href="{{ route('barangs.show', $b->id) }}" class="font-semibold text-slate-800 hover:text-indigo-600 block truncate">
                                                    {{ $b->nama_barang }}
                                                </a>
                                            @else
                                                <span class="font-semibold text-slate-800 block truncate">{{ $b->nama_barang }}</span>
                                            @endif
                                            <div class="text-[11px] text-slate-400 font-mono">{{ $b->kode_barang }}</div>
                                        </div>
                                        @if($isHabis)
                                            <span class="shrink-0 inline-block px-2 py-0.5 rounded-full text-[11px] font-medium bg-rose-50 text-rose-700 border border-rose-200">Habis</span>
                                        @elseif($isMenipis)
                                            <span class="shrink-0 inline-block px-2 py-0.5 rounded-full text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-200">Menipis</span>
                                        @else
                                            <span class="shrink-0 inline-block px-2 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">Aman</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center justify-between pt-1.5 border-t border-slate-200/60 text-[11px]">
                                        <span class="text-slate-500 truncate pr-2">Rak: <strong class="text-slate-700">{{ $b->location->name ?? ($b->lokasi_gudang ?: '-') }}</strong></span>
                                        <span class="shrink-0">
                                            <strong class="{{ $isHabis ? 'text-rose-600' : ($isMenipis ? 'text-amber-600' : 'text-slate-800') }}">{{ number_format($b->stok) }} {{ $b->satuan }}</strong>
                                            <span class="text-slate-400">/ min {{ number_format($b->stok_minimum) }}</span>
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="hidden md:block overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr class="border-b border-slate-200 text-slate-500 font-semibold">
                                        <th class="pb-2.5 pr-3">Barang</th>
                                        <th class="pb-2.5 px-3">Rak</th>
                                        <th class="pb-2.5 px-3 text-right">Stok</th>
                                        <th class="pb-2.5 px-3 text-right">Min.</th>
                                        <th class="pb-2.5 pl-3 text-right">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($stockOverviewItems as $b)
                                        @php
                                            $isHabis = $b->stok <= 0;
                                            $isMenipis = !$isHabis && $b->stok <= $b->stok_minimum;
                                        @endphp
                                        <tr class="hover:bg-slate-50/70">
                                            <td class="py-2.5 pr-3">
                                                @if(auth()->user()->hasPermission('barangs'))
                                                    <a href="{{ route('barangs.show', $b->id) }}" class="font-medium text-slate-800 hover:text-indigo-600">
                                                        {{ $b->nama_barang }}
                                                    </a>
                                                @else
                                                    <span class="font-medium text-slate-800">{{ $b->nama_barang }}</span>
                                                @endif
                                                <div class="text-[11px] text-slate-400">{{ $b->kode_barang }}</div>
                                            </td>
                                            <td class="py-2.5 px-3 text-slate-600">
                                                {{ $b->location->name ?? ($b->lokasi_gudang ?: '-') }}
                                            </td>
                                            <td class="py-2.5 px-3 text-right font-semibold {{ $isHabis ? 'text-rose-600' : ($isMenipis ? 'text-amber-600' : 'text-slate-800') }} whitespace-nowrap">
                                                {{ number_format($b->stok) }} {{ $b->satuan }}
                                            </td>
                                            <td class="py-2.5 px-3 text-right text-slate-500 whitespace-nowrap">
                                                {{ number_format($b->stok_minimum) }} {{ $b->satuan }}
                                            </td>
                                            <td class="py-2.5 pl-3 text-right whitespace-nowrap">
                                                @if($isHabis)
                                                    <span class="inline-block px-2 py-0.5 rounded-full text-[11px] font-medium bg-rose-50 text-rose-700 border border-rose-200">Habis</span>
                                                @elseif($isMenipis)
                                                    <span class="inline-block px-2 py-0.5 rounded-full text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-200">Menipis</span>
                                                @else
                                                    <span class="inline-block px-2 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">Aman</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <div class="lg:col-span-5 bg-white rounded-xl border border-slate-200 p-5 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-slate-800 text-sm">Kapasitas Rak</h3>
                        @if(auth()->user()->hasPermission('locations'))
                            <a href="{{ route('locations.index') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-700">
                                Kelola Rak &rarr;
                            </a>
                        @endif
                    </div>

                    @if($locationsSummary->isEmpty())
                        <div class="py-6 text-center text-xs text-slate-500">
                            Belum ada lokasi rak aktif.
                        </div>
                    @else
                        <div class="space-y-3.5">
                            @foreach($locationsSummary->take(5) as $loc)
                                @php
                                    $pct = $loc->computed_percent;
                                    $barColor = $pct !== null
                                        ? ($pct >= 90 ? 'bg-rose-500' : ($pct >= 75 ? 'bg-amber-500' : 'bg-indigo-600'))
                                        : 'bg-slate-300';
                                @endphp
                                <div>
                                    <div class="flex items-center justify-between gap-2 text-xs mb-1">
                                        <div class="min-w-0 truncate">
                                            <span class="font-medium text-slate-800">{{ $loc->name }}</span>
                                            <span class="text-[11px] text-slate-400 ml-1">{{ $loc->warehouse->name ?? $loc->code }}</span>
                                        </div>
                                        <div class="text-right shrink-0 whitespace-nowrap text-slate-700">
                                            @if($loc->computed_max > 0)
                                                <span class="font-semibold">{{ number_format($loc->computed_used) }} / {{ number_format($loc->computed_max) }} {{ $loc->satuan_label }}</span>
                                                <span class="text-[11px] text-slate-400 ml-1">({{ $pct }}%)</span>
                                            @else
                                                <span class="font-semibold">{{ number_format($loc->computed_used) }} {{ $loc->satuan_label }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full {{ $barColor }}" style="width: {{ $pct !== null ? $pct : 0 }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                    <span>{{ $totalWarehouses }} gudang &bull; {{ $totalLocations }} rak aktif</span>
                    <span>{{ $totalSuppliers }} supplier</span>
                </div>
            </div>
        </div>

        <!-- Grafik & Antrean Dokumen -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
            <div class="lg:col-span-7 bg-white rounded-xl border border-slate-200 p-5">
                <h3 class="font-semibold text-slate-800 text-sm mb-4">Pergerakan Barang (6 Bulan Terakhir)</h3>
                <div class="relative h-60">
                    <canvas id="mutationTrendChart"></canvas>
                </div>
            </div>

            <div class="lg:col-span-5 bg-white rounded-xl border border-slate-200 flex flex-col justify-between">
                <div>
                    <div class="px-5 py-3.5 border-b border-slate-100">
                        <h3 class="font-semibold text-slate-800 text-sm">Antrean Dokumen</h3>
                    </div>

                    <div class="divide-y divide-slate-100 px-5">
                        <a href="{{ route('permintaan-barang.index') }}" class="py-3.5 flex items-center justify-between gap-3 hover:bg-slate-50/70 -mx-5 px-5 transition">
                            <span class="text-xs font-medium text-slate-700">Permintaan Barang Produksi</span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $pendingProdRequests > 0 ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-slate-100 text-slate-600' }}">
                                {{ $pendingProdRequests }}
                            </span>
                        </a>

                        <a href="{{ route('permintaan-barang.index') }}" class="py-3.5 flex items-center justify-between gap-3 hover:bg-slate-50/70 -mx-5 px-5 transition">
                            <span class="text-xs font-medium text-slate-700">Cek QC Defrost</span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $pendingDefrostCount > 0 ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-slate-100 text-slate-600' }}">
                                {{ $pendingDefrostCount }}
                            </span>
                        </a>

                        <a href="{{ route('permintaan-pembelian.index') }}" class="py-3.5 flex items-center justify-between gap-3 hover:bg-slate-50/70 -mx-5 px-5 transition">
                            <span class="text-xs font-medium text-slate-700">Permintaan Pembelian (PR)</span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $pendingPRs > 0 ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-slate-100 text-slate-600' }}">
                                {{ $pendingPRs }}
                            </span>
                        </a>

                        <a href="{{ route('purchase-orders.index') }}" class="py-3.5 flex items-center justify-between gap-3 hover:bg-slate-50/70 -mx-5 px-5 transition">
                            <div>
                                <span class="text-xs font-medium text-slate-700">Purchase Order (PO) Aktif</span>
                                @if($activePOValue > 0)
                                    <span class="text-[11px] text-slate-400 ml-1">&bull; Rp {{ number_format($activePOValue, 0, ',', '.') }}</span>
                                @endif
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $activePOs > 0 ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-slate-100 text-slate-600' }}">
                                {{ $activePOs }}
                            </span>
                        </a>
                    </div>
                </div>

                <div class="px-5 py-3 bg-slate-50/70 border-t border-slate-100 rounded-b-xl flex items-center justify-between text-[11px] text-slate-500">
                    <span>QC Inbound: <strong class="text-slate-700">{{ $qcInboundPass }}/{{ $qcInboundTotal }}</strong> lolos</span>
                    <span>Opname terakhir: <strong class="text-slate-700">{{ $lastOpname ? $lastOpname->created_at->format('d M Y') : '-' }}</strong></span>
                </div>
            </div>
        </div>

        <!-- FEFO & Transaksi Terakhir -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
            <div class="lg:col-span-6 bg-white rounded-xl border border-slate-200 flex flex-col">
                <div class="px-5 py-3.5 border-b border-slate-100">
                    <h3 class="font-semibold text-slate-800 text-sm">Prioritas Pengeluaran (FEFO)</h3>
                </div>

                <div class="p-5 flex-1">
                    @if($fefoExpiringBatches->isEmpty())
                        <div class="py-6 text-center text-xs text-slate-500">
                            Belum ada batch dengan tanggal kadaluarsa.
                        </div>
                    @else
                        <div class="md:hidden space-y-2.5">
                            @foreach($fefoExpiringBatches as $batch)
                                <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-200/80 space-y-1.5 text-xs">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="min-w-0">
                                            <div class="font-semibold text-slate-800 truncate">{{ $batch->barang->nama_barang ?? '-' }}</div>
                                            <div class="text-[11px] text-slate-400 font-mono">{{ $batch->batch_number ?: '-' }} &bull; {{ $batch->location->name ?? ($batch->barang->location->name ?? '-') }}</div>
                                        </div>
                                        <span class="shrink-0 font-semibold text-slate-700">
                                            {{ number_format($batch->sisa_stok) }} {{ $batch->barang->satuan ?? '' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between pt-1.5 border-t border-slate-200/60 text-[11px]">
                                        <span class="text-slate-500">Exp: <strong class="text-slate-700">{{ \Carbon\Carbon::parse($batch->tanggal_kadaluarsa)->format('d M Y') }}</strong></span>
                                        @if($batch->days_left < 0)
                                            <span class="font-semibold text-rose-600">Lewat {{ abs($batch->days_left) }} hari</span>
                                        @elseif($batch->days_left <= 30)
                                            <span class="font-semibold text-amber-600">{{ $batch->days_left }} hari lagi</span>
                                        @else
                                            <span class="text-slate-400">{{ $batch->days_left }} hari lagi</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="hidden md:block overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr class="border-b border-slate-200 text-slate-500 font-semibold">
                                        <th class="pb-2.5 pr-3">Barang & Batch</th>
                                        <th class="pb-2.5 px-3">Rak</th>
                                        <th class="pb-2.5 px-3 text-right">Sisa</th>
                                        <th class="pb-2.5 pl-3 text-right">Expired</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($fefoExpiringBatches as $batch)
                                        <tr class="hover:bg-slate-50/70">
                                            <td class="py-2.5 pr-3">
                                                <div class="font-medium text-slate-800">{{ $batch->barang->nama_barang ?? '-' }}</div>
                                                <div class="text-[11px] text-slate-400">{{ $batch->batch_number ?: '-' }}</div>
                                            </td>
                                            <td class="py-2.5 px-3 text-slate-600">
                                                {{ $batch->location->name ?? ($batch->barang->location->name ?? '-') }}
                                            </td>
                                            <td class="py-2.5 px-3 text-right font-semibold text-slate-700 whitespace-nowrap">
                                                {{ number_format($batch->sisa_stok) }} {{ $batch->barang->satuan ?? '' }}
                                            </td>
                                            <td class="py-2.5 pl-3 text-right whitespace-nowrap">
                                                <div class="text-slate-700 font-medium">
                                                    {{ \Carbon\Carbon::parse($batch->tanggal_kadaluarsa)->format('d M Y') }}
                                                </div>
                                                @if($batch->days_left < 0)
                                                    <span class="text-[10px] font-semibold text-rose-600">Lewat {{ abs($batch->days_left) }} hari</span>
                                                @elseif($batch->days_left <= 30)
                                                    <span class="text-[10px] font-semibold text-amber-600">{{ $batch->days_left }} hari lagi</span>
                                                @else
                                                    <span class="text-[10px] text-slate-400">{{ $batch->days_left }} hari lagi</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <div class="lg:col-span-6 bg-white rounded-xl border border-slate-200 flex flex-col" x-data="{ tab: 'masuk' }">
                <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between gap-2">
                    <h3 class="font-semibold text-slate-800 text-sm">Transaksi Terakhir</h3>

                    <div class="inline-flex rounded-lg bg-slate-100 p-0.5 text-xs font-medium">
                        <button type="button"
                                @click="tab = 'masuk'"
                                :class="tab === 'masuk' ? 'bg-white text-slate-800 shadow-2xs' : 'text-slate-500 hover:text-slate-700'"
                                class="px-3 py-1 rounded-md transition">
                            Masuk
                        </button>
                        <button type="button"
                                @click="tab = 'keluar'"
                                :class="tab === 'keluar' ? 'bg-white text-slate-800 shadow-2xs' : 'text-slate-500 hover:text-slate-700'"
                                class="px-3 py-1 rounded-md transition">
                            Keluar
                        </button>
                    </div>
                </div>

                <div class="p-5 flex-1 flex flex-col justify-between" x-show="tab === 'masuk'">
                    @if($recentIncoming->isEmpty())
                        <div class="py-6 text-center text-xs text-slate-500">
                            Belum ada transaksi barang masuk.
                        </div>
                    @else
                        <div class="md:hidden space-y-2.5">
                            @foreach($recentIncoming->take(5) as $in)
                                <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-200/80 space-y-1.5 text-xs">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="min-w-0">
                                            <div class="font-semibold text-slate-800 truncate">{{ $in->barang->nama_barang ?? $in->snapshot_nama_barang ?? '-' }}</div>
                                            <div class="text-[11px] text-slate-400 font-mono">{{ $in->no_masuk ?: '-' }}</div>
                                        </div>
                                        <span class="shrink-0 font-semibold text-emerald-600">+{{ number_format($in->jumlah) }} {{ $in->barang->satuan ?? '' }}</span>
                                    </div>
                                    <div class="flex items-center justify-between pt-1.5 border-t border-slate-200/60 text-[11px] text-slate-500">
                                        <span class="truncate pr-2">{{ $in->purchaseOrder->supplier->nama_supplier ?? 'Manual' }}</span>
                                        <span class="shrink-0">{{ $in->tanggal ? $in->tanggal->format('d M Y') : '-' }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="hidden md:block overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr class="border-b border-slate-200 text-slate-500 font-semibold">
                                        <th class="pb-2.5 pr-3">Barang</th>
                                        <th class="pb-2.5 px-3">Sumber</th>
                                        <th class="pb-2.5 px-3 text-right">Jumlah</th>
                                        <th class="pb-2.5 pl-3 text-right">Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($recentIncoming->take(5) as $in)
                                        <tr class="hover:bg-slate-50/70">
                                            <td class="py-2.5 pr-3">
                                                <div class="font-medium text-slate-800">{{ $in->barang->nama_barang ?? $in->snapshot_nama_barang ?? '-' }}</div>
                                                <div class="text-[11px] text-slate-400">{{ $in->no_masuk ?: '-' }}</div>
                                            </td>
                                            <td class="py-2.5 px-3 text-slate-600">
                                                {{ $in->purchaseOrder->supplier->nama_supplier ?? 'Manual' }}
                                            </td>
                                            <td class="py-2.5 px-3 text-right font-semibold text-emerald-600 whitespace-nowrap">
                                                {{ number_format($in->jumlah) }} {{ $in->barang->satuan ?? '' }}
                                            </td>
                                            <td class="py-2.5 pl-3 text-right text-slate-500 whitespace-nowrap">
                                                {{ $in->tanggal ? $in->tanggal->format('d M Y') : '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 text-right">
                            <a href="{{ route('barang-masuk.index') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-700">
                                Semua Barang Masuk &rarr;
                            </a>
                        </div>
                    @endif
                </div>

                <div class="p-5 flex-1 flex flex-col justify-between" x-show="tab === 'keluar'" x-cloak>
                    @if($recentOutgoing->isEmpty())
                        <div class="py-6 text-center text-xs text-slate-500">
                            Belum ada transaksi barang keluar.
                        </div>
                    @else
                        <div class="md:hidden space-y-2.5">
                            @foreach($recentOutgoing->take(5) as $out)
                                <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-200/80 space-y-1.5 text-xs">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="min-w-0">
                                            <div class="font-semibold text-slate-800 truncate">{{ $out->barang->nama_barang ?? $out->snapshot_nama_barang ?? '-' }}</div>
                                            <div class="text-[11px] text-slate-400">{{ $out->user->name ?? '-' }}</div>
                                        </div>
                                        <span class="shrink-0 font-semibold text-slate-800">-{{ number_format($out->jumlah) }} {{ $out->barang->satuan ?? '' }}</span>
                                    </div>
                                    <div class="flex items-center justify-between pt-1.5 border-t border-slate-200/60 text-[11px] text-slate-500">
                                        <span class="truncate pr-2 font-mono">{{ $out->permintaanBarang->no_permintaan ?? 'Manual' }}</span>
                                        <span class="shrink-0">{{ $out->tanggal ? $out->tanggal->format('d M Y') : '-' }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="hidden md:block overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr class="border-b border-slate-200 text-slate-500 font-semibold">
                                        <th class="pb-2.5 pr-3">Barang</th>
                                        <th class="pb-2.5 px-3">Referensi</th>
                                        <th class="pb-2.5 px-3 text-right">Jumlah</th>
                                        <th class="pb-2.5 pl-3 text-right">Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($recentOutgoing->take(5) as $out)
                                        <tr class="hover:bg-slate-50/70">
                                            <td class="py-2.5 pr-3">
                                                <div class="font-medium text-slate-800">{{ $out->barang->nama_barang ?? $out->snapshot_nama_barang ?? '-' }}</div>
                                                <div class="text-[11px] text-slate-400">{{ $out->user->name ?? '-' }}</div>
                                            </td>
                                            <td class="py-2.5 px-3 text-slate-600">
                                                {{ $out->permintaanBarang->no_permintaan ?? 'Manual' }}
                                            </td>
                                            <td class="py-2.5 px-3 text-right font-semibold text-slate-800 whitespace-nowrap">
                                                {{ number_format($out->jumlah) }} {{ $out->barang->satuan ?? '' }}
                                            </td>
                                            <td class="py-2.5 pl-3 text-right text-slate-500 whitespace-nowrap">
                                                {{ $out->tanggal ? $out->tanggal->format('d M Y') : '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 text-right">
                            <a href="{{ route('barang-keluar.index') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-700">
                                Semua Barang Keluar &rarr;
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chartEl = document.getElementById('mutationTrendChart');
            if (!chartEl) return;

            const trendMonths = @json($trendMonths);
            const trendInbound = @json($trendInbound);
            const trendOutbound = @json($trendOutbound);

            Chart.defaults.color = '#64748b';

            new Chart(chartEl.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: trendMonths,
                    datasets: [
                        {
                            label: 'Barang Masuk',
                            data: trendInbound,
                            backgroundColor: '#4f46e5',
                            borderRadius: 4,
                            barPercentage: 0.7,
                            categoryPercentage: 0.65,
                        },
                        {
                            label: 'Barang Keluar',
                            data: trendOutbound,
                            backgroundColor: '#cbd5e1',
                            borderRadius: 4,
                            barPercentage: 0.7,
                            categoryPercentage: 0.65,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            align: 'end',
                            labels: {
                                font: { size: 11 },
                                usePointStyle: true,
                                pointStyle: 'circle',
                                boxWidth: 7,
                                padding: 14
                            }
                        },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            titleFont: { size: 12, weight: '600' },
                            bodyFont: { size: 11 },
                            padding: 10,
                            cornerRadius: 6
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f1f5f9' },
                            border: { display: false },
                            ticks: { font: { size: 11 } }
                        },
                        x: {
                            grid: { display: false },
                            border: { display: false },
                            ticks: { font: { size: 11 } }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>
