<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order - {{ $po->no_po }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; margin: 0; background: white; }
            .print-container { border: none !important; shadow: none !important; padding: 0 !important; }
        }
        @page { size: A4 portrait; margin: 12mm; }
    </style>
</head>
<body class="bg-slate-100 font-sans text-slate-900 p-6 antialiased">

    <!-- Floating Action Header (No Print) -->
    <div class="no-print max-w-4xl mx-auto mb-6 flex justify-between items-center bg-white p-4 rounded-2xl shadow border border-slate-200">
        <span class="text-sm font-bold text-slate-700">Preview Dokumen Cetak Purchase Order (PO)</span>
        <div class="flex space-x-3">
            <button onclick="window.print()" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-xl transition shadow flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Cetak Sekarang (Print / Save PDF)
            </button>
            <button onclick="window.close()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl">
                Tutup Window
            </button>
        </div>
    </div>

    <!-- Main PO Document Body -->
    <div class="print-container max-w-4xl mx-auto bg-white p-10 rounded-2xl shadow-xl border border-slate-200 text-slate-900">
        
        <!-- Header Perusahaan & Judul Dokumen -->
        <div class="flex justify-between items-center border-b-2 border-slate-900 pb-6 mb-6">
            <div class="flex items-center space-x-4">
                <img src="{{ asset('images/logo.svg') }}" alt="INDOFROZ Logo" class="h-20 w-20 object-contain">
                <div>
                    <h1 class="text-2xl font-black tracking-wider text-slate-900">PT INDOFROZ UTAMA</h1>
                    <p class="text-xs text-slate-600 font-semibold mt-0.5">Cold Storage & Seafood Processing Industry</p>
                    <p class="text-xs text-slate-500">Jl. Margomulyo Permai No. 88, Surabaya - Jawa Timur</p>
                    <p class="text-xs text-slate-500">Telp: (031) 7654-3210 | Email: purchasing@indofroz.com</p>
                </div>
            </div>
            <div class="text-right">
                <h2 class="text-3xl font-black tracking-widest text-indigo-700 uppercase">PURCHASE ORDER</h2>
                <p class="font-mono text-base font-bold text-slate-800 mt-1">{{ $po->no_po }}</p>
                <p class="text-xs text-slate-500 mt-0.5">Tanggal: <span class="font-bold text-slate-700">{{ \Carbon\Carbon::parse($po->tanggal)->format('d F Y') }}</span></p>
            </div>
        </div>

        <!-- Vendor & Order Metadata -->
        <div class="grid grid-cols-2 gap-8 mb-8 text-sm">
            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 border-b border-slate-200 pb-1">VENDOR / SUPPLIER (KEPADA)</h3>
                <p class="font-bold text-slate-900 text-base">{{ $po->supplier->nama_supplier ?? '-' }}</p>
                <p class="text-xs text-slate-600 mt-1"><span class="font-semibold">Kontak:</span> {{ $po->supplier->kontak ?? '-' }}</p>
                <p class="text-xs text-slate-600 mt-0.5"><span class="font-semibold">Alamat:</span> {{ $po->supplier->alamat ?? '-' }}</p>
            </div>

            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 border-b border-slate-200 pb-1">INFORMASI PESANAN</h3>
                <div class="space-y-1 text-xs">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Syarat Pembayaran:</span>
                        <span class="font-bold text-slate-800">{{ $po->syarat_pembayaran ?? 'Net 30 Days' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Referensi PR:</span>
                        <span class="font-mono font-semibold text-slate-700">{{ $po->permintaanPembelian->no_pr ?? 'Manual Direct PO' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Dibuat Oleh:</span>
                        <span class="font-semibold text-slate-800">{{ $po->user->name ?? 'Purchasing' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Detail Barang -->
        <table class="w-full border-collapse mb-6 text-sm">
            <thead>
                <tr class="bg-slate-900 text-white text-xs uppercase font-bold text-left">
                    <th class="p-3 w-12 text-center">No</th>
                    <th class="p-3 w-28">Kode</th>
                    <th class="p-3">Deskripsi Barang</th>
                    <th class="p-3 text-center w-24">Jumlah</th>
                    <th class="p-3 text-right w-36">Harga Satuan</th>
                    <th class="p-3 text-right w-36">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 border-b border-slate-900">
                @php $calcSubtotal = 0; @endphp
                @foreach($po->details as $index => $detail)
                    @php 
                        $itemSubtotal = $detail->subtotal > 0 ? $detail->subtotal : ($detail->jumlah * $detail->harga_satuan);
                        $calcSubtotal += $itemSubtotal;
                    @endphp
                    <tr class="hover:bg-slate-50">
                        <td class="p-3 text-center text-slate-500">{{ $index + 1 }}</td>
                        <td class="p-3 font-mono font-bold text-slate-700">{{ $detail->barang->kode_barang ?? '-' }}</td>
                        <td class="p-3 font-semibold text-slate-900">{{ $detail->snapshot_nama_barang ?? ($detail->barang->nama_barang ?? '-') }}</td>
                        <td class="p-3 text-center font-bold text-slate-800">{{ $detail->jumlah }} {{ $detail->barang->satuan ?? '' }}</td>
                        <td class="p-3 text-right font-mono text-slate-700">Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                        <td class="p-3 text-right font-mono font-bold text-slate-900">Rp {{ number_format($itemSubtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Total Breakdown Table -->
        <div class="flex justify-between items-start mb-10">
            <div class="w-1/2 pr-6">
                <h4 class="text-xs font-bold uppercase text-slate-500 mb-1">Catatan & Ketentuan:</h4>
                <p class="text-xs text-slate-600 bg-slate-50 p-3 rounded-lg border border-slate-200 leading-relaxed">
                    {{ $po->catatan ?? '1. Kirimkan surat jalan resmi & sertifikat pemeriksaan saat barang tiba. 2. Faktur tagihan wajib melampirkan PO ini.' }}
                </p>
            </div>

            <div class="w-1/2 bg-slate-50 p-4 rounded-xl border border-slate-200 text-sm space-y-2">
                <div class="flex justify-between text-slate-600">
                    <span>Subtotal:</span>
                    <span class="font-mono font-bold text-slate-800">Rp {{ number_format($po->subtotal > 0 ? $po->subtotal : $calcSubtotal, 0, ',', '.') }}</span>
                </div>

                @if($po->diskon > 0)
                    <div class="flex justify-between text-rose-600 text-xs">
                        <span>Diskon Potongan:</span>
                        <span class="font-mono font-bold">- Rp {{ number_format($po->diskon, 0, ',', '.') }}</span>
                    </div>
                @endif

                <div class="flex justify-between text-slate-600 text-xs border-t border-slate-200 pt-1">
                    <span>Pajak PPN ({{ number_format($po->pajak_persen ?? 11, 1) }}%):</span>
                    <span class="font-mono font-semibold text-slate-800">Rp {{ number_format($po->pajak_nominal, 0, ',', '.') }}</span>
                </div>

                <div class="flex justify-between text-base font-black text-slate-900 border-t-2 border-slate-900 pt-2">
                    <span>TOTAL HARGA:</span>
                    <span class="font-mono text-indigo-700">Rp {{ number_format($po->total_harga > 0 ? $po->total_harga : $calcSubtotal, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Tanda Tangan & Persetujuan -->
        <div class="grid grid-cols-3 gap-6 text-center text-xs mt-12 pt-6 border-t border-slate-200">
            <div>
                <p class="text-slate-500 font-semibold">Dibuat Oleh (Purchasing)</p>
                <div class="h-20"></div>
                <p class="font-bold text-slate-900 underline">{{ $po->user->name ?? 'Staf Purchasing' }}</p>
                <p class="text-[10px] text-slate-400">Tanggal: {{ \Carbon\Carbon::parse($po->tanggal)->format('d/m/Y') }}</p>
            </div>

            <div>
                <p class="text-slate-500 font-semibold">Disetujui Oleh (Management)</p>
                <div class="h-20"></div>
                <p class="font-bold text-slate-900 underline">( Manager Operasional )</p>
                <p class="text-[10px] text-slate-400">Tanggal: ....................</p>
            </div>

            <div>
                <p class="text-slate-500 font-semibold">Konfirmasi Supplier (Vendor)</p>
                <div class="h-20"></div>
                <p class="font-bold text-slate-900 underline">{{ $po->supplier->nama_supplier ?? 'Nama & Stempel Supplier' }}</p>
                <p class="text-[10px] text-slate-400">Tanggal: ....................</p>
            </div>
        </div>

    </div>

</body>
</html>
