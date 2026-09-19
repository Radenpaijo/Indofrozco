<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan - PT INDOFROZ</title>
    <!-- Tailwind CSS for printing layout -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body {
                background: white;
                color: black;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body class="bg-white text-black p-8 antialiased" onload="window.print()">
    <!-- Header -->
    <div class="border-b-2 border-gray-800 pb-4 mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-gray-900">PT INDOFROZ</h1>
            <p class="text-sm text-gray-500 font-medium">Inventory & Purchasing ERP System</p>
        </div>
        <div class="text-right">
            <h2 class="text-lg font-bold text-gray-800 uppercase">
                @if($reportType === 'gudang')
                    Laporan Mutasi Barang
                @elseif($reportType === 'pembelian')
                    Laporan Purchase Order (PO)
                @else
                    Laporan Produksi
                @endif
            </h2>
            <p class="text-xs text-gray-500 font-medium mt-1">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
        </div>
    </div>

    <!-- Info details -->
    <div class="mb-4 text-xs text-gray-500 flex justify-between">
        <span>Dicetak oleh: {{ auth()->user()->name }} ({{ strtoupper(auth()->user()->role) }})</span>
        <span>Tanggal cetak: {{ now()->format('d F Y H:i') }}</span>
    </div>

    <!-- Content Table -->
    <div class="mt-6">
        @if($reportType === 'gudang')
            <!-- GUDANG PRINT -->
            <table class="w-full text-left text-xs border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-100 text-gray-700 font-bold">
                        <th class="border border-gray-300 px-3 py-2">Tanggal</th>
                        <th class="border border-gray-300 px-3 py-2">Mutasi</th>
                        <th class="border border-gray-300 px-3 py-2">No. Transaksi</th>
                        <th class="border border-gray-300 px-3 py-2">Kode</th>
                        <th class="border border-gray-300 px-3 py-2">Nama Barang</th>
                        <th class="border border-gray-300 px-3 py-2">Jumlah</th>
                        <th class="border border-gray-300 px-3 py-2">Ref/Surat Jalan</th>
                        <th class="border border-gray-300 px-3 py-2">Petugas</th>
                    </tr>
                </thead>
                <tbody>
                    @php $merged = collect(); @endphp
                    @foreach($reportData['masuk'] as $m)
                        @php $m->jenis = 'MASUK'; $merged->push($m); @endphp
                    @endforeach
                    @foreach($reportData['keluar'] as $k)
                        @php $k->jenis = 'KELUAR'; $merged->push($k); @endphp
                    @endforeach
                    @php $merged = $merged->sortBy('tanggal'); @endphp

                    @forelse($merged as $log)
                        <tr class="border-b border-gray-200">
                            <td class="border border-gray-300 px-3 py-2">{{ $log->tanggal->format('Y-m-d H:i') }}</td>
                            <td class="border border-gray-300 px-3 py-2 font-bold">{{ $log->jenis }}</td>
                            <td class="border border-gray-300 px-3 py-2 font-mono">{{ $log->jenis === 'MASUK' ? $log->no_masuk : ($log->permintaanBarang ? $log->permintaanBarang->no_permintaan : 'RILIS MANUAL') }}</td>
                            <td class="border border-gray-300 px-3 py-2 font-mono">{{ $log->barang->kode_barang ?? '-' }}</td>
                            <td class="border border-gray-300 px-3 py-2">{{ $log->snapshot_nama_barang ?? ($log->barang->nama_barang ?? '-') }}</td>
                            <td class="border border-gray-300 px-3 py-2 font-bold">{{ $log->jenis === 'MASUK' ? '+' : '-' }}{{ $log->jumlah }}</td>
                            <td class="border border-gray-300 px-3 py-2">{{ $log->jenis === 'MASUK' ? ($log->no_surat_jalan ?? '-') : ($log->permintaanBarang->no_permintaan ?? '-') }}</td>
                            <td class="border border-gray-300 px-3 py-2">{{ $log->user->name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="border border-gray-300 px-3 py-8 text-center text-gray-500">Tidak ada transaksi dalam rentang tanggal terpilih.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        @elseif($reportType === 'pembelian')
            <!-- PEMBELIAN PRINT -->
            <table class="w-full text-left text-xs border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-100 text-gray-700 font-bold">
                        <th class="border border-gray-300 px-3 py-2">No. PO</th>
                        <th class="border border-gray-300 px-3 py-2">Tanggal</th>
                        <th class="border border-gray-300 px-3 py-2">Supplier</th>
                        <th class="border border-gray-300 px-3 py-2">Barang</th>
                        <th class="border border-gray-300 px-3 py-2">Jumlah</th>
                        <th class="border border-gray-300 px-3 py-2">Harga Satuan</th>
                        <th class="border border-gray-300 px-3 py-2">Total Harga</th>
                        <th class="border border-gray-300 px-3 py-2">Status PO</th>
                    </tr>
                </thead>
                <tbody>
                    @php $sumTotalPO = 0; @endphp
                    @forelse($reportData['pos'] as $po)
                        @foreach($po->details as $d)
                            @php 
                                $itemTotal = $d->jumlah * $d->harga_satuan;
                                $sumTotalPO += $itemTotal;
                            @endphp
                            <tr class="border-b border-gray-200">
                                <td class="border border-gray-300 px-3 py-2 font-mono">{{ $po->no_po }}</td>
                                <td class="border border-gray-300 px-3 py-2">{{ \Carbon\Carbon::parse($po->tanggal)->format('Y-m-d') }}</td>
                                <td class="border border-gray-300 px-3 py-2">{{ $po->supplier->nama_supplier ?? '-' }}</td>
                                <td class="border border-gray-300 px-3 py-2">{{ $d->snapshot_nama_barang ?? ($d->barang->nama_barang ?? '-') }}</td>
                                <td class="border border-gray-300 px-3 py-2 font-bold">{{ $d->jumlah }}</td>
                                <td class="border border-gray-300 px-3 py-2">Rp {{ number_format($d->harga_satuan, 0, ',', '.') }}</td>
                                <td class="border border-gray-300 px-3 py-2 font-bold">Rp {{ number_format($itemTotal, 0, ',', '.') }}</td>
                                <td class="border border-gray-300 px-3 py-2 uppercase">{{ $po->status }}</td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="8" class="border border-gray-300 px-3 py-8 text-center text-gray-500">Tidak ada purchase order dalam rentang tanggal terpilih.</td>
                        </tr>
                    @endforelse
                    @if($reportData['pos']->isNotEmpty())
                        <tr class="bg-gray-100 font-bold text-gray-900">
                            <td colspan="6" class="border border-gray-300 px-3 py-2 text-right">AKUMULASI TOTAL PO:</td>
                            <td colspan="2" class="border border-gray-300 px-3 py-2 text-indigo-700">Rp {{ number_format($sumTotalPO, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>

        @else
            <!-- PRODUKSI PRINT -->
            <table class="w-full text-left text-xs border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-100 text-gray-700 font-bold">
                        <th class="border border-gray-300 px-3 py-2">No. Permintaan</th>
                        <th class="border border-gray-300 px-3 py-2">Tanggal</th>
                        <th class="border border-gray-300 px-3 py-2">Pemohon (Produksi)</th>
                        <th class="border border-gray-300 px-3 py-2">Nama Barang</th>
                        <th class="border border-gray-300 px-3 py-2">Jumlah</th>
                        <th class="border border-gray-300 px-3 py-2">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportData['permintaans'] as $req)
                        @foreach($req->details as $d)
                            <tr class="border-b border-gray-200">
                                <td class="border border-gray-300 px-3 py-2 font-mono">{{ $req->no_permintaan }}</td>
                                <td class="border border-gray-300 px-3 py-2">{{ \Carbon\Carbon::parse($req->tanggal)->format('Y-m-d') }}</td>
                                <td class="border border-gray-300 px-3 py-2">{{ $req->user->name ?? '-' }}</td>
                                <td class="border border-gray-300 px-3 py-2">{{ $d->barang->nama_barang ?? '-' }}</td>
                                <td class="border border-gray-300 px-3 py-2 font-bold">{{ $d->jumlah }}</td>
                                <td class="border border-gray-300 px-3 py-2 uppercase">{{ $req->status }}</td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="6" class="border border-gray-300 px-3 py-8 text-center text-gray-500">Tidak ada pengajuan produksi dalam rentang tanggal terpilih.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @endif
    </div>

    <!-- Signatures section at bottom of printed page -->
    <div class="mt-16 grid grid-cols-2 text-center text-sm">
        <div>
            <p>Dibuat oleh,</p>
            <div class="h-24"></div>
            <p class="font-bold underline">{{ auth()->user()->name }}</p>
            <p class="text-xs text-gray-500">{{ strtoupper(auth()->user()->role) }}</p>
        </div>
        <div>
            <p>Diketahui oleh,</p>
            <div class="h-24"></div>
            <p class="font-bold underline">_______________________</p>
            <p class="text-xs text-gray-500">OWNER / DIREKTUR</p>
        </div>
    </div>
</body>
</html>
