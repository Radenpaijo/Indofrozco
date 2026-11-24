<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KARTU STOCK - {{ $barang->nama_barang }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Outfit', sans-serif;
            margin: 0;
            padding: 0;
        }
        body {
            background-color: #f1f5f9;
            color: #0f172a;
            padding: 20px;
        }
        .print-container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #dceaf6;
            border: 2px solid #5a8ab8;
            border-radius: 12px;
            padding: 24px;
        }
        .alphabet-bar {
            display: flex;
            justify-content: space-between;
            border-bottom: 2px solid #5a8ab8;
            padding-bottom: 8px;
            margin-bottom: 16px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            font-weight: 700;
        }
        .alphabet-char {
            padding: 2px 4px;
            border-radius: 3px;
        }
        .alphabet-active {
            background-color: #1a4971;
            color: #ffffff;
        }
        .title-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .title-header h1 {
            font-size: 22px;
            font-weight: 900;
            letter-spacing: 2px;
            color: #12385b;
            text-transform: uppercase;
        }
        .metadata-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px 24px;
            border-bottom: 2px solid #5a8ab8;
            padding-bottom: 14px;
            margin-bottom: 16px;
            font-size: 13px;
        }
        .meta-row {
            display: flex;
            align-items: baseline;
        }
        .meta-label {
            font-weight: 700;
            width: 100px;
            color: #334155;
        }
        .meta-value {
            font-weight: 800;
            color: #12385b;
            border-bottom: 1px dotted #475569;
            flex: 1;
            padding-bottom: 2px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: rgba(255, 255, 255, 0.85);
            border: 2px solid #5a8ab8;
            font-size: 12px;
        }
        th, td {
            border: 1px solid #7da6cc;
            padding: 8px 10px;
        }
        th {
            background-color: #bdd8ee;
            color: #0f172a;
            font-weight: 800;
            text-transform: uppercase;
            font-size: 11px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        .font-bold { font-weight: 700; }
        .text-emerald { color: #047857; }
        .text-rose { color: #b91c1c; }
        .footer-note {
            display: flex;
            justify-content: space-between;
            margin-top: 14px;
            font-size: 10px;
            color: #475569;
        }
        .no-print {
            text-align: center;
            margin-bottom: 16px;
        }
        .btn-print {
            background-color: #2563eb;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            font-size: 13px;
        }
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .print-container {
                max-width: 100%;
                border: 1.5px solid #000;
                background-color: #e5f0fa !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                box-shadow: none;
                padding: 16px;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button class="btn-print" onclick="window.print()">🖨️ Cetak Dokumen</button>
        <button class="btn-print" style="background:#64748b; margin-left: 8px;" onclick="window.close()">Tutup</button>
    </div>

    <div class="print-container">
        <!-- Alphabet Bar -->
        @php
            $firstLetter = strtoupper(substr($barang->nama_barang, 0, 1));
            $letters = range('A', 'Z');
        @endphp
        <div class="alphabet-bar">
            @foreach($letters as $char)
                <span class="alphabet-char {{ $char === $firstLetter ? 'alphabet-active' : '' }}">
                    {{ $char }}
                </span>
            @endforeach
        </div>

        <div class="title-header">
            <h1>KARTU STOCK BARANG</h1>
        </div>

        <div class="metadata-grid">
            <div class="meta-row">
                <span class="meta-label">Jenis Barang</span>
                <span class="meta-value">: {{ $barang->nama_barang }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-label">Kartu No.</span>
                <span class="meta-value font-mono">: {{ $barang->kode_barang }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-label">Satuan</span>
                <span class="meta-value">: {{ $barang->satuan }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-label">Lokasi / Rak</span>
                <span class="meta-value">: {{ $barang->lokasi_gudang ?? 'Cold Storage' }} ({{ $barang->kode_rak ?? 'Umum' }})</span>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 85px;" class="text-center">Tgl.</th>
                    <th>TERIMA DARI / NO LOT / REF</th>
                    <th style="width: 85px;" class="text-center">Masuk</th>
                    <th style="width: 85px;" class="text-center">Keluar</th>
                    <th style="width: 85px;" class="text-center">Sisa</th>
                </tr>
            </thead>
            <tbody>
                <tr style="background-color: #f1f7fc;">
                    <td class="text-center font-mono" style="font-size: 11px;">
                        {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}
                    </td>
                    <td style="font-style: italic; font-weight: 700;">SALDO AWAL (Periode Lalu)</td>
                    <td class="text-center font-mono">-</td>
                    <td class="text-center font-mono">-</td>
                    <td class="text-center font-mono font-bold">{{ number_format($initialBalance, 2) }}</td>
                </tr>

                @php
                    $runningBalance = $initialBalance;
                @endphp

                @forelse($transactions as $t)
                    @php
                        $qtyIn = $t->type === 'IN' ? (float)$t->jumlah : 0;
                        $qtyOut = $t->type === 'OUT' ? (float)$t->jumlah : 0;
                        $runningBalance = $runningBalance + $qtyIn - $qtyOut;

                        $lotInfo = '';
                        if (!empty($t->batch?->batch_number)) {
                            $lotInfo .= 'Lot: ' . $t->batch->batch_number;
                        }
                        if (!empty($t->batch?->tanggal_kadaluarsa)) {
                            $lotInfo .= ' (ED: ' . \Carbon\Carbon::parse($t->batch->tanggal_kadaluarsa)->format('d/m/Y') . ')';
                        }
                    @endphp
                    <tr>
                        <td class="text-center font-mono" style="font-size: 11px;">
                            {{ \Carbon\Carbon::parse($t->created_at)->format('d/m/Y') }}
                        </td>
                        <td>
                            <div class="font-bold">{{ $t->nomor_referensi ?: ($t->type === 'IN' ? 'Barang Masuk' : 'Pengeluaran') }}</div>
                            @if($lotInfo)
                                <div style="font-size: 10px; font-family: 'JetBrains Mono', monospace; color: #1e3a8a;">{{ $lotInfo }}</div>
                            @endif
                        </td>
                        <td class="text-center font-mono font-bold text-emerald">
                            {{ $qtyIn > 0 ? number_format($qtyIn, 2) : '-' }}
                        </td>
                        <td class="text-center font-mono font-bold text-rose">
                            {{ $qtyOut > 0 ? number_format($qtyOut, 2) : '-' }}
                        </td>
                        <td class="text-center font-mono font-bold">
                            {{ number_format($runningBalance, 2) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center" style="padding: 20px; font-style: italic; color: #64748b;">
                            Tidak ada transaksi pada periode ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr style="background-color: #bdd8ee; font-weight: 800;">
                    <td colspan="4" class="text-right" style="text-transform: uppercase;">SALDO AKHIR :</td>
                    <td class="text-center font-mono font-bold" style="font-size: 13px;">{{ number_format($runningBalance, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        <div class="footer-note">
            <span>WMS INDOFROZ &bull; Cold Storage Physical Stock Card</span>
            <span>Dicetak pada: {{ now()->format('d/m/Y H:i') }} WIB</span>
        </div>
    </div>

</body>
</html>

