<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 text-left">
            <div class="flex items-center space-x-3">
                <div class="p-2.5 bg-gradient-to-tr from-emerald-600 to-indigo-600 rounded-2xl shadow-md text-white shrink-0">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center space-x-2">
                        <h2 class="font-black text-2xl text-slate-800 tracking-tight text-left">
                            Dashboard Operasional WMS
                        </h2>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span> SYSTEM ACTIVE
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5 text-left">Monitoring real-time persediaan barang, mutasi stok, cold storage & audit trail PT INDOFROZ UTAMA.</p>
                </div>
            </div>
            
            <!-- Quick Action Shortcut Buttons -->
            <div class="flex items-center space-x-2 overflow-x-auto no-scrollbar pt-2 lg:pt-0">
                @if(auth()->user()->hasRole(['admin', 'gudang']))
                    <a href="{{ route('barang-masuk.create') }}" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition shadow-md hover:shadow-lg flex items-center space-x-1.5">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>+ Barang Masuk</span>
                    </a>
                    <a href="{{ route('barang-keluar.create') }}" class="px-3.5 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl transition shadow-md hover:shadow-lg flex items-center space-x-1.5">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                        <span>+ Barang Keluar</span>
                    </a>
                @endif
                @if(auth()->user()->hasRole(['admin', 'produksi']))
                    <a href="{{ route('permintaan-barang.create') }}" class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition shadow-md hover:shadow-lg flex items-center space-x-1.5">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <span>+ Material Req.</span>
                    </a>
                @endif
                @if(auth()->user()->hasRole(['admin', 'purchasing']))
                    <a href="{{ route('purchase-orders.create') }}" class="px-3.5 py-2 bg-sky-600 hover:bg-sky-700 text-white text-xs font-bold rounded-xl transition shadow-md hover:shadow-lg flex items-center space-x-1.5">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        <span>+ Purchase Order</span>
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Alert Flash Session -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl shadow-sm flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm font-semibold">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <!-- 1. Top KPI Executive Metrics Grid (5 Cards) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                
                <!-- Card 1: Total Katalog & Total Qty -->
                <div class="bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 text-white p-5 rounded-2xl shadow-md border border-slate-800 relative overflow-hidden group">
                    <div class="absolute -right-3 -bottom-3 opacity-15 text-white group-hover:scale-110 transition-transform">
                        <svg class="h-28 w-28" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-indigo-300">Total Katalog Barang</p>
                    <div class="mt-2 flex items-baseline justify-between">
                        <h3 class="text-3xl font-black font-mono tracking-tight">{{ number_format($totalItems) }} <span class="text-xs font-normal text-indigo-200">SKU</span></h3>
                    </div>
                    <div class="mt-3 flex items-center justify-between text-xs border-t border-slate-800/80 pt-2">
                        <span class="text-slate-400">Total Stok:</span>
                        <span class="font-mono font-bold text-emerald-400 bg-emerald-950/60 border border-emerald-500/30 px-2 py-0.5 rounded-md">{{ number_format($totalStockQty) }} Unit</span>
                    </div>
                </div>

                <!-- Card 2: Stok Menipis Alert -->
                <a href="{{ route('barangs.index', ['status' => 'low_stock']) }}" class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 hover:shadow-md transition-all duration-200 transform hover:-translate-y-0.5 block">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Stok Menipis (Alert)</p>
                            <h3 class="text-3xl font-black font-mono {{ $lowStockCount > 0 ? 'text-rose-600' : 'text-slate-800' }} mt-2">{{ $lowStockCount }} <span class="text-xs font-medium text-slate-400">SKU</span></h3>
                        </div>
                        <div class="p-3 {{ $lowStockCount > 0 ? 'bg-rose-50 text-rose-600 border border-rose-200' : 'bg-emerald-50 text-emerald-600 border border-emerald-200' }} rounded-xl">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3 flex items-center justify-between text-xs border-t border-slate-100 pt-2">
                        <span class="text-slate-500 font-medium">Status Limit:</span>
                        <span class="{{ $lowStockCount > 0 ? 'text-rose-600 font-extrabold' : 'text-emerald-600 font-bold' }}">
                            {{ $lowStockCount > 0 ? '⚠️ Butuh PR Restok' : '✓ Aman' }}
                        </span>
                    </div>
                </a>

                <!-- Card 3: Pergerakan Mutasi Bulan Ini -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Mutasi {{ date('M Y') }}</p>
                    <div class="mt-2 grid grid-cols-2 gap-1.5 text-xs">
                        <div class="bg-emerald-50/80 p-2 rounded-lg border border-emerald-100">
                            <span class="text-[10px] font-bold text-emerald-700 uppercase block">IN (Masuk)</span>
                            <span class="font-bold text-emerald-900 font-mono text-sm">+{{ number_format($monthlyIncomingQty) }}</span>
                        </div>
                        <div class="bg-rose-50/80 p-2 rounded-lg border border-rose-100">
                            <span class="text-[10px] font-bold text-rose-700 uppercase block">OUT (Keluar)</span>
                            <span class="font-bold text-rose-900 font-mono text-sm">-{{ number_format($monthlyOutgoingQty) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Nilai PO Pipeline (Active POs) -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Nilai PO Active Pipeline</p>
                    <h3 class="text-xl font-black font-mono text-sky-700 mt-2 truncate">Rp {{ number_format($activePOValue, 0, ',', '.') }}</h3>
                    <div class="mt-3 flex items-center justify-between text-xs border-t border-slate-100 pt-2">
                        <span class="text-slate-500">PO Dalam Pengiriman:</span>
                        <span class="font-bold text-sky-700 bg-sky-50 px-2 py-0.5 rounded border border-sky-200 font-mono">{{ $activePOs }} PO</span>
                    </div>
                </div>

                <!-- Card 5: Gudang, Rak & Supplier -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Infrastruktur Gudang</p>
                    <div class="mt-2 space-y-1 text-xs">
                        <div class="flex justify-between items-center py-0.5">
                            <span class="text-slate-600">Total Gudang:</span>
                            <span class="font-bold text-slate-800 font-mono">{{ $totalWarehouses }} Unit</span>
                        </div>
                        <div class="flex justify-between items-center py-0.5">
                            <span class="text-slate-600">Lokasi Rak Storage:</span>
                            <span class="font-bold text-slate-800 font-mono">{{ $totalLocations }} Rak</span>
                        </div>
                        <div class="flex justify-between items-center py-0.5 border-t border-slate-100 pt-1">
                            <span class="text-slate-600">Mitra Supplier:</span>
                            <span class="font-bold text-indigo-600 font-mono">{{ $totalSuppliers }} Vendor</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- 2. Charts Section (Inbound vs Outbound Line Chart & Category Donut Chart) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- 6-Month Inbound vs Outbound Trend Chart (2/3 width) -->
                <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="font-extrabold text-slate-800 text-base flex items-center space-x-2">
                                <span class="p-1.5 bg-indigo-50 rounded-lg text-indigo-600 border border-indigo-100">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                                </span>
                                <span>Tren Mutasi Barang Inbound vs Outbound (6 Bulan Terakhir)</span>
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">Perbandingan volume barang masuk (pembelian/penerimaan) vs barang keluar (produksi/distribusi).</p>
                        </div>
                    </div>
                    <div class="h-64 relative">
                        <canvas id="mutationTrendChart"></canvas>
                    </div>
                </div>

                <!-- Category Stock Distribution Chart (1/3 width) -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="font-extrabold text-slate-800 text-base flex items-center space-x-2">
                                <span class="p-1.5 bg-emerald-50 rounded-lg text-emerald-600 border border-emerald-100">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                                </span>
                                <span>Proporsi Kategori Stok</span>
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">Sebaran jumlah stok fisik berdasarkan kategori produk.</p>
                        </div>
                    </div>
                    <div class="h-64 relative flex items-center justify-center">
                        <canvas id="categoryDonutChart"></canvas>
                    </div>
                </div>

            </div>

            <!-- 3. Operational Queue Bar & Fast Moving Items Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Fast Moving Items (Top 5 Outbound Items) (2/3 width) -->
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="font-bold text-slate-800 text-base flex items-center space-x-2">
                                <span class="p-1.5 bg-amber-50 rounded-lg text-amber-600 border border-amber-200">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                </span>
                                <span>Top 5 Barang Pergerakan Cepat (Fast-Moving Items - 30 Hari Terakhir)</span>
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">Item barang dengan tingkat perputaran (outbound volume) tertinggi.</p>
                        </div>
                    </div>

                    @if($fastMovingItems->isEmpty())
                        <div class="p-6 text-center text-slate-400 text-sm bg-slate-50 rounded-xl border border-dashed border-slate-200">
                            Belum ada histori pengeluaran barang dalam 30 hari terakhir.
                        </div>
                    @else
                        <div class="space-y-4">
                            @php
                                $maxOutbound = $fastMovingItems->max('total_keluar') ?: 1;
                            @endphp
                            @foreach($fastMovingItems as $fast)
                                @php
                                    $percent = round(($fast->total_keluar / $maxOutbound) * 100);
                                @endphp
                                <div class="space-y-1">
                                    <div class="flex justify-between items-center text-xs">
                                        <div class="flex items-center space-x-2">
                                            <span class="font-mono font-bold text-indigo-600">{{ $fast->barang->kode_barang ?? '-' }}</span>
                                            <span class="font-bold text-slate-800">{{ $fast->barang->nama_barang ?? 'Barang Deleted' }}</span>
                                        </div>
                                        <span class="font-bold font-mono text-slate-700">{{ number_format($fast->total_keluar) }} {{ $fast->barang->satuan ?? 'Unit' }} Outbound</span>
                                    </div>
                                    <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                                        <div class="bg-gradient-to-r from-indigo-500 to-emerald-500 h-2.5 rounded-full transition-all duration-500" style="width: {{ $percent }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Operational Request Queue Status (1/3 width) -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex flex-col justify-between">
                    <div>
                        <h3 class="font-bold text-slate-800 text-base mb-3 flex items-center space-x-2">
                            <span class="p-1.5 bg-sky-50 rounded-lg text-sky-600 border border-sky-200">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </span>
                            <span>Status Antrean Pengajuan</span>
                        </h3>
                        
                        <div class="space-y-3 text-xs">
                            <a href="{{ route('permintaan-barang.index') }}" class="flex justify-between items-center p-3 bg-slate-50 hover:bg-indigo-50/50 rounded-xl border border-slate-200/80 transition">
                                <div class="flex items-center space-x-2">
                                    <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                                    <span class="text-slate-700 font-semibold">Material Request Produksi</span>
                                </div>
                                <span class="px-2.5 py-1 font-bold rounded-lg {{ $pendingProdRequests > 0 ? 'bg-amber-100 text-amber-800 border border-amber-300' : 'bg-slate-200 text-slate-600' }}">{{ $pendingProdRequests }} Pending</span>
                            </a>

                            <a href="{{ route('permintaan-pembelian.index') }}" class="flex justify-between items-center p-3 bg-slate-50 hover:bg-indigo-50/50 rounded-xl border border-slate-200/80 transition">
                                <div class="flex items-center space-x-2">
                                    <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                                    <span class="text-slate-700 font-semibold">Purchase Requisition (PR)</span>
                                </div>
                                <span class="px-2.5 py-1 font-bold rounded-lg {{ $pendingPRs > 0 ? 'bg-amber-100 text-amber-800 border border-amber-300' : 'bg-slate-200 text-slate-600' }}">{{ $pendingPRs }} Pending</span>
                            </a>

                            <a href="{{ route('purchase-orders.index') }}" class="flex justify-between items-center p-3 bg-slate-50 hover:bg-indigo-50/50 rounded-xl border border-slate-200/80 transition">
                                <div class="flex items-center space-x-2">
                                    <span class="h-2 w-2 rounded-full bg-sky-500"></span>
                                    <span class="text-slate-700 font-semibold">PO Aktif Dalam Pengiriman</span>
                                </div>
                                <span class="px-2.5 py-1 font-bold rounded-lg bg-sky-100 text-sky-800 border border-sky-300 font-mono">{{ $activePOs }} Active</span>
                            </a>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        <span>Stock Opname Terakhir:</span>
                        <span class="font-bold text-slate-700">{{ $lastOpname ? \Carbon\Carbon::parse($lastOpname->created_at)->format('d M Y') : 'Belum Ada' }}</span>
                    </div>
                </div>

            </div>

            <!-- 4. Stock Alerts (FIFO Shelf Life & Low Stock Tables) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- FIFO Shelf Life & Expiry Alert Table -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-slate-800 text-base flex items-center space-x-2">
                            <span class="p-1.5 bg-amber-50 rounded-lg text-amber-600 border border-amber-200">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </span>
                            <span>Peringatan FIFO Shelf Life & Kadaluarsa</span>
                        </h3>
                        <a href="{{ route('barangs.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-900">Lihat Semua &rarr;</a>
                    </div>

                    @if($expiringItems->isEmpty())
                        <div class="p-6 text-center text-slate-400 text-sm bg-slate-50/50 rounded-xl border border-dashed border-slate-200">
                            ✓ Semua batch barang dalam batas kondisi segar (aman).
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 text-[11px] font-bold uppercase text-slate-500 border-b border-slate-200">
                                        <th class="p-3">Barang / Item</th>
                                        <th class="p-3">Gudang</th>
                                        <th class="p-3">Limit Umur</th>
                                        <th class="p-3">Sisa Waktu</th>
                                        <th class="p-3">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-xs">
                                    @foreach($expiringItems as $item)
                                        <tr class="hover:bg-slate-50/80 transition">
                                            <td class="p-3">
                                                <span class="font-bold text-slate-800 block">{{ $item['barang']->nama_barang }}</span>
                                                <span class="font-mono text-[10px] text-slate-400">{{ $item['barang']->kode_barang }}</span>
                                            </td>
                                            <td class="p-3 text-slate-600 font-medium">{{ $item['barang']->lokasi_gudang }}</td>
                                            <td class="p-3 text-slate-600 font-medium">{{ $item['barang']->batas_umur_hari }} Hari</td>
                                            <td class="p-3 font-bold">
                                                @if($item['max_days_left'] < 0)
                                                    <span class="text-rose-600">Lewat {{ abs($item['max_days_left']) }} hari</span>
                                                @else
                                                    <span class="text-amber-600">{{ $item['max_days_left'] }} hari lagi</span>
                                                @endif
                                            </td>
                                            <td class="p-3">
                                                @if($item['status'] === 'expired')
                                                    <span class="px-2 py-0.5 text-[10px] font-extrabold bg-rose-100 text-rose-700 rounded-full border border-rose-300">EXPIRED</span>
                                                @else
                                                    <span class="px-2 py-0.5 text-[10px] font-extrabold bg-amber-100 text-amber-700 rounded-full border border-amber-300">WARNING</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <!-- Low Stock Alert Table -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-slate-800 text-base flex items-center space-x-2">
                            <span class="p-1.5 bg-rose-50 rounded-lg text-rose-600 border border-rose-200">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </span>
                            <span>Peringatan Stok Menipis (Restok Required)</span>
                        </h3>
                    </div>

                    @if($lowStockItems->isEmpty())
                        <div class="p-6 text-center text-slate-400 text-sm bg-slate-50/50 rounded-xl border border-dashed border-slate-200">
                            ✓ Semua stok barang berada di atas ambang batas minimum.
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 text-[11px] font-bold uppercase text-slate-500 border-b border-slate-200">
                                        <th class="p-3">Kode</th>
                                        <th class="p-3">Nama Barang</th>
                                        <th class="p-3">Stok Saat Ini</th>
                                        <th class="p-3">Minimum</th>
                                        <th class="p-3 text-right">Tindakan Restok</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-xs">
                                    @foreach($lowStockItems as $b)
                                        <tr class="hover:bg-slate-50/80 transition">
                                            <td class="p-3 font-mono font-bold text-indigo-600">{{ $b->kode_barang }}</td>
                                            <td class="p-3 font-semibold text-slate-800">{{ $b->nama_barang }}</td>
                                            <td class="p-3 font-bold text-rose-600 font-mono">{{ number_format($b->stok) }} {{ $b->satuan }}</td>
                                            <td class="p-3 font-semibold text-slate-500 font-mono">{{ number_format($b->stok_minimum) }} {{ $b->satuan }}</td>
                                            <td class="p-3 text-right">
                                                @if(auth()->user()->hasRole(['admin', 'gudang', 'purchasing']))
                                                    <a href="{{ route('permintaan-pembelian.create', ['barang_id' => $b->id]) }}" class="px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-[11px] font-bold rounded-lg border border-indigo-200 transition">
                                                        + Buat PR
                                                    </a>
                                                @else
                                                    <span class="text-slate-400">-</span>
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

            <!-- 5. Warehouse Racks Distribution & Interactive Live Feed -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Warehouse & Storage Location Summary (2/3 width) -->
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                    <h3 class="font-bold text-slate-800 text-base mb-4 flex items-center space-x-2">
                        <span class="p-1.5 bg-blue-50 rounded-lg text-blue-600 border border-blue-200">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </span>
                        <span>Distribusi Gudang Storage & Rak Racking System</span>
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($warehousesSummary as $wh)
                            <div class="p-4 bg-slate-50/80 rounded-xl border border-slate-200 space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="font-extrabold text-slate-800 text-sm">{{ $wh->name }}</span>
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-blue-100 text-blue-800 uppercase">{{ str_replace('_', ' ', $wh->type) }}</span>
                                </div>
                                <p class="text-xs text-slate-500">Kode: <span class="font-mono font-bold text-slate-700">{{ $wh->code }}</span> &bull; Total Lokasi Rak: <span class="font-bold text-slate-800">{{ $wh->locations_count }} Rak</span></p>
                                <div class="flex flex-wrap gap-1.5 pt-1">
                                    @foreach($wh->locations as $loc)
                                        <span class="px-2 py-0.5 bg-white border border-slate-200 text-[10px] font-mono font-bold text-slate-700 rounded-md shadow-2xs">{{ $loc->code }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Live Audit Trail Activity Feed (1/3 width) -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-bold text-slate-800 text-sm flex items-center space-x-2">
                                <span class="p-1 bg-indigo-50 rounded text-indigo-600">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </span>
                                <span>Audit Log Aktivitas Live</span>
                            </h3>
                            <a href="{{ route('audit-logs.index') }}" class="text-[11px] font-bold text-indigo-600 hover:text-indigo-900">Selengkapnya &rarr;</a>
                        </div>

                        <div class="space-y-2.5 max-h-[300px] overflow-y-auto no-scrollbar pr-1">
                            @forelse($recentAudits as $log)
                                <div class="p-2.5 bg-slate-50/90 rounded-xl border border-slate-100 text-xs space-y-1">
                                    <div class="flex justify-between items-center">
                                        <span class="font-bold text-indigo-700 text-[11px]">{{ $log->action }}</span>
                                        <span class="text-[10px] text-slate-400 font-mono">{{ $log->created_at->format('H:i:s') }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-slate-600 text-[11px]">
                                        <span class="font-semibold text-slate-800">{{ $log->user->name ?? 'System' }}</span>
                                        <span class="px-1.5 py-0.5 text-[9px] font-bold bg-slate-200/80 text-slate-700 rounded">{{ $log->module }}</span>
                                    </div>
                                </div>
                            @empty
                                <p class="text-xs text-slate-400 py-2">Belum ada catatan aktivitas audit.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-100 text-center">
                        <span class="text-[11px] text-slate-400">Semua aksi dipantau dengan zona waktu WIB (Asia/Jakarta).</span>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- Load Chart.js CDN for Interactive Visualizations -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Data from Controller
            const trendMonths = @json($trendMonths);
            const trendInbound = @json($trendInbound);
            const trendOutbound = @json($trendOutbound);
            const categoryData = @json($categoryBreakdown);

            // 1. Inbound vs Outbound Trend Line Chart
            const ctxTrend = document.getElementById('mutationTrendChart').getContext('2d');
            new Chart(ctxTrend, {
                type: 'line',
                data: {
                    labels: trendMonths,
                    datasets: [
                        {
                            label: 'Barang Masuk (Inbound Qty)',
                            data: trendInbound,
                            borderColor: '#059669', // Emerald 600
                            backgroundColor: 'rgba(5, 150, 105, 0.1)',
                            fill: true,
                            tension: 0.35,
                            borderWidth: 2.5,
                            pointRadius: 4,
                            pointBackgroundColor: '#059669',
                        },
                        {
                            label: 'Barang Keluar (Outbound Qty)',
                            data: trendOutbound,
                            borderColor: '#e11d48', // Rose 600
                            backgroundColor: 'rgba(225, 29, 72, 0.1)',
                            fill: true,
                            tension: 0.35,
                            borderWidth: 2.5,
                            pointRadius: 4,
                            pointBackgroundColor: '#e11d48',
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: { family: 'ui-sans-serif, system-ui', size: 11, weight: 'bold' },
                                usePointStyle: true,
                                boxWidth: 8
                            }
                        },
                        tooltip: {
                            padding: 10,
                            cornerRadius: 8,
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f1f5f9' },
                            ticks: { font: { size: 10 } }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 10 } }
                        }
                    }
                }
            });

            // 2. Category Stock Distribution Donut Chart
            const ctxDonut = document.getElementById('categoryDonutChart').getContext('2d');
            const catNames = categoryData.map(c => c.name);
            const catStok = categoryData.map(c => c.stok);

            new Chart(ctxDonut, {
                type: 'doughnut',
                data: {
                    labels: catNames,
                    datasets: [{
                        data: catStok,
                        backgroundColor: [
                            '#4f46e5', // Indigo
                            '#10b981', // Emerald
                            '#f59e0b', // Amber
                            '#06b6d4', // Cyan
                            '#ec4899', // Pink
                            '#8b5cf6', // Purple
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: { family: 'ui-sans-serif, system-ui', size: 10, weight: 'bold' },
                                usePointStyle: true,
                                boxWidth: 6
                            }
                        }
                    },
                    cutout: '68%'
                }
            });
        });
    </script>
</x-app-layout>

