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

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Alert Messages -->
            @if(session('success'))
                <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded shadow-sm">
                    <span class="text-emerald-800 font-medium">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Table Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead>
                            <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <th class="px-6 py-4">No. PO</th>
                                <th class="px-6 py-4">Tanggal Order</th>
                                <th class="px-6 py-4">Supplier</th>
                                <th class="px-6 py-4">Purchasing Agent</th>
                                <th class="px-6 py-4">Ref. PR</th>
                                <th class="px-6 py-4">Status PO</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse($pos as $po)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4 font-mono font-semibold text-gray-700">
                                        {{ $po->no_po }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        {{ \Carbon\Carbon::parse($po->tanggal)->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-900 font-medium">
                                        {{ $po->supplier->nama_supplier ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        {{ $po->user->name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 font-mono text-gray-500">
                                        {{ $po->permintaanPembelian->no_pr ?? 'Manual (Non-PR)' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($po->status === 'dikirim')
                                            <span class="px-2.5 py-1 text-xs font-bold bg-indigo-50 text-indigo-700 rounded-full">DIKIRIM</span>
                                        @elseif($po->status === 'diterima_sebagian')
                                            <span class="px-2.5 py-1 text-xs font-bold bg-amber-50 text-amber-700 rounded-full font-bold">SEBAGIAN DITERIMA</span>
                                        @elseif($po->status === 'selesai')
                                            <span class="px-2.5 py-1 text-xs font-bold bg-emerald-50 text-emerald-700 rounded-full">SELESAI</span>
                                        @elseif($po->status === 'dibatalkan')
                                            <span class="px-2.5 py-1 text-xs font-bold bg-gray-100 text-gray-500 rounded-full">DIBATALKAN</span>
                                        @else
                                            <span class="px-2.5 py-1 text-xs font-bold bg-gray-50 text-gray-600 rounded-full">{{ strtoupper($po->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <a href="{{ route('purchase-orders.show', $po->id) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold text-xs transition-colors">Detail PO</a>
                                        
                                        @if(in_array($po->status, ['dikirim', 'diterima_sebagian']) && auth()->user()->hasRole(['admin', 'gudang']))
                                            <a href="{{ route('barang-masuk.create', ['po_id' => $po->id]) }}" class="text-emerald-600 hover:text-emerald-900 font-bold text-xs transition-colors">Terima Barang</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                        Belum ada Purchase Order (PO) yang dibuat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($pos->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                        {{ $pos->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
