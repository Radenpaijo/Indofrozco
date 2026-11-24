<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <x-back-button :href="route('barangs.show', $barang->id)" />
                <div class="min-w-0">
                    <h2 class="font-bold text-xl sm:text-2xl text-slate-800 leading-tight truncate">
                        Kartu Stok Digital: {{ $barang->nama_barang }}
                    </h2>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('barangs.stock-card.print', array_merge(['barang' => $barang->id], request()->all())) }}" target="_blank">
                    <x-button variant="indigo" class="text-xs px-3.5 py-2">
                        <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Cetak Kartu Stok (A4)
                    </x-button>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="w-full space-y-6">

        <!-- Filter Periode Tanggal -->
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm flex flex-wrap items-center justify-between gap-4">
            <form method="GET" action="{{ route('barangs.stock-card', $barang->id) }}" class="flex flex-wrap items-center gap-3 text-xs">
                <div>
                    <label class="block text-slate-500 font-bold mb-1">Dari Tanggal:</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="rounded-lg border-slate-300 text-xs font-medium focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-slate-500 font-bold mb-1">Sampai Tanggal:</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="rounded-lg border-slate-300 text-xs font-medium focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="pt-4">
                    <x-button type="submit" variant="outline" class="text-xs">
                        Filter Kartu
                    </x-button>
                </div>
            </form>
            <div class="text-right">
                <span class="text-xs text-slate-400 block font-medium">Stok Fisik Saat Ini</span>
                <span class="text-xl font-black text-slate-900 font-mono">{{ $barang->stok }} {{ $barang->satuan }}</span>
            </div>
        </div>

        <!-- TAMPILAN FISIK KARTU STOK BIRU (Authentic Cold Storage Blue Paper Stock Card) -->
        <div class="bg-[#dceaf6] text-slate-900 rounded-2xl border-2 border-[#96bbdc] shadow-md p-6 sm:p-8 font-sans max-w-4xl mx-auto">
            
            <!-- Index Abjad Bar (A s/d Z) -->
            @php
                $firstLetter = strtoupper(substr($barang->nama_barang, 0, 1));
                $letters = range('A', 'Z');
            @endphp
            <div class="flex items-center justify-between border-b-2 border-[#6092bd] pb-3 mb-4 text-[10px] sm:text-xs font-mono font-bold tracking-wider text-slate-700 overflow-x-auto">
                @foreach($letters as $char)
                    <span class="px-1.5 py-0.5 rounded {{ $char === $firstLetter ? 'bg-[#205280] text-white font-black ring-2 ring-[#205280]' : '' }}">
                        {{ $char }}
                    </span>
                @endforeach
            </div>

            <!-- Title & Metadata -->
            <div class="text-center mb-6">
                <h1 class="text-xl sm:text-2xl font-black tracking-widest text-[#163e63] uppercase">
                    KARTU STOCK BARANG
                </h1>
                <div class="w-24 h-0.5 bg-[#6092bd] mx-auto mt-1"></div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-2 gap-x-6 text-xs sm:text-sm border-b-2 border-[#6092bd] pb-4 mb-4">
                <div class="flex items-baseline gap-2">
                    <span class="font-bold text-slate-700 min-w-24">Jenis Barang</span>
                    <span class="font-bold text-[#163e63] border-b border-dotted border-slate-600 flex-1 pb-0.5">: {{ $barang->nama_barang }}</span>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="font-bold text-slate-700 min-w-24">Kartu No.</span>
                    <span class="font-mono font-bold text-[#163e63] border-b border-dotted border-slate-600 flex-1 pb-0.5">: {{ $barang->kode_barang }}</span>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="font-bold text-slate-700 min-w-24">Satuan</span>
                    <span class="font-bold text-[#163e63] border-b border-dotted border-slate-600 flex-1 pb-0.5">: {{ $barang->satuan }}</span>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="font-bold text-slate-700 min-w-24">Lokasi / Rak</span>
                    <span class="font-bold text-[#163e63] border-b border-dotted border-slate-600 flex-1 pb-0.5">: {{ $barang->lokasi_gudang ?? 'Cold Storage' }} ({{ $barang->kode_rak ?? 'Umum' }})</span>
                </div>
            </div>

            <!-- Kartu Ledger Mobile Cards -->
            @php $mobileBalance = $initialBalance; @endphp
            <div class="md:hidden space-y-2.5 text-xs">
                <div class="p-3.5 rounded-xl bg-[#eaf3fa] border border-[#6092bd] flex items-center justify-between">
                    <div>
                        <div class="font-bold text-slate-700">SALDO AWAL</div>
                        <div class="text-[11px] font-mono text-slate-500">{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</div>
                    </div>
                    <div class="font-mono font-black text-sm text-slate-900">{{ number_format($initialBalance, 2) }}</div>
                </div>

                @forelse($transactions as $t)
                    @php
                        $mIn = $t->type === 'IN' ? (float)$t->jumlah : 0;
                        $mOut = $t->type === 'OUT' ? (float)$t->jumlah : 0;
                        $mobileBalance = $mobileBalance + $mIn - $mOut;
                        $mLot = '';
                        if (!empty($t->batch?->batch_number)) $mLot .= 'Lot: ' . $t->batch->batch_number;
                        if (!empty($t->batch?->tanggal_kadaluarsa)) $mLot .= ' (ED: ' . \Carbon\Carbon::parse($t->batch->tanggal_kadaluarsa)->format('d/m/Y') . ')';
                    @endphp
                    <div class="p-3.5 rounded-xl bg-white/90 border border-[#a8c9e4] space-y-2">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <div class="font-bold text-slate-900">{{ $t->nomor_referensi ?: ($t->type === 'IN' ? 'Barang Masuk' : 'Pengeluaran Produksi') }}</div>
                                <div class="text-[11px] font-mono text-slate-500">{{ \Carbon\Carbon::parse($t->created_at)->format('d/m/Y') }}</div>
                                @if($mLot)
                                    <div class="text-[11px] font-mono text-indigo-800 font-semibold mt-0.5">{{ $mLot }}</div>
                                @endif
                                @if(!empty($t->catatan) && $t->catatan !== $t->nomor_referensi)
                                    <div class="text-[10px] text-slate-500 mt-0.5">{{ $t->catatan }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-2 pt-2 border-t border-[#d6e6f4] text-center font-mono">
                            <div>
                                <span class="text-[10px] font-sans text-slate-400 uppercase block">Masuk</span>
                                <span class="font-bold text-emerald-700">{{ $mIn > 0 ? number_format($mIn, 2) : '-' }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] font-sans text-slate-400 uppercase block">Keluar</span>
                                <span class="font-bold text-rose-700">{{ $mOut > 0 ? number_format($mOut, 2) : '-' }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] font-sans text-slate-400 uppercase block">Sisa</span>
                                <span class="font-black text-slate-900">{{ number_format($mobileBalance, 2) }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-6 rounded-xl bg-white/70 border border-[#a8c9e4] text-center text-slate-500 italic">
                        Tidak ada mutasi transaksi pada periode ini.
                    </div>
                @endforelse

                <div class="p-3.5 rounded-xl bg-[#bfd8ed] border-2 border-[#6092bd] flex items-center justify-between">
                    <span class="font-bold uppercase tracking-wider text-slate-900">SALDO AKHIR</span>
                    <span class="font-mono font-black text-sm text-[#163e63]">{{ number_format($mobileBalance, 2) }}</span>
                </div>
            </div>

            <!-- Kartu Ledger Table (Desktop) -->
            <div class="hidden md:block overflow-x-auto bg-white/70 rounded-xl border-2 border-[#6092bd]">
                <table class="w-full text-left border-collapse text-xs sm:text-sm">
                    <thead>
                        <tr class="bg-[#bfd8ed] border-b-2 border-[#6092bd] text-slate-800 font-bold uppercase tracking-wider text-[11px]">
                            <th class="py-2.5 px-3 border-r border-[#6092bd] w-28 text-center">Tgl.</th>
                            <th class="py-2.5 px-3 border-r border-[#6092bd]">TERIMA DARI / NO LOT / REF</th>
                            <th class="py-2.5 px-3 border-r border-[#6092bd] w-24 text-center">Masuk</th>
                            <th class="py-2.5 px-3 border-r border-[#6092bd] w-24 text-center">Keluar</th>
                            <th class="py-2.5 px-3 w-24 text-center">Sisa</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#a8c9e4] font-medium text-slate-800">
                        <!-- Saldo Awal -->
                        <tr class="bg-[#eaf3fa]">
                            <td class="py-2 px-3 border-r border-[#a8c9e4] text-center font-mono text-xs text-slate-500">
                                {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}
                            </td>
                            <td class="py-2 px-3 border-r border-[#a8c9e4] italic text-slate-600 font-bold">
                                SALDO AWAL (Periode Lalu)
                            </td>
                            <td class="py-2 px-3 border-r border-[#a8c9e4] text-center font-mono">-</td>
                            <td class="py-2 px-3 border-r border-[#a8c9e4] text-center font-mono">-</td>
                            <td class="py-2 px-3 text-center font-mono font-bold text-slate-900">
                                {{ number_format($initialBalance, 2) }}
                            </td>
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
                            <tr class="hover:bg-blue-50/50">
                                <td class="py-2.5 px-3 border-r border-[#a8c9e4] text-center font-mono text-xs">
                                    {{ \Carbon\Carbon::parse($t->created_at)->format('d/m/Y') }}
                                </td>
                                <td class="py-2.5 px-3 border-r border-[#a8c9e4] text-xs">
                                    <div class="font-bold text-slate-900">
                                        {{ $t->nomor_referensi ?: ($t->type === 'IN' ? 'Barang Masuk' : 'Pengeluaran Produksi') }}
                                    </div>
                                    @if($lotInfo)
                                        <div class="text-[11px] font-mono text-indigo-800 font-semibold">{{ $lotInfo }}</div>
                                    @endif
                                    @if(!empty($t->catatan) && $t->catatan !== $t->nomor_referensi)
                                        <div class="text-[10px] text-slate-500">{{ $t->catatan }}</div>
                                    @endif
                                </td>
                                <td class="py-2.5 px-3 border-r border-[#a8c9e4] text-center font-mono font-bold text-emerald-700">
                                    {{ $qtyIn > 0 ? number_format($qtyIn, 2) : '-' }}
                                </td>
                                <td class="py-2.5 px-3 border-r border-[#a8c9e4] text-center font-mono font-bold text-rose-700">
                                    {{ $qtyOut > 0 ? number_format($qtyOut, 2) : '-' }}
                                </td>
                                <td class="py-2.5 px-3 text-center font-mono font-black text-slate-900">
                                    {{ number_format($runningBalance, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-500 italic">
                                    Tidak ada mutasi transaksi pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="bg-[#bfd8ed] border-t-2 border-[#6092bd] font-bold text-slate-900">
                            <td colspan="4" class="py-2.5 px-3 text-right uppercase text-xs tracking-wider">
                                SALDO AKHIR :
                            </td>
                            <td class="py-2.5 px-3 text-center font-mono font-black text-sm text-[#163e63]">
                                {{ number_format($runningBalance, 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Footer Card Note -->
            <div class="mt-4 flex items-center justify-between text-[11px] text-slate-600">
                <span>Dokumen Stock Card Cold Storage &bull; WMS INDOFROZ</span>
                <span>Diperbarui: {{ now()->format('d M Y, H:i') }} WIB</span>
            </div>
        </div>

    </div>
</x-app-layout>

