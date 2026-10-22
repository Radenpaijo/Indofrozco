<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-4 min-w-0">
                <x-back-button />
                <div class="min-w-0">
                    <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                        {{ __('Laporan ERP PT INDOFROZ') }}
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">Laporan stok, mutasi, kartu stok, dan histori log transaksi.</p>
                </div>
            </div>
            <div class="flex items-center space-x-3 shrink-0">
                <!-- Export CSV Button -->
                <a href="{{ route('reports.export', ['type' => $reportType, 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="inline-flex items-center px-4 py-2.5 border border-slate-200 rounded-xl text-xs font-semibold bg-white text-slate-700 hover:bg-slate-50 transition-colors shadow-sm">
                    <svg class="h-4 w-4 mr-2 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export CSV
                </a>
                <!-- Print Button -->
                <a href="{{ route('reports.print', ['type' => $reportType, 'start_date' => $startDate, 'end_date' => $endDate]) }}" target="_blank" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-md transition-all">
                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak PDF / Print
                </a>
            </div>
        </div>
    </x-slot>

    <div class="w-full space-y-6">
        <!-- Filter Section -->
        <x-card>
            <x-card.content class="p-6">
                <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="space-y-1">
                        <x-label for="type">Jenis Laporan</x-label>
                        <x-select name="type" id="type">
                            <option value="gudang" {{ $reportType === 'gudang' ? 'selected' : '' }}>Laporan Gudang (Mutasi Stok)</option>
                            <option value="pembelian" {{ $reportType === 'pembelian' ? 'selected' : '' }}>Laporan Pembelian (PO)</option>
                            <option value="produksi" {{ $reportType === 'produksi' ? 'selected' : '' }}>Laporan Produksi (Requisition)</option>
                        </x-select>
                    </div>
                    <div class="space-y-1">
                        <x-label for="start_date">Tanggal Mulai</x-label>
                        <x-input type="date" name="start_date" id="start_date" value="{{ $startDate }}" />
                    </div>
                    <div class="space-y-1">
                        <x-label for="end_date">Tanggal Akhir</x-label>
                        <x-input type="date" name="end_date" id="end_date" value="{{ $endDate }}" />
                    </div>
                    <div class="flex items-end">
                        <x-button type="submit" variant="default" class="w-full">
                            Filter & Sinkronkan
                        </x-button>
                    </div>
                </form>
            </x-card.content>
        </x-card>

        <!-- Report Output Card -->
        <x-card class="p-6">
            <x-card.header class="p-0 pb-4">
                <x-card.title>
                    @if($reportType === 'gudang')
                        Laporan Mutasi Barang Gudang
                    @elseif($reportType === 'pembelian')
                        Laporan Transaksi Purchase Order (PO)
                    @else
                        Laporan Permintaan Barang Produksi
                    @endif
                </x-card.title>
                <x-card.description>
                    Periode: {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}
                </x-card.description>
            </x-card.header>

            <x-card.content class="p-0 border-t border-slate-200 pt-4">
                @if($reportType === 'gudang')
                    <!-- GUDANG TABLE -->
                    <x-table>
                        <x-table.header>
                            <x-table.row>
                                <x-table.head>Tanggal</x-table.head>
                                <x-table.head>Jenis</x-table.head>
                                <x-table.head>No. Transaksi</x-table.head>
                                <x-table.head>Kode</x-table.head>
                                <x-table.head>Barang</x-table.head>
                                <x-table.head>Jumlah</x-table.head>
                                <x-table.head>Ref/Surat Jalan</x-table.head>
                                <x-table.head>Operator</x-table.head>
                            </x-table.row>
                        </x-table.header>
                        <x-table.body>
                            @php $merged = collect(); @endphp
                            @foreach($reportData['masuk'] as $m)
                                @php $m->jenis = 'MASUK'; $merged->push($m); @endphp
                            @endforeach
                            @foreach($reportData['keluar'] as $k)
                                @php $k->jenis = 'KELUAR'; $merged->push($k); @endphp
                            @endforeach
                            @php $merged = $merged->sortBy('tanggal'); @endphp

                            @forelse($merged as $log)
                                <x-table.row>
                                    <x-table.cell class="text-slate-600">{{ $log->tanggal->format('Y-m-d H:i') }}</x-table.cell>
                                    <x-table.cell>
                                        <x-badge variant="outline">
                                            {{ $log->jenis }}
                                        </x-badge>
                                    </x-table.cell>
                                    <x-table.cell class="font-mono font-semibold text-slate-800">
                                        {{ $log->jenis === 'MASUK' ? $log->no_masuk : ($log->permintaanBarang ? $log->permintaanBarang->no_permintaan : 'RILIS MANUAL') }}
                                    </x-table.cell>
                                    <x-table.cell class="font-mono text-slate-600">{{ $log->barang->kode_barang ?? '-' }}</x-table.cell>
                                    <x-table.cell class="font-medium text-slate-900">{{ $log->snapshot_nama_barang ?? ($log->barang->nama_barang ?? '-') }}</x-table.cell>
                                    <x-table.cell class="font-bold {{ $log->jenis === 'MASUK' ? 'text-emerald-600' : 'text-rose-600' }}">
                                        {{ $log->jenis === 'MASUK' ? '+' : '-' }}{{ $log->jumlah }}
                                    </x-table.cell>
                                    <x-table.cell class="text-slate-600">
                                        {{ $log->jenis === 'MASUK' ? ($log->no_surat_jalan ?? '-') : ($log->permintaanBarang->no_permintaan ?? 'Manual') }}
                                    </x-table.cell>
                                    <x-table.cell class="text-slate-600">{{ $log->user->name ?? '-' }}</x-table.cell>
                                </x-table.row>
                            @empty
                                <x-table.row>
                                    <x-table.cell colspan="8" class="py-8 text-center text-slate-400">Tidak ada transaksi dalam rentang tanggal terpilih.</x-table.cell>
                                </x-table.row>
                            @endforelse
                        </x-table.body>
                    </x-table>

                @elseif($reportType === 'pembelian')
                    <!-- PEMBELIAN TABLE -->
                    <x-table>
                        <x-table.header>
                            <x-table.row>
                                <x-table.head>No. PO</x-table.head>
                                <x-table.head>Tanggal PO</x-table.head>
                                <x-table.head>Supplier</x-table.head>
                                <x-table.head>Barang</x-table.head>
                                <x-table.head>Qty Pesan</x-table.head>
                                <x-table.head>Harga Satuan</x-table.head>
                                <x-table.head>Total Harga</x-table.head>
                                <x-table.head>Status PO</x-table.head>
                            </x-table.row>
                        </x-table.header>
                        <x-table.body>
                            @php $sumTotalPO = 0; @endphp
                            @forelse($reportData['pos'] as $po)
                                @foreach($po->details as $d)
                                    @php 
                                        $itemTotal = $d->jumlah * $d->harga_satuan; 
                                        $sumTotalPO += $itemTotal;
                                    @endphp
                                    <x-table.row>
                                        <x-table.cell class="font-mono font-semibold text-slate-800">{{ $po->no_po }}</x-table.cell>
                                        <x-table.cell class="text-slate-600">{{ \Carbon\Carbon::parse($po->tanggal)->format('Y-m-d') }}</x-table.cell>
                                        <x-table.cell class="font-medium text-slate-900">{{ $po->supplier->nama_supplier ?? '-' }}</x-table.cell>
                                        <x-table.cell class="text-slate-700">{{ $d->snapshot_nama_barang ?? ($d->barang->nama_barang ?? '-') }}</x-table.cell>
                                        <x-table.cell class="font-bold text-slate-800">{{ $d->jumlah }}</x-table.cell>
                                        <x-table.cell class="text-slate-600">Rp {{ number_format($d->harga_satuan, 0, ',', '.') }}</x-table.cell>
                                        <x-table.cell class="text-slate-900 font-bold">Rp {{ number_format($itemTotal, 0, ',', '.') }}</x-table.cell>
                                        <x-table.cell>
                                            <x-badge variant="outline">
                                                {{ strtoupper($po->status) }}
                                            </x-badge>
                                        </x-table.cell>
                                    </x-table.row>
                                @endforeach
                            @empty
                                <x-table.row>
                                    <x-table.cell colspan="8" class="py-8 text-center text-slate-400">Tidak ada purchase order dalam rentang tanggal terpilih.</x-table.cell>
                                </x-table.row>
                            @endforelse
                            @if($reportData['pos']->isNotEmpty())
                                <x-table.row class="bg-slate-100 font-bold text-slate-900">
                                    <x-table.cell colspan="6" class="text-right">AKUMULASI TOTAL PO:</x-table.cell>
                                    <x-table.cell colspan="2" class="text-indigo-700 text-base">Rp {{ number_format($sumTotalPO, 0, ',', '.') }}</x-table.cell>
                                </x-table.row>
                            @endif
                        </x-table.body>
                    </x-table>

                @else
                    <!-- PRODUKSI TABLE -->
                    <x-table>
                        <x-table.header>
                            <x-table.row>
                                <x-table.head>No. Permintaan</x-table.head>
                                <x-table.head>Tanggal</x-table.head>
                                <x-table.head>Pemohon</x-table.head>
                                <x-table.head>Barang</x-table.head>
                                <x-table.head>Jumlah Diminta</x-table.head>
                                <x-table.head>Status</x-table.head>
                            </x-table.row>
                        </x-table.header>
                        <x-table.body>
                            @forelse($reportData['permintaans'] as $req)
                                @foreach($req->details as $d)
                                    <x-table.row>
                                        <x-table.cell class="font-mono font-semibold text-slate-800">{{ $req->no_permintaan }}</x-table.cell>
                                        <x-table.cell class="text-slate-600">{{ \Carbon\Carbon::parse($req->tanggal)->format('Y-m-d') }}</x-table.cell>
                                        <x-table.cell class="font-medium text-slate-900">{{ $req->user->name ?? '-' }}</x-table.cell>
                                        <x-table.cell class="text-slate-700">{{ $d->barang->nama_barang ?? '-' }}</x-table.cell>
                                        <x-table.cell class="font-bold text-slate-800">{{ $d->jumlah }}</x-table.cell>
                                        <x-table.cell>
                                            <x-badge variant="outline">
                                                {{ strtoupper($req->status) }}
                                            </x-badge>
                                        </x-table.cell>
                                    </x-table.row>
                                @endforeach
                            @empty
                                <x-table.row>
                                    <x-table.cell colspan="6" class="py-8 text-center text-slate-400">Tidak ada pengajuan produksi dalam rentang tanggal terpilih.</x-table.cell>
                                </x-table.row>
                            @endforelse
                        </x-table.body>
                    </x-table>
                @endif
            </x-card.content>
        </x-card>
    </div>
    </div>
</x-app-layout>
