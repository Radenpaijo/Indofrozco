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

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Filter Section -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="type" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Jenis Laporan</label>
                        <select name="type" id="type" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="gudang" {{ $reportType === 'gudang' ? 'selected' : '' }}>Laporan Gudang (Mutasi Stok)</option>
                            <option value="pembelian" {{ $reportType === 'pembelian' ? 'selected' : '' }}>Laporan Pembelian (PO)</option>
                            <option value="produksi" {{ $reportType === 'produksi' ? 'selected' : '' }}>Laporan Produksi (Requisition)</option>
                        </select>
                    </div>
                    <div>
                        <label for="start_date" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Tanggal Mulai</label>
                        <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>
                    <div>
                        <label for="end_date" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Tanggal Akhir</label>
                        <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white text-sm font-semibold rounded-lg transition-colors">
                            Filter & Sinkronkan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Report Output Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="mb-4">
                    <h3 class="text-lg font-bold text-gray-800">
                        @if($reportType === 'gudang')
                            Laporan Mutasi Barang Gudang
                        @elseif($reportType === 'pembelian')
                            Laporan Transaksi Purchase Order (PO)
                        @else
                            Laporan Permintaan Barang Produksi
                        @endif
                    </h3>
                    <p class="text-xs text-gray-400 mt-1">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}</p>
                </div>

                <div class="border-t border-gray-100 pt-4">
                    @if($reportType === 'gudang')
                        <!-- GUDANG TABLE -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-100 text-sm">
                                <thead>
                                    <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        <th class="px-4 py-3">Tanggal</th>
                                        <th class="px-4 py-3">Jenis</th>
                                        <th class="px-4 py-3">No. Transaksi</th>
                                        <th class="px-4 py-3">Kode</th>
                                        <th class="px-4 py-3">Barang</th>
                                        <th class="px-4 py-3">Jumlah</th>
                                        <th class="px-4 py-3">Ref/Surat Jalan</th>
                                        <th class="px-4 py-3">Operator</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @php $merged = collect(); @endphp
                                    @foreach($reportData['masuk'] as $m)
                                        @php $m->jenis = 'MASUK'; $merged->push($m); @endphp
                                    @endforeach
                                    @foreach($reportData['keluar'] as $k)
                                        @php $k->jenis = 'KELUAR'; $merged->push($k); @endphp
                                    @endforeach
                                    @php $merged = $merged->sortBy('tanggal'); @endphp

                                    @forelse($merged as $log)
                                        <tr>
                                            <td class="px-4 py-3 text-gray-500">{{ $log->tanggal->format('Y-m-d H:i') }}</td>
                                            <td class="px-4 py-3">
                                                <span class="px-2 py-0.5 text-xs font-bold rounded {{ $log->jenis === 'MASUK' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                                    {{ $log->jenis }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 font-mono font-semibold text-gray-700">
                                                {{ $log->jenis === 'MASUK' ? $log->no_masuk : ($log->permintaanBarang ? $log->permintaanBarang->no_permintaan : 'RILIS MANUAL') }}
                                            </td>
                                            <td class="px-4 py-3 font-mono text-gray-500">{{ $log->barang->kode_barang ?? '-' }}</td>
                                            <td class="px-4 py-3 font-medium text-gray-900">{{ $log->snapshot_nama_barang ?? ($log->barang->nama_barang ?? '-') }}</td>
                                            <td class="px-4 py-3 font-bold {{ $log->jenis === 'MASUK' ? 'text-emerald-600' : 'text-rose-600' }}">
                                                {{ $log->jenis === 'MASUK' ? '+' : '-' }}{{ $log->jumlah }}
                                            </td>
                                            <td class="px-4 py-3 text-gray-600">
                                                {{ $log->jenis === 'MASUK' ? ($log->no_surat_jalan ?? '-') : ($log->permintaanBarang->no_permintaan ?? 'Manual') }}
                                            </td>
                                            <td class="px-4 py-3 text-gray-600">{{ $log->user->name ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="px-4 py-8 text-center text-gray-500">Tidak ada transaksi dalam rentang tanggal terpilih.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                    @elseif($reportType === 'pembelian')
                        <!-- PEMBELIAN TABLE -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-100 text-sm">
                                <thead>
                                    <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        <th class="px-4 py-3">No. PO</th>
                                        <th class="px-4 py-3">Tanggal PO</th>
                                        <th class="px-4 py-3">Supplier</th>
                                        <th class="px-4 py-3">Barang</th>
                                        <th class="px-4 py-3">Qty Pesan</th>
                                        <th class="px-4 py-3">Harga Satuan</th>
                                        <th class="px-4 py-3">Total Harga</th>
                                        <th class="px-4 py-3">Status PO</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @php $sumTotalPO = 0; @endphp
                                    @forelse($reportData['pos'] as $po)
                                        @foreach($po->details as $d)
                                            @php 
                                                $itemTotal = $d->jumlah * $d->harga_satuan; 
                                                $sumTotalPO += $itemTotal;
                                            @endphp
                                            <tr>
                                                <td class="px-4 py-3 font-mono font-semibold text-gray-700">{{ $po->no_po }}</td>
                                                <td class="px-4 py-3 text-gray-500">{{ \Carbon\Carbon::parse($po->tanggal)->format('Y-m-d') }}</td>
                                                <td class="px-4 py-3 font-medium text-gray-900">{{ $po->supplier->nama_supplier ?? '-' }}</td>
                                                <td class="px-4 py-3 text-gray-600">{{ $d->snapshot_nama_barang ?? ($d->barang->nama_barang ?? '-') }}</td>
                                                <td class="px-4 py-3 font-bold text-gray-800">{{ $d->jumlah }}</td>
                                                <td class="px-4 py-3 text-gray-600">Rp {{ number_format($d->harga_satuan, 0, ',', '.') }}</td>
                                                <td class="px-4 py-3 text-gray-900 font-bold">Rp {{ number_format($itemTotal, 0, ',', '.') }}</td>
                                                <td class="px-4 py-3">
                                                    <span class="px-2 py-0.5 rounded text-xs font-bold bg-indigo-50 text-indigo-700">
                                                        {{ strtoupper($po->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @empty
                                        <tr>
                                            <td colspan="8" class="px-4 py-8 text-center text-gray-500">Tidak ada purchase order dalam rentang tanggal terpilih.</td>
                                        </tr>
                                    @endforelse
                                    @if($reportData['pos']->isNotEmpty())
                                        <tr class="bg-gray-50 font-bold text-gray-900">
                                            <td colspan="6" class="px-4 py-3 text-right">AKUMULASI TOTAL PO:</td>
                                            <td colspan="2" class="px-4 py-3 text-indigo-700 text-base">Rp {{ number_format($sumTotalPO, 0, ',', '.') }}</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                    @else
                        <!-- PRODUKSI TABLE -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-100 text-sm">
                                <thead>
                                    <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        <th class="px-4 py-3">No. Permintaan</th>
                                        <th class="px-4 py-3">Tanggal</th>
                                        <th class="px-4 py-3">Pemohon</th>
                                        <th class="px-4 py-3">Barang</th>
                                        <th class="px-4 py-3">Jumlah Diminta</th>
                                        <th class="px-4 py-3">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($reportData['permintaans'] as $req)
                                        @foreach($req->details as $d)
                                            <tr>
                                                <td class="px-4 py-3 font-mono font-semibold text-gray-700">{{ $req->no_permintaan }}</td>
                                                <td class="px-4 py-3 text-gray-500">{{ \Carbon\Carbon::parse($req->tanggal)->format('Y-m-d') }}</td>
                                                <td class="px-4 py-3 font-medium text-gray-900">{{ $req->user->name ?? '-' }}</td>
                                                <td class="px-4 py-3 text-gray-600">{{ $d->barang->nama_barang ?? '-' }}</td>
                                                <td class="px-4 py-3 font-bold text-gray-800">{{ $d->jumlah }}</td>
                                                <td class="px-4 py-3">
                                                    <span class="px-2 py-0.5 rounded text-xs font-bold 
                                                        @if($req->status === 'pending') bg-amber-50 text-amber-700
                                                        @elseif($req->status === 'selesai') bg-emerald-50 text-emerald-700
                                                        @else bg-rose-50 text-rose-700
                                                        @endif">
                                                        {{ strtoupper($req->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">Tidak ada pengajuan produksi dalam rentang tanggal terpilih.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
