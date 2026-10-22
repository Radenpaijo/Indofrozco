<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-4 min-w-0">
                <x-back-button />
                <div class="min-w-0">
                    <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                        {{ __('Purchase Orders (PO)') }}
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">Daftar pesanan pembelian barang ke supplier / vendor.</p>
                </div>
            </div>
            @if(auth()->user()->hasRole(['admin', 'purchasing']))
                <a href="{{ route('purchase-orders.create') }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-md transition-all shrink-0">
                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Buat PO Baru
                </a>
            @endif
        </div>
    </x-slot>

    <div class="w-full space-y-6">

        <!-- Table Card -->
        <x-table>
            <x-table.header>
                <x-table.row>
                    <x-table.head>No. PO</x-table.head>
                    <x-table.head>Tanggal Order</x-table.head>
                    <x-table.head>Supplier</x-table.head>
                    <x-table.head>Purchasing Agent</x-table.head>
                    <x-table.head>Ref. PR</x-table.head>
                    <x-table.head class="text-center">Status PO</x-table.head>
                    <x-table.head class="text-center">Aksi</x-table.head>
                </x-table.row>
            </x-table.header>
            <x-table.body>
                @forelse($pos as $po)
                    <x-table.row>
                        <x-table.cell class="font-mono font-semibold text-slate-800">
                            {{ $po->no_po }}
                        </x-table.cell>
                        <x-table.cell class="text-slate-600">
                            {{ \Carbon\Carbon::parse($po->tanggal)->format('d M Y') }}
                        </x-table.cell>
                        <x-table.cell class="text-slate-900 font-medium">
                            {{ $po->supplier->nama_supplier ?? '-' }}
                        </x-table.cell>
                        <x-table.cell class="text-slate-600">
                            {{ $po->user->name ?? '-' }}
                        </x-table.cell>
                        <x-table.cell class="font-mono text-slate-600">
                            {{ $po->permintaanPembelian->no_pr ?? 'Manual (Non-PR)' }}
                        </x-table.cell>
                        <x-table.cell class="text-center">
                            @if($po->status === 'dikirim')
                                <x-badge variant="outline">DIKIRIM</x-badge>
                            @elseif($po->status === 'diterima_sebagian')
                                <x-badge variant="secondary">SEBAGIAN DITERIMA</x-badge>
                            @elseif($po->status === 'selesai')
                                <x-badge variant="secondary">SELESAI</x-badge>
                            @elseif($po->status === 'dibatalkan')
                                <x-badge variant="destructive">DIBATALKAN</x-badge>
                            @else
                                <x-badge variant="outline">{{ strtoupper($po->status) }}</x-badge>
                            @endif
                        </x-table.cell>
                        <x-table.cell class="text-center">
                            <div class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap">
                                <a href="{{ route('purchase-orders.show', $po->id) }}">
                                    <x-button variant="outline" size="sm">
                                        <svg class="w-3.5 h-3.5 mr-1 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Detail PO
                                    </x-button>
                                </a>

                                <a href="{{ route('purchase-orders.print', $po->id) }}" target="_blank">
                                    <x-button variant="outline" size="sm">
                                        <svg class="w-3.5 h-3.5 mr-1 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                        </svg>
                                        Cetak
                                    </x-button>
                                </a>
                                
                                @if(in_array($po->status, ['dikirim', 'diterima_sebagian']) && auth()->user()->hasRole(['admin', 'gudang']))
                                    <a href="{{ route('barang-masuk.create', ['po_id' => $po->id]) }}">
                                        <x-button variant="indigo" size="sm">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Terima Barang
                                        </x-button>
                                    </a>
                                @endif
                            </div>
                        </x-table.cell>
                    </x-table.row>
                @empty
                    <x-table.row>
                        <x-table.cell colspan="7" class="py-8 text-center text-slate-400">
                            Belum ada Purchase Order (PO) yang dibuat.
                        </x-table.cell>
                    </x-table.row>
                @endforelse
            </x-table.body>
        </x-table>

        @if($pos->hasPages())
            <div class="px-6 py-4 bg-white rounded-lg border border-slate-300">
                {{ $pos->links() }}
            </div>
        @endif
    </div>
    </div>
</x-app-layout>
