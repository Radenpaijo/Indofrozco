<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-4 min-w-0">
                <x-back-button />
                <div class="min-w-0">
                    <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                        {{ __('Penerimaan Gudang (Barang Masuk)') }}
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">Catat transaksi penerimaan stok barang dari PO / Supplier.</p>
                </div>
            </div>
            @if(auth()->user()->hasRole(['admin', 'gudang']))
                <a href="{{ route('barang-masuk.create') }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-md transition-all shrink-0">
                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Catat Penerimaan Baru
                </a>
            @endif
        </div>
    </x-slot>

    <div class="w-full space-y-6">

        <!-- Table Card -->
        <x-table>
            <x-table.header>
                <x-table.row>
                    <x-table.head>No. Penerimaan</x-table.head>
                    <x-table.head>No. Surat Jalan</x-table.head>
                    <x-table.head>Nama Barang</x-table.head>
                    <x-table.head>Jumlah Diterima</x-table.head>
                    <x-table.head>Petugas Gudang</x-table.head>
                    <x-table.head>Ref. PO</x-table.head>
                    <x-table.head>Tanggal Penerimaan</x-table.head>
                </x-table.row>
            </x-table.header>
            <x-table.body>
                @forelse($barangMasuks as $masuk)
                    <x-table.row>
                        <x-table.cell class="font-mono font-semibold text-slate-800">
                            {{ $masuk->no_masuk }}
                        </x-table.cell>
                        <x-table.cell class="text-slate-900 font-semibold">
                            {{ $masuk->no_surat_jalan }}
                        </x-table.cell>
                        <x-table.cell class="font-medium text-slate-800">
                            {{ $masuk->snapshot_nama_barang ?? ($masuk->barang->nama_barang ?? '-') }}
                        </x-table.cell>
                        <x-table.cell class="font-bold text-emerald-600">
                            +{{ $masuk->jumlah }} {{ $masuk->barang->satuan ?? '' }}
                        </x-table.cell>
                        <x-table.cell class="text-slate-600">
                            {{ $masuk->user->name ?? '-' }}
                        </x-table.cell>
                        <x-table.cell class="text-slate-500 font-mono">
                            @if($masuk->purchaseOrder)
                                <a href="{{ route('purchase-orders.show', $masuk->purchaseOrder->id) }}" class="text-indigo-600 hover:underline font-semibold">
                                    {{ $masuk->purchaseOrder->no_po }}
                                </a>
                            @else
                                <span class="text-slate-400 italic">Manual (Tanpa PO)</span>
                            @endif
                        </x-table.cell>
                        <x-table.cell class="text-slate-500">
                            {{ \Carbon\Carbon::parse($masuk->tanggal)->format('d M Y H:i') }}
                        </x-table.cell>
                    </x-table.row>
                @empty
                    <x-table.row>
                        <x-table.cell colspan="7" class="py-8 text-center text-slate-400">
                            Belum ada penerimaan gudang.
                        </x-table.cell>
                    </x-table.row>
                @endforelse
            </x-table.body>
        </x-table>

        @if($barangMasuks->hasPages())
            <div class="px-6 py-4 bg-white rounded-lg border border-slate-300">
                {{ $barangMasuks->links() }}
            </div>
        @endif
    </div>
    </div>
</x-app-layout>
