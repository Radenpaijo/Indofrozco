<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-4 min-w-0">
                <x-back-button />
                <div class="min-w-0">
                    <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                        {{ __('Pengeluaran Gudang & Alokasi Batch FIFO') }}
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">Histori pengeluaran barang untuk produksi / distribusi.</p>
                </div>
            </div>
            @if(auth()->user()->hasRole(['admin', 'gudang']))
                <a href="{{ route('barang-keluar.create') }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-md transition-all shrink-0">
                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Catat Rilis Manual
                </a>
            @endif
        </div>
    </x-slot>

    <div class="w-full space-y-6">

        <!-- Table Card -->
        <x-table>
            <x-table.header>
                <x-table.row>
                    <x-table.head>Nomor Pengeluaran</x-table.head>
                    <x-table.head>Nama Barang</x-table.head>
                    <x-table.head>Jumlah Keluar</x-table.head>
                    <x-table.head>Batch Terpotong (FIFO)</x-table.head>
                    <x-table.head>Petugas Gudang</x-table.head>
                    <x-table.head>Ref. Permintaan</x-table.head>
                    <x-table.head>Tanggal Pengeluaran</x-table.head>
                </x-table.row>
            </x-table.header>
            <x-table.body>
                @forelse($barangKeluars as $keluar)
                    <x-table.row>
                        <x-table.cell class="font-mono font-semibold text-slate-800">
                            @if($keluar->permintaanBarang)
                                REQ-{{ $keluar->permintaanBarang->no_permintaan }}
                            @else
                                MANUAL-OUT-{{ $keluar->id }}
                            @endif
                        </x-table.cell>
                        <x-table.cell class="font-medium text-slate-800">
                            {{ $keluar->snapshot_nama_barang ?? ($keluar->barang->nama_barang ?? '-') }}
                        </x-table.cell>
                        <x-table.cell class="font-bold text-rose-600">
                            -{{ $keluar->jumlah }} {{ $keluar->barang->satuan ?? '' }}
                        </x-table.cell>
                        <x-table.cell>
                            @if($keluar->inventoryTransactions && $keluar->inventoryTransactions->count() > 0)
                                <div class="flex flex-wrap gap-1">
                                    @foreach($keluar->inventoryTransactions as $tx)
                                        @if($tx->batch)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-mono font-medium bg-slate-100 text-slate-700 border border-slate-300">
                                                {{ $tx->batch->batch_number }} (-{{ (float)$tx->jumlah }})
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-mono font-medium bg-slate-50 text-slate-600 border border-slate-200">
                                                Direct Stock (-{{ (float)$tx->jumlah }})
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <span class="text-xs text-slate-400 italic">No batch ledger</span>
                            @endif
                        </x-table.cell>
                        <x-table.cell class="text-slate-600">
                            {{ $keluar->user->name ?? '-' }}
                        </x-table.cell>
                        <x-table.cell class="text-slate-500 font-mono">
                            @if($keluar->permintaanBarang)
                                <a href="{{ route('permintaan-barang.show', $keluar->permintaanBarang->id) }}" class="text-indigo-600 hover:underline font-semibold">
                                    {{ $keluar->permintaanBarang->no_permintaan }}
                                </a>
                            @else
                                <span class="text-slate-400 italic">Rilis Manual</span>
                            @endif
                        </x-table.cell>
                        <x-table.cell class="text-slate-500 text-xs font-mono">
                            {{ \Carbon\Carbon::parse($keluar->tanggal)->format('d M Y H:i') }}
                        </x-table.cell>
                    </x-table.row>
                @empty
                    <x-table.row>
                        <x-table.cell colspan="7" class="py-8 text-center text-slate-400">
                            Belum ada pengeluaran gudang.
                        </x-table.cell>
                    </x-table.row>
                @endforelse
            </x-table.body>
        </x-table>

        @if($barangKeluars->hasPages())
            <div class="px-6 py-4 bg-white rounded-lg border border-slate-300">
                {{ $barangKeluars->links() }}
            </div>
        @endif
    </div>
    </div>
</x-app-layout>
