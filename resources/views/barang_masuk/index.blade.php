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
                                <th class="px-6 py-4">No. Penerimaan</th>
                                <th class="px-6 py-4">No. Surat Jalan</th>
                                <th class="px-6 py-4">Nama Barang</th>
                                <th class="px-6 py-4">Jumlah Diterima</th>
                                <th class="px-6 py-4">Petugas Gudang</th>
                                <th class="px-6 py-4">Ref. PO</th>
                                <th class="px-6 py-4">Tanggal Penerimaan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse($barangMasuks as $masuk)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4 font-mono font-semibold text-gray-700">
                                        {{ $masuk->no_masuk }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-900 font-semibold">
                                        {{ $masuk->no_surat_jalan }}
                                    </td>
                                    <td class="px-6 py-4 font-medium text-gray-800">
                                        {{ $masuk->snapshot_nama_barang ?? ($masuk->barang->nama_barang ?? '-') }}
                                    </td>
                                    <td class="px-6 py-4 font-bold text-emerald-600">
                                        +{{ $masuk->jumlah }} {{ $masuk->barang->satuan ?? '' }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        {{ $masuk->user->name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-500 font-mono">
                                        @if($masuk->purchaseOrder)
                                            <a href="{{ route('purchase-orders.show', $masuk->purchaseOrder->id) }}" class="text-indigo-600 hover:underline">
                                                {{ $masuk->purchaseOrder->no_po }}
                                            </a>
                                        @else
                                            <span class="text-gray-400 italic">Manual (Tanpa PO)</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-gray-500">
                                        {{ \Carbon\Carbon::parse($masuk->tanggal)->format('d M Y H:i') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                        Belum ada penerimaan gudang.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($barangMasuks->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                        {{ $barangMasuks->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
