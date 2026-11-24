<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Pengambilan Digital - {{ $permintaan->no_permintaan }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                padding: 0;
                margin: 0;
                background: white;
            }

            .print-container {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
            }
        }

        @page {
            size: A4 portrait;
            margin: 10mm;
        }
    </style>
</head>

<body class="bg-slate-100 font-sans text-slate-900 p-6 antialiased text-xs">

    <!-- Floating Action Header (No Print) -->
    <div class="no-print max-w-4xl mx-auto mb-6 flex justify-between items-center bg-white p-4 rounded-2xl shadow border border-slate-200">
        <div>
            <span class="text-sm font-bold text-slate-800">Bukti Digital Pengambilan Barang Gudang</span>
            <p class="text-xs text-slate-500">Dokumen transaksi resmi terverifikasi secara elektronik oleh Sistem WMS INDOFROZ.</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="window.print()"
                class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl transition shadow flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Cetak / Simpan PDF
            </button>
            <button onclick="window.close()"
                class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl">
                Tutup
            </button>
        </div>
    </div>

    <!-- Main Document Body -->
    <div class="print-container max-w-4xl mx-auto bg-white p-8 border border-slate-300 text-slate-900">

        <!-- Header Perusahaan & Judul Dokumen -->
        <div class="flex justify-between items-start border-b-2 border-slate-900 pb-4 mb-5">
            <div class="flex items-center space-x-3">
                <img src="{{ asset('images/logo.svg') }}" alt="INDOFROZ Logo" class="h-16 w-16 object-contain">
                <div>
                    <h1 class="text-xl font-black tracking-wider text-slate-900">PT INDOFROZ UTAMA</h1>
                    <p class="text-[11px] text-slate-600 font-semibold">Warehouse Management System - Cold Storage Division</p>
                    <p class="text-[10px] text-slate-500">Jl. Margomulyo Permai No. 88, Surabaya - Jawa Timur</p>
                </div>
            </div>
            <div class="text-right">
                <h2 class="text-base font-black tracking-wider text-slate-900 uppercase">BUKTI PENGELUARAN BARANG DIGITAL</h2>
                <p class="font-mono text-sm font-bold text-indigo-700 mt-0.5">{{ $permintaan->no_permintaan }}</p>
                <div class="mt-1">
                    @if($permintaan->status === 'selesai')
                        <span class="inline-block px-2.5 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                            ✓ TERVERIFIKASI DIGITAL (SELESAI)
                        </span>
                    @elseif($permintaan->status === 'pending')
                        <span class="inline-block px-2.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                            MENUNGGU VERIFIKASI GUDANG
                        </span>
                    @else
                        <span class="inline-block px-2.5 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-300">
                            DITOLAK
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Meta Data Grid -->
        <div class="grid grid-cols-2 gap-4 bg-slate-50 p-3 rounded-lg border border-slate-200 mb-5 text-xs">
            <div>
                <table class="w-full">
                    <tr>
                        <td class="text-slate-500 w-32 py-0.5">Nomor Pengajuan</td>
                        <td class="font-mono font-bold text-slate-900">: {{ $permintaan->no_permintaan }}</td>
                    </tr>
                    <tr>
                        <td class="text-slate-500 py-0.5">Waktu Pengajuan</td>
                        <td class="font-medium text-slate-800">: {{ \Carbon\Carbon::parse($permintaan->tanggal)->format('d F Y H:i') }} WIB</td>
                    </tr>
                    <tr>
                        <td class="text-slate-500 py-0.5">Bagian Pemohon</td>
                        <td class="font-bold text-slate-800">: {{ $permintaan->user->name ?? '-' }} (PRODUKSI)</td>
                    </tr>
                </table>
            </div>
            <div>
                <table class="w-full">
                    <tr>
                        <td class="text-slate-500 w-32 py-0.5">Waktu Verifikasi</td>
                        <td class="font-medium text-slate-800">: 
                            @if($permintaan->status === 'selesai')
                                {{ \Carbon\Carbon::parse($permintaan->barangKeluars->first()?->tanggal ?? $permintaan->updated_at)->format('d F Y H:i') }} WIB
                            @else
                                Dalam Proses
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-slate-500 py-0.5">Petugas Verifikator</td>
                        <td class="font-bold text-slate-800">: {{ $permintaan->barangKeluars->first()?->user->name ?? '-' }} (GUDANG)</td>
                    </tr>
                    <tr>
                        <td class="text-slate-500 py-0.5">Metode Alokasi</td>
                        <td class="font-bold text-indigo-700">: FIFO (First In, First Out)</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Table of Items -->
        <div class="mb-5">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 mb-2">Rincian Barang & Alokasi Batch FIFO</h3>
            <table class="w-full border-collapse border border-slate-300 text-xs">
                <thead>
                    <tr class="bg-slate-200 text-slate-800 font-bold border-b border-slate-400 text-[11px]">
                        <th class="border border-slate-300 p-2 text-center w-8">No</th>
                        <th class="border border-slate-300 p-2 text-left">Kode & Nama Barang</th>
                        <th class="border border-slate-300 p-2 text-left">Gudang & Lokasi Rak</th>
                        <th class="border border-slate-300 p-2 text-left">Alokasi Batch (FIFO)</th>
                        <th class="border border-slate-300 p-2 text-center">Tgl Expired</th>
                        <th class="border border-slate-300 p-2 text-right">Kuantitas</th>
                        <th class="border border-slate-300 p-2 text-center">Status Stok</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($permintaan->details as $idx => $detail)
                        @php
                            $barang = $detail->barang;
                            $gudangName = $barang->location?->warehouse?->name ?? $barang->lokasi_gudang ?? 'Gudang Utama';
                            $rakCode = $barang->location?->name ?? $barang->location?->code ?? $barang->kode_rak ?? '-';
                            
                            // FIFO Batches calculation
                            $remaining = $detail->jumlah;
                            $allocated = [];
                            foreach ($barang->batches as $b) {
                                if ($remaining <= 0) break;
                                $take = min((float)$b->stok, $remaining);
                                $allocated[] = [
                                    'batch_number' => $b->batch_number,
                                    'kadaluarsa' => $b->tanggal_kadaluarsa ? \Carbon\Carbon::parse($b->tanggal_kadaluarsa)->format('d/m/Y') : '-',
                                    'qty' => $take,
                                    'stok_batch' => (float)$b->stok,
                                ];
                                $remaining -= $take;
                            }
                        @endphp
                        <tr class="border-b border-slate-200">
                            <td class="border border-slate-300 p-2 text-center font-bold">{{ $idx + 1 }}</td>
                            <td class="border border-slate-300 p-2">
                                <span class="font-bold text-slate-900 block">{{ $barang->nama_barang }}</span>
                                <span class="font-mono text-[10px] text-slate-500">{{ $barang->kode_barang }}</span>
                            </td>
                            <td class="border border-slate-300 p-2">
                                <span class="font-semibold text-slate-800 block">{{ $gudangName }}</span>
                                <span class="text-[10px] text-indigo-700 font-mono font-bold">Rak: {{ $rakCode }}</span>
                            </td>
                            <td class="border border-slate-300 p-2">
                                @if(!empty($allocated))
                                    @foreach($allocated as $itemBatch)
                                        <div class="mb-0.5">
                                            <span class="font-mono font-bold text-slate-800">{{ $itemBatch['batch_number'] }}</span>
                                            <span class="text-[10px] text-slate-500">({{ $itemBatch['qty'] }} {{ $barang->satuan }})</span>
                                        </div>
                                    @endforeach
                                @else
                                    <span class="text-rose-600 italic text-[11px] font-semibold">Stok kosong / belum ada batch</span>
                                @endif
                            </td>
                            <td class="border border-slate-300 p-2 text-center font-mono">
                                @if(!empty($allocated))
                                    @foreach($allocated as $itemBatch)
                                        <div class="text-[11px] text-slate-700">{{ $itemBatch['kadaluarsa'] }}</div>
                                    @endforeach
                                @else
                                    -
                                @endif
                            </td>
                            <td class="border border-slate-300 p-2 text-right font-bold font-mono">
                                {{ $detail->jumlah }} {{ $barang->satuan }}
                            </td>
                            <td class="border border-slate-300 p-2 text-center">
                                @if($barang->stok >= $detail->jumlah)
                                    <span class="text-emerald-700 font-bold text-[10px]">Tersedia</span>
                                @else
                                    <span class="text-rose-600 font-bold text-[10px]">Kurang ({{ $detail->jumlah - $barang->stok }})</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Electronic Digital Authentication Stamp (Paperless) -->
        <div class="border-2 border-indigo-200 bg-indigo-50/40 p-4 rounded-xl mt-6">
            <div class="flex items-center space-x-3 mb-3 border-b border-indigo-100 pb-2">
                <div class="h-6 w-6 rounded-full bg-indigo-600 flex items-center justify-center text-white">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div>
                    <h4 class="font-black text-xs uppercase tracking-wider text-indigo-900">PENGESAHAN ELEKTRONIK SISTEM WMS (DIGITALLY AUTHENTICATED)</h4>
                    <p class="text-[10px] text-slate-500">Dokumen ini sah dan terdaftar resmi di basis data sistem tanpa memerlukan tanda tangan manual/basah.</p>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4 text-xs">
                <div class="bg-white p-2.5 rounded-lg border border-indigo-100">
                    <span class="text-[10px] font-bold text-slate-400 uppercase block">Diajukan Secara Digital</span>
                    <strong class="text-slate-900 block text-xs mt-0.5">{{ $permintaan->user->name ?? '-' }}</strong>
                    <span class="text-[10px] text-slate-500">Staf Produksi &bull; {{ \Carbon\Carbon::parse($permintaan->tanggal)->format('d/m/Y H:i') }}</span>
                </div>
                <div class="bg-white p-2.5 rounded-lg border border-indigo-100">
                    <span class="text-[10px] font-bold text-slate-400 uppercase block">Diverifikasi & Diserahkan</span>
                    <strong class="text-slate-900 block text-xs mt-0.5">{{ $permintaan->barangKeluars->first()?->user->name ?? ($permintaan->status === 'selesai' ? 'Staf Gudang' : 'Menunggu Approval') }}</strong>
                    <span class="text-[10px] text-slate-500">Staf Gudang &bull; {{ $permintaan->status === 'selesai' ? \Carbon\Carbon::parse($permintaan->barangKeluars->first()?->tanggal ?? $permintaan->updated_at)->format('d/m/Y H:i') : '-' }}</span>
                </div>
                <div class="bg-white p-2.5 rounded-lg border border-indigo-100">
                    <span class="text-[10px] font-bold text-slate-400 uppercase block">Integritas Audit Ledger</span>
                    <strong class="text-indigo-700 block text-xs mt-0.5">VALID & TERCATAT</strong>
                    <span class="text-[10px] font-mono text-slate-500">{{ $permintaan->no_permintaan }}</span>
                </div>
            </div>
        </div>

    </div>

</body>

</html>
