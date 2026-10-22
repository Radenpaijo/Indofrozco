<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-800 tracking-tight">
            Dashboard Operasional WMS
        </h2>
    </x-slot>

    <div class="py-6 space-y-6">
        <div class="w-full space-y-6">


            <!-- 1. Strategic KPI Cards (5 Uniform Cards) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                
                <!-- Card 1: Katalog Barang -->
                <x-card class="p-4 flex flex-col justify-between h-full space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Katalog Barang</span>
                        <span class="p-1 bg-slate-100 text-slate-700 rounded border border-slate-300">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </span>
                    </div>
                    <div>
                        <h3 class="text-2xl font-extrabold text-slate-900 font-mono">{{ number_format($totalItems) }} <span class="text-xs font-semibold text-slate-500 font-sans">SKU</span></h3>
                    </div>
                    <div class="pt-2 border-t border-slate-200 flex items-center justify-between text-xs">
                        <span class="text-slate-500">Total Stok:</span>
                        <span class="font-bold text-slate-800 font-mono">{{ number_format($totalStockQty) }} Unit</span>
                    </div>
                </x-card>

                <!-- Card 2: Stok Menipis Alert -->
                <a href="{{ route('barangs.index', ['status' => 'low_stock']) }}" class="block">
                    <x-card class="p-4 hover:border-slate-400 transition flex flex-col justify-between h-full space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Stok Menipis</span>
                            <span class="p-1 bg-slate-100 text-slate-700 border border-slate-300 rounded">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </span>
                        </div>
                        <div>
                            <h3 class="text-2xl font-extrabold text-slate-900 font-mono">{{ $lowStockCount }} <span class="text-xs font-semibold text-slate-500 font-sans">SKU</span></h3>
                        </div>
                        <div class="pt-2 border-t border-slate-200 flex items-center justify-between text-xs">
                            <span class="text-slate-500">Status Stok:</span>
                            <x-badge variant="{{ $lowStockCount > 0 ? 'outline' : 'secondary' }}">
                                {{ $lowStockCount > 0 ? 'Restok Required' : 'Stok Aman' }}
                            </x-badge>
                        </div>
                    </x-card>
                </a>

                <!-- Card 3: Pergerakan Mutasi Bulan Ini -->
                <x-card class="p-4 flex flex-col justify-between h-full space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Mutasi {{ date('M Y') }}</span>
                        <span class="p-1 bg-slate-100 text-slate-700 rounded border border-slate-300">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div class="bg-slate-100 p-1.5 rounded border border-slate-300 text-center">
                            <span class="text-[10px] font-bold text-slate-600 block">MASUK</span>
                            <span class="font-bold text-slate-900 font-mono">+{{ number_format($monthlyIncomingQty) }}</span>
                        </div>
                        <div class="bg-slate-100 p-1.5 rounded border border-slate-300 text-center">
                            <span class="text-[10px] font-bold text-slate-600 block">KELUAR</span>
                            <span class="font-bold text-slate-900 font-mono">-{{ number_format($monthlyOutgoingQty) }}</span>
                        </div>
                    </div>
                    <div class="pt-2 border-t border-slate-200 flex items-center justify-between text-xs">
                        <span class="text-slate-500">Total Transaksi:</span>
                        <span class="font-bold text-slate-800 font-mono">{{ number_format($monthlyIncomingQty + $monthlyOutgoingQty) }} Unit</span>
                    </div>
                </x-card>

                <!-- Card 4: Nilai PO Active Pipeline -->
                <x-card class="p-4 flex flex-col justify-between h-full space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Nilai PO Active</span>
                        <span class="p-1 bg-slate-100 text-slate-700 rounded border border-slate-300">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                    </div>
                    <div>
                        <h3 class="text-lg font-extrabold text-slate-900 font-mono truncate">Rp {{ number_format($activePOValue, 0, ',', '.') }}</h3>
                    </div>
                    <div class="pt-2 border-t border-slate-200 flex items-center justify-between text-xs">
                        <span class="text-slate-500">PO Dikirim:</span>
                        <span class="font-bold text-slate-800 font-mono">{{ $activePOs }} PO</span>
                    </div>
                </x-card>

                <!-- Card 5: Gudang & Infrastructure -->
                <x-card class="p-4 flex flex-col justify-between h-full space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Infrastruktur</span>
                        <span class="p-1 bg-slate-100 text-slate-700 rounded border border-slate-300">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </span>
                    </div>
                    <div class="space-y-1 text-xs">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500">Gudang Aktif:</span>
                            <span class="font-bold text-slate-800 font-mono">{{ $totalWarehouses }} Unit</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500">Lokasi Rak Storage:</span>
                            <span class="font-bold text-slate-800 font-mono">{{ $totalLocations }} Rak</span>
                        </div>
                    </div>
                    <div class="pt-2 border-t border-slate-200 flex items-center justify-between text-xs">
                        <span class="text-slate-500">Mitra Vendor:</span>
                        <span class="font-bold text-slate-800 font-mono">{{ $totalSuppliers }} Supplier</span>
                    </div>
                </x-card>

            </div>

            <!-- 2. Chart Section (Standard Clean Lines & Donut) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Line Chart -->
                <x-card class="lg:col-span-2 p-5">
                    <div class="mb-4 pb-3 border-b border-slate-200">
                        <h3 class="font-bold text-slate-900 text-sm">Tren Mutasi Inbound vs Outbound (6 Bulan Terakhir)</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Perbandingan volume barang masuk (pembelian) dan barang keluar (produksi/distribusi).</p>
                    </div>
                    <div class="h-64 relative">
                        <canvas id="mutationTrendChart"></canvas>
                    </div>
                </x-card>

                <!-- Donut Chart -->
                <x-card class="p-5 flex flex-col justify-between">
                    <div>
                        <div class="mb-4 pb-3 border-b border-slate-200">
                            <h3 class="font-bold text-slate-900 text-sm">Proporsi Kategori Stok</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Persentase jumlah fisik barang per kategori.</p>
                        </div>
                    </div>
                    <div class="h-56 relative flex items-center justify-center my-auto">
                        <canvas id="categoryDonutChart"></canvas>
                    </div>
                </x-card>

            </div>

            <!-- 3. Operational Queue & Fast Moving Items -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Fast Moving Items -->
                <x-card class="lg:col-span-2 p-5">
                    <div class="mb-4 pb-3 border-b border-slate-200">
                        <h3 class="font-bold text-slate-900 text-sm">Top 5 Barang Pergerakan Cepat (30 Hari Terakhir)</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Item barang dengan volume pengeluaran paling tinggi.</p>
                    </div>

                    @if($fastMovingItems->isEmpty())
                        <div class="p-6 text-center text-slate-400 text-xs bg-slate-50 rounded-lg border border-slate-200">
                            Belum ada histori pengeluaran barang 30 hari terakhir.
                        </div>
                    @else
                        <div class="space-y-3">
                            @php
                                $maxOutbound = $fastMovingItems->max('total_keluar') ?: 1;
                            @endphp
                            @foreach($fastMovingItems as $index => $fast)
                                @php
                                    $percent = round(($fast->total_keluar / $maxOutbound) * 100);
                                @endphp
                                <div class="p-3 bg-slate-50 rounded-lg border border-slate-200 space-y-1.5">
                                    <div class="flex justify-between items-center text-xs">
                                        <div>
                                            <span class="font-bold text-slate-500 mr-2">#{{ $index + 1 }}</span>
                                            <span class="font-mono font-bold text-slate-900 mr-2">{{ $fast->barang->kode_barang ?? '-' }}</span>
                                            <span class="font-semibold text-slate-800">{{ $fast->barang->nama_barang ?? 'Item' }}</span>
                                        </div>
                                        <span class="font-bold font-mono text-slate-800 bg-white px-2.5 py-0.5 rounded border border-slate-300">
                                            {{ number_format($fast->total_keluar) }} {{ $fast->barang->satuan ?? 'Unit' }}
                                        </span>
                                    </div>
                                    <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden border border-slate-300/50">
                                        <div class="bg-slate-900 h-2 rounded-full" style="width: {{ $percent }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </x-card>

                <!-- Antrean Pengajuan -->
                <x-card class="p-5 flex flex-col justify-between">
                    <div>
                        <div class="mb-4 pb-3 border-b border-slate-200">
                            <h3 class="font-bold text-slate-900 text-sm">Status Antrean Pengajuan</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Status dokumen yang menunggu verifikasi.</p>
                        </div>
                        
                        <div class="space-y-2.5 text-xs">
                            <a href="{{ route('permintaan-barang.index') }}" class="flex justify-between items-center p-3 bg-slate-50 hover:bg-slate-100 rounded-lg border border-slate-200 transition">
                                <span class="text-slate-800 font-semibold">Material Request Produksi</span>
                                <x-badge variant="secondary">{{ $pendingProdRequests }} Pending</x-badge>
                            </a>

                            <a href="{{ route('permintaan-pembelian.index') }}" class="flex justify-between items-center p-3 bg-slate-50 hover:bg-slate-100 rounded-lg border border-slate-200 transition">
                                <span class="text-slate-800 font-semibold">Purchase Requisition (PR)</span>
                                <x-badge variant="secondary">{{ $pendingPRs }} Pending</x-badge>
                            </a>

                            <a href="{{ route('purchase-orders.index') }}" class="flex justify-between items-center p-3 bg-slate-50 hover:bg-slate-100 rounded-lg border border-slate-200 transition">
                                <span class="text-slate-800 font-semibold">PO Aktif (Dikirim)</span>
                                <x-badge variant="outline">{{ $activePOs }} Active</x-badge>
                            </a>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-200 flex items-center justify-between text-xs text-slate-500">
                        <span>Stock Opname Terakhir:</span>
                        <span class="font-bold text-slate-800">{{ $lastOpname ? \Carbon\Carbon::parse($lastOpname->created_at)->format('d M Y') : 'Belum Ada' }}</span>
                    </div>
                </x-card>

            </div>

            <!-- 4. Tables Section (FIFO & Low Stock Alert) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Table FIFO Shelf Life Expiry -->
                <x-card class="p-5">
                    <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-200">
                        <h3 class="font-bold text-slate-900 text-sm">Peringatan FIFO Shelf Life & Expired</h3>
                        <a href="{{ route('barangs.index') }}" class="text-xs font-bold text-slate-700 hover:text-slate-900">Lihat Semua &rarr;</a>
                    </div>

                    @if($expiringItems->isEmpty())
                        <div class="p-6 text-center text-slate-400 text-xs bg-slate-50 rounded-lg border border-slate-200">
                            ✓ Semua batch barang dalam batas kondisi aman.
                        </div>
                    @else
                        <x-table>
                            <x-table.header>
                                <x-table.row>
                                    <x-table.head>Barang</x-table.head>
                                    <x-table.head>Gudang</x-table.head>
                                    <x-table.head>Sisa Waktu</x-table.head>
                                    <x-table.head class="text-right">Status</x-table.head>
                                </x-table.row>
                            </x-table.header>
                            <x-table.body>
                                @foreach($expiringItems as $item)
                                    <x-table.row>
                                        <x-table.cell>
                                            <span class="font-bold text-slate-800 block">{{ $item['barang']->nama_barang }}</span>
                                            <span class="font-mono text-[10px] text-slate-500">{{ $item['barang']->kode_barang }}</span>
                                        </x-table.cell>
                                        <x-table.cell class="text-slate-600 font-medium">{{ $item['barang']->lokasi_gudang }}</x-table.cell>
                                        <x-table.cell class="font-bold">
                                            @if($item['max_days_left'] < 0)
                                                <span class="text-slate-800 font-mono">Lewat {{ abs($item['max_days_left']) }} hari</span>
                                            @else
                                                <span class="text-slate-700 font-mono">{{ $item['max_days_left'] }} hari lagi</span>
                                            @endif
                                        </x-table.cell>
                                        <x-table.cell class="text-right">
                                            @if($item['status'] === 'expired')
                                                <x-badge variant="destructive">EXPIRED</x-badge>
                                            @else
                                                <x-badge variant="outline">WARNING</x-badge>
                                            @endif
                                        </x-table.cell>
                                    </x-table.row>
                                @endforeach
                            </x-table.body>
                        </x-table>
                    @endif
                </x-card>

                <!-- Table Low Stock Alert -->
                <x-card class="p-5">
                    <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-200">
                        <h3 class="font-bold text-slate-900 text-sm">Peringatan Stok Menipis</h3>
                    </div>

                    @if($lowStockItems->isEmpty())
                        <div class="p-6 text-center text-slate-400 text-xs bg-slate-50 rounded-lg border border-slate-200">
                            ✓ Semua stok barang di atas batas minimum.
                        </div>
                    @else
                        <x-table>
                            <x-table.header>
                                <x-table.row>
                                    <x-table.head>Kode & Nama</x-table.head>
                                    <x-table.head>Stok Saat Ini</x-table.head>
                                    <x-table.head>Minimum</x-table.head>
                                    <x-table.head class="text-right">Aksi</x-table.head>
                                </x-table.row>
                            </x-table.header>
                            <x-table.body>
                                @foreach($lowStockItems as $b)
                                    <x-table.row>
                                        <x-table.cell>
                                            <span class="font-mono font-bold text-slate-900 text-[11px] block">{{ $b->kode_barang }}</span>
                                            <span class="font-semibold text-slate-800">{{ $b->nama_barang }}</span>
                                        </x-table.cell>
                                        <x-table.cell class="font-bold text-slate-900 font-mono">{{ number_format($b->stok) }} {{ $b->satuan }}</x-table.cell>
                                        <x-table.cell class="text-slate-600 font-mono font-medium">{{ number_format($b->stok_minimum) }} {{ $b->satuan }}</x-table.cell>
                                        <x-table.cell class="text-right">
                                            @if(auth()->user()->hasRole(['admin', 'gudang', 'purchasing']))
                                                <a href="{{ route('permintaan-pembelian.create', ['barang_id' => $b->id]) }}">
                                                    <x-button variant="outline" size="sm">+ PR</x-button>
                                                </a>
                                            @else
                                                <span class="text-slate-400">-</span>
                                            @endif
                                        </x-table.cell>
                                    </x-table.row>
                                @endforeach
                            </x-table.body>
                        </x-table>
                    @endif
                </x-card>

            </div>

            <!-- 5. Warehouse Racks Distribution & Live Audit Trail -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Gudang & Rak -->
                <x-card class="lg:col-span-2 p-5">
                    <h3 class="font-bold text-slate-900 text-sm mb-4 pb-3 border-b border-slate-200">
                        Distribusi Gudang & Rak Storage
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($warehousesSummary as $wh)
                            <div class="p-3.5 bg-slate-50 rounded-lg border border-slate-300 space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-slate-900 text-xs">{{ $wh->name }}</span>
                                    <x-badge variant="secondary">
                                        {{ str_replace('_', ' ', $wh->type) }}
                                    </x-badge>
                                </div>
                                <div class="flex items-center justify-between text-xs text-slate-600">
                                    <span>Kode: <strong class="font-mono text-slate-800">{{ $wh->code }}</strong></span>
                                    <span>Rak: <strong class="font-bold text-slate-800">{{ $wh->locations_count }} Rak</strong></span>
                                </div>
                                <div class="flex flex-wrap gap-1 pt-1">
                                    @foreach($wh->locations as $loc)
                                        <span class="px-2 py-0.5 bg-white border border-slate-300 text-[10px] font-mono font-bold text-slate-800 rounded">
                                            {{ $loc->code }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-card>

                <!-- Audit Log Aktivitas Feed -->
                <x-card class="p-5 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-200">
                            <h3 class="font-bold text-slate-900 text-sm">Audit Log Aktivitas</h3>
                            <a href="{{ route('audit-logs.index') }}" class="text-[11px] font-bold text-slate-700 hover:text-slate-900">Semua &rarr;</a>
                        </div>

                        <div class="space-y-2 max-h-[300px] overflow-y-auto no-scrollbar pr-1">
                            @forelse($recentAudits as $log)
                                <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-200 text-xs space-y-1">
                                    <div class="flex justify-between items-center">
                                        <span class="font-bold text-slate-900 text-[11px] uppercase">{{ $log->action }}</span>
                                        <span class="text-[10px] text-slate-400 font-mono font-semibold">{{ $log->created_at->format('H:i:s') }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-slate-600 text-[11px]">
                                        <span class="font-bold text-slate-800">{{ $log->user->name ?? 'System' }}</span>
                                        <x-badge variant="outline" class="text-[9px] px-1.5 py-0.2">{{ $log->module }}</x-badge>
                                    </div>
                                </div>
                            @empty
                                <p class="text-xs text-slate-400 py-4 text-center">Belum ada catatan audit log.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-200 text-center text-[10px] text-slate-400 font-medium">
                        Waktu sistem WIB (Asia/Jakarta)
                    </div>
                </x-card>

            </div>

        </div>
    </div>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const trendMonths = @json($trendMonths);
            const trendInbound = @json($trendInbound);
            const trendOutbound = @json($trendOutbound);
            const categoryData = @json($categoryBreakdown);

            // Line Chart (Shadcn Lyra Slate Palette)
            const ctxTrend = document.getElementById('mutationTrendChart').getContext('2d');
            new Chart(ctxTrend, {
                type: 'line',
                data: {
                    labels: trendMonths,
                    datasets: [
                        {
                            label: 'Barang Masuk (Inbound)',
                            data: trendInbound,
                            borderColor: '#0f172a', // Slate 900
                            backgroundColor: 'rgba(15, 23, 42, 0.04)',
                            fill: true,
                            tension: 0.2,
                            borderWidth: 2,
                            pointRadius: 3,
                            pointBackgroundColor: '#0f172a',
                        },
                        {
                            label: 'Barang Keluar (Outbound)',
                            data: trendOutbound,
                            borderColor: '#64748b', // Slate 500
                            backgroundColor: 'rgba(100, 116, 139, 0.04)',
                            fill: true,
                            tension: 0.2,
                            borderWidth: 2,
                            pointRadius: 3,
                            pointBackgroundColor: '#64748b',
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
                                font: { family: 'ui-sans-serif, system-ui', size: 11, weight: 'bold' },
                                usePointStyle: true,
                                boxWidth: 6
                            }
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

            // Donut Chart (Shadcn Lyra Slate Monochrome Palette)
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
                            '#0f172a',
                            '#334155',
                            '#475569',
                            '#64748b',
                            '#94a3b8',
                            '#cbd5e1',
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
                    cutout: '65%'
                }
            });
        });
    </script>
</x-app-layout>
