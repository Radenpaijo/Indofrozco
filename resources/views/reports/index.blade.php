<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                <x-back-button :href="route('dashboard')" />
                <div class="min-w-0">
                    <h2 class="font-bold text-xl sm:text-2xl text-slate-800 leading-tight truncate">
                        {{ __('Laporan ERP') }}
                    </h2>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0 w-full sm:w-auto self-start sm:self-center">
                <!-- Export CSV Button -->
                <a href="{{ route('reports.export', ['type' => $reportType, 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="flex-1 sm:flex-initial">
                    <x-button variant="outline" class="w-full justify-center shadow-sm">
                        <svg class="h-4 w-4 mr-2 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export CSV
                    </x-button>
                </a>
                <!-- Print Button -->
                <a href="{{ route('reports.print', ['type' => $reportType, 'start_date' => $startDate, 'end_date' => $endDate]) }}" target="_blank" class="flex-1 sm:flex-initial">
                    <x-button variant="indigo" class="w-full justify-center shadow-sm">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Cetak PDF
                    </x-button>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="w-full space-y-4 sm:space-y-6">
        <!-- Filter Section -->
        <x-card>
            <x-card.content class="p-4 sm:p-6">
                <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
                    <div class="col-span-2 md:col-span-1 space-y-1">
                        <x-label for="type">Jenis Laporan</x-label>
                        <x-select name="type" id="type">
                            <option value="gudang" {{ $reportType === 'gudang' ? 'selected' : '' }}>Laporan Gudang (Mutasi Stok)</option>
                            <option value="pembelian" {{ $reportType === 'pembelian' ? 'selected' : '' }}>Laporan Pembelian (PO)</option>
                            <option value="produksi" {{ $reportType === 'produksi' ? 'selected' : '' }}>Laporan Produksi (Requisition)</option>
                        </x-select>
                    </div>
                    <div class="col-span-1 space-y-1">
                        <x-label for="start_date">Tanggal Mulai</x-label>
                        <x-input type="date" name="start_date" id="start_date" value="{{ $startDate }}" />
                    </div>
                    <div class="col-span-1 space-y-1">
                        <x-label for="end_date">Tanggal Akhir</x-label>
                        <x-input type="date" name="end_date" id="end_date" value="{{ $endDate }}" />
                    </div>
                    <div class="col-span-2 md:col-span-1 flex items-end">
                        <x-button type="submit" variant="default" class="w-full">
                            Filter & Sinkronkan
                        </x-button>
                    </div>
                </form>
            </x-card.content>
        </x-card>

        <!-- Report Output Card -->
        <x-card class="p-4 sm:p-6 overflow-hidden">
            <x-card.header class="p-0 pb-3.5">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div>
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
                            {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                        </x-card.description>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                        {{ number_format($paginatedItems->total()) }} data
                    </span>
                </div>
            </x-card.header>

            <x-card.content class="p-0 border-t border-slate-200 pt-0 sm:pt-4">
                @if($reportType === 'gudang')
                    <!-- GUDANG TABLE (Desktop) -->
                    <div class="hidden sm:block">
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
                                @forelse($paginatedItems as $log)
                                    <x-table.row>
                                        <x-table.cell class="text-slate-600 whitespace-nowrap">{{ $log->tanggal->format('Y-m-d H:i') }}</x-table.cell>
                                        <x-table.cell class="whitespace-nowrap">
                                            <x-badge variant="outline">
                                                {{ $log->jenis }}
                                            </x-badge>
                                        </x-table.cell>
                                        <x-table.cell class="font-mono font-semibold text-slate-800 whitespace-nowrap">
                                            {{ $log->jenis === 'MASUK' ? $log->no_masuk : ($log->permintaanBarang ? $log->permintaanBarang->no_permintaan : 'RILIS MANUAL') }}
                                        </x-table.cell>
                                        <x-table.cell class="font-mono text-slate-600 whitespace-nowrap">{{ $log->barang->kode_barang ?? '-' }}</x-table.cell>
                                        <x-table.cell class="font-medium text-slate-900 whitespace-nowrap">{{ $log->snapshot_nama_barang ?? ($log->barang->nama_barang ?? '-') }}</x-table.cell>
                                        <x-table.cell class="font-bold whitespace-nowrap {{ $log->jenis === 'MASUK' ? 'text-emerald-600' : 'text-rose-600' }}">
                                            {{ $log->jenis === 'MASUK' ? '+' : '-' }}{{ $log->jumlah }}
                                        </x-table.cell>
                                        <x-table.cell class="text-slate-600 whitespace-nowrap">
                                            {{ $log->jenis === 'MASUK' ? ($log->no_surat_jalan ?? '-') : ($log->permintaanBarang->no_permintaan ?? 'Manual') }}
                                        </x-table.cell>
                                        <x-table.cell class="text-slate-600 whitespace-nowrap">{{ $log->user->name ?? '-' }}</x-table.cell>
                                    </x-table.row>
                                @empty
                                    <x-table.row>
                                        <x-table.cell colspan="8" class="py-8 text-center text-slate-400">Tidak ada transaksi dalam rentang tanggal terpilih.</x-table.cell>
                                    </x-table.row>
                                @endforelse
                            </x-table.body>
                        </x-table>
                    </div>

                    <!-- GUDANG MOBILE COMPACT LIST -->
                    <div class="block sm:hidden -mx-4 -mb-4">
                        @if($paginatedItems->isEmpty())
                            <div class="p-6 text-center text-slate-400 text-xs">
                                Tidak ada transaksi dalam rentang tanggal terpilih.
                            </div>
                        @else
                            <div class="divide-y divide-slate-200 bg-white">
                                @foreach($paginatedItems as $log)
                                    <div x-data="{ open: false }" class="transition-colors">
                                        <button type="button" @click="open = !open" class="w-full px-4 py-2.5 flex items-center justify-between gap-2 text-left hover:bg-slate-50">
                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-center gap-1.5">
                                                    <span class="shrink-0 inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold border {{ $log->jenis === 'MASUK' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200' }}">
                                                        {{ $log->jenis }}
                                                    </span>
                                                    <span class="font-bold text-slate-900 text-xs truncate">
                                                        {{ $log->snapshot_nama_barang ?? ($log->barang->nama_barang ?? '-') }}
                                                    </span>
                                                </div>
                                                <div class="text-[11px] text-slate-400 font-mono mt-0.5 truncate">
                                                    {{ $log->tanggal->format('d/m/y H:i') }} &bull; {{ $log->barang->kode_barang ?? '-' }}
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2 shrink-0">
                                                <span class="text-xs font-extrabold whitespace-nowrap {{ $log->jenis === 'MASUK' ? 'text-emerald-600' : 'text-rose-600' }}">
                                                    {{ $log->jenis === 'MASUK' ? '+' : '-' }}{{ $log->jumlah }} {{ $log->barang->satuan ?? '' }}
                                                </span>
                                                <svg class="w-4 h-4 text-slate-400 transition-transform duration-150" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </div>
                                        </button>
                                        <div x-show="open" x-cloak class="px-4 pb-3 pt-2 bg-slate-50/70 border-t border-slate-100 grid grid-cols-2 gap-2 text-[11px]">
                                            <div class="min-w-0">
                                                <span class="text-slate-400 block text-[10px]">No. Transaksi</span>
                                                <span class="font-mono font-semibold text-slate-800 truncate block">
                                                    {{ $log->jenis === 'MASUK' ? $log->no_masuk : ($log->permintaanBarang ? $log->permintaanBarang->no_permintaan : 'RILIS MANUAL') }}
                                                </span>
                                            </div>
                                            <div class="min-w-0">
                                                <span class="text-slate-400 block text-[10px]">Ref / Operator</span>
                                                <span class="text-slate-700 truncate block">
                                                    {{ $log->jenis === 'MASUK' ? ($log->no_surat_jalan ?? '-') : ($log->permintaanBarang->no_permintaan ?? 'Manual') }} &bull; {{ $log->user->name ?? '-' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                @elseif($reportType === 'pembelian')
                    <!-- PEMBELIAN TABLE (Desktop) -->
                    <div class="hidden sm:block">
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
                                @forelse($paginatedItems as $row)
                                    @php $po = $row->po; $d = $row->detail; @endphp
                                    <x-table.row>
                                        <x-table.cell class="font-mono font-semibold text-slate-800 whitespace-nowrap">{{ $po->no_po }}</x-table.cell>
                                        <x-table.cell class="text-slate-600 whitespace-nowrap">{{ \Carbon\Carbon::parse($po->tanggal)->format('Y-m-d') }}</x-table.cell>
                                        <x-table.cell class="font-medium text-slate-900 whitespace-nowrap">{{ $po->supplier->nama_supplier ?? '-' }}</x-table.cell>
                                        <x-table.cell class="text-slate-700 whitespace-nowrap">{{ $d->snapshot_nama_barang ?? ($d->barang->nama_barang ?? '-') }}</x-table.cell>
                                        <x-table.cell class="font-bold text-slate-800 whitespace-nowrap">{{ $d->jumlah }}</x-table.cell>
                                        <x-table.cell class="text-slate-600 whitespace-nowrap">Rp {{ number_format($d->harga_satuan, 0, ',', '.') }}</x-table.cell>
                                        <x-table.cell class="text-slate-900 font-bold whitespace-nowrap">Rp {{ number_format($row->item_total, 0, ',', '.') }}</x-table.cell>
                                        <x-table.cell class="whitespace-nowrap">
                                            <x-badge variant="outline">
                                                {{ strtoupper($po->status) }}
                                            </x-badge>
                                        </x-table.cell>
                                    </x-table.row>
                                @empty
                                    <x-table.row>
                                        <x-table.cell colspan="8" class="py-8 text-center text-slate-400">Tidak ada purchase order dalam rentang tanggal terpilih.</x-table.cell>
                                    </x-table.row>
                                @endforelse
                                @if($paginatedItems->total() > 0)
                                    <x-table.row class="bg-slate-100 font-bold text-slate-900">
                                        <x-table.cell colspan="6" class="text-right whitespace-nowrap">AKUMULASI TOTAL PO:</x-table.cell>
                                        <x-table.cell colspan="2" class="text-indigo-700 text-base whitespace-nowrap">Rp {{ number_format($sumTotalPO, 0, ',', '.') }}</x-table.cell>
                                    </x-table.row>
                                @endif
                            </x-table.body>
                        </x-table>
                    </div>

                    <!-- PEMBELIAN MOBILE COMPACT LIST -->
                    <div class="block sm:hidden -mx-4 -mb-4">
                        @if($paginatedItems->isEmpty())
                            <div class="p-6 text-center text-slate-400 text-xs">
                                Tidak ada purchase order dalam rentang tanggal terpilih.
                            </div>
                        @else
                            <div class="divide-y divide-slate-200 bg-white">
                                @foreach($paginatedItems as $row)
                                    @php $po = $row->po; $d = $row->detail; @endphp
                                    <div x-data="{ open: false }">
                                        <button type="button" @click="open = !open" class="w-full px-4 py-2.5 flex items-center justify-between gap-2 text-left hover:bg-slate-50">
                                            <div class="min-w-0 flex-1">
                                                <div class="font-bold text-slate-900 text-xs truncate">
                                                    {{ $d->snapshot_nama_barang ?? ($d->barang->nama_barang ?? '-') }}
                                                </div>
                                                <div class="text-[11px] text-slate-400 font-mono mt-0.5 truncate">
                                                    {{ $po->no_po }} &bull; {{ \Carbon\Carbon::parse($po->tanggal)->format('d/m/y') }}
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2 shrink-0">
                                                <span class="text-xs font-extrabold text-indigo-700 font-mono whitespace-nowrap">
                                                    Rp {{ number_format($row->item_total, 0, ',', '.') }}
                                                </span>
                                                <svg class="w-4 h-4 text-slate-400 transition-transform duration-150" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </div>
                                        </button>
                                        <div x-show="open" x-cloak class="px-4 pb-3 pt-2 bg-slate-50/70 border-t border-slate-100 space-y-1 text-[11px]">
                                            <div class="flex justify-between gap-2">
                                                <span class="text-slate-400">Supplier</span>
                                                <span class="font-semibold text-slate-800 truncate">{{ $po->supplier->nama_supplier ?? '-' }}</span>
                                            </div>
                                            <div class="flex justify-between gap-2">
                                                <span class="text-slate-400">Qty &times; Harga</span>
                                                <span class="text-slate-700 font-mono">{{ $d->jumlah }} &times; Rp {{ number_format($d->harga_satuan, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="flex justify-between gap-2">
                                                <span class="text-slate-400">Status</span>
                                                <span class="font-semibold text-slate-700">{{ strtoupper($po->status) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="px-4 py-3 bg-indigo-50 border-t border-indigo-200 flex items-center justify-between text-xs">
                                <span class="font-bold text-indigo-950 uppercase text-[11px]">Total PO</span>
                                <span class="font-black text-indigo-700 font-mono text-sm">Rp {{ number_format($sumTotalPO, 0, ',', '.') }}</span>
                            </div>
                        @endif
                    </div>

                @else
                    <!-- PRODUKSI TABLE (Desktop) -->
                    <div class="hidden sm:block">
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
                                @forelse($paginatedItems as $row)
                                    @php $req = $row->req; $d = $row->detail; @endphp
                                    <x-table.row>
                                        <x-table.cell class="font-mono font-semibold text-slate-800 whitespace-nowrap">{{ $req->no_permintaan }}</x-table.cell>
                                        <x-table.cell class="text-slate-600 whitespace-nowrap">{{ \Carbon\Carbon::parse($req->tanggal)->format('Y-m-d') }}</x-table.cell>
                                        <x-table.cell class="font-medium text-slate-900 whitespace-nowrap">{{ $req->user->name ?? '-' }}</x-table.cell>
                                        <x-table.cell class="text-slate-700 whitespace-nowrap">{{ $d->barang->nama_barang ?? '-' }}</x-table.cell>
                                        <x-table.cell class="font-bold text-slate-800 whitespace-nowrap">{{ $d->jumlah }}</x-table.cell>
                                        <x-table.cell class="whitespace-nowrap">
                                            <x-badge variant="outline">
                                                {{ strtoupper($req->status) }}
                                            </x-badge>
                                        </x-table.cell>
                                    </x-table.row>
                                @empty
                                    <x-table.row>
                                        <x-table.cell colspan="6" class="py-8 text-center text-slate-400">Tidak ada pengajuan produksi dalam rentang tanggal terpilih.</x-table.cell>
                                    </x-table.row>
                                @endforelse
                            </x-table.body>
                        </x-table>
                    </div>

                    <!-- PRODUKSI MOBILE COMPACT LIST -->
                    <div class="block sm:hidden -mx-4 -mb-4">
                        @if($paginatedItems->isEmpty())
                            <div class="p-6 text-center text-slate-400 text-xs">
                                Tidak ada pengajuan produksi dalam rentang tanggal terpilih.
                            </div>
                        @else
                            <div class="divide-y divide-slate-200 bg-white">
                                @foreach($paginatedItems as $row)
                                    @php $req = $row->req; $d = $row->detail; @endphp
                                    <div x-data="{ open: false }">
                                        <button type="button" @click="open = !open" class="w-full px-4 py-2.5 flex items-center justify-between gap-2 text-left hover:bg-slate-50">
                                            <div class="min-w-0 flex-1">
                                                <div class="font-bold text-slate-900 text-xs truncate">
                                                    {{ $d->barang->nama_barang ?? '-' }}
                                                </div>
                                                <div class="text-[11px] text-slate-400 font-mono mt-0.5 truncate">
                                                    {{ \Carbon\Carbon::parse($req->tanggal)->format('d/m/y') }} &bull; {{ strtoupper($req->status) }}
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2 shrink-0">
                                                <span class="text-xs font-extrabold text-slate-800 whitespace-nowrap">
                                                    {{ $d->jumlah }} {{ $d->barang->satuan ?? '' }}
                                                </span>
                                                <svg class="w-4 h-4 text-slate-400 transition-transform duration-150" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </div>
                                        </button>
                                        <div x-show="open" x-cloak class="px-4 pb-3 pt-2 bg-slate-50/70 border-t border-slate-100 grid grid-cols-2 gap-2 text-[11px]">
                                            <div class="min-w-0">
                                                <span class="text-slate-400 block text-[10px]">No. Permintaan</span>
                                                <span class="font-mono font-semibold text-slate-800 truncate block">{{ $req->no_permintaan }}</span>
                                            </div>
                                            <div class="min-w-0">
                                                <span class="text-slate-400 block text-[10px]">Pemohon</span>
                                                <span class="text-slate-700 truncate block">{{ $req->user->name ?? '-' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif

                @if($paginatedItems->hasPages())
                    <div class="mt-4 pt-3 border-t border-slate-100">
                        {{ $paginatedItems->links() }}
                    </div>
                @endif
            </x-card.content>
        </x-card>
    </div>
</x-app-layout>
