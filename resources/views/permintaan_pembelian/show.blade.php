<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4 min-w-0">
            <x-back-button />
            <div class="min-w-0">
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    {{ __('Detail Requisition (PR): ') }} {{ $pr->no_pr }}
                </h2>
                <p class="text-xs text-slate-500 mt-1">Status persetujuan & rincian barang pengajuan PR.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Alert Messages -->
            @if(session('success'))
                <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded shadow-sm">
                    <span class="text-emerald-800 font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-rose-50 border-l-4 border-rose-500 p-4 rounded shadow-sm">
                    <span class="text-rose-800 font-medium">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Request Card Header -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-wrap justify-between items-center gap-4">
                <div class="space-y-1">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Nomor Requisition</p>
                    <h3 class="text-xl font-bold text-gray-800 font-mono">{{ $pr->no_pr }}</h3>
                    <p class="text-sm text-gray-500">Tanggal Diajukan: <span class="font-medium text-gray-700">{{ \Carbon\Carbon::parse($pr->tanggal)->format('d F Y H:i') }}</span></p>
                    <p class="text-sm text-gray-500">Dibuat Oleh: <span class="font-medium text-gray-700">{{ $pr->user->name ?? '-' }} (GUDANG)</span></p>
                </div>

                <div class="flex flex-col items-end gap-2">
                    <span class="px-3 py-1 text-sm font-bold rounded-full 
                        @if($pr->status === 'pending') bg-amber-50 text-amber-700
                        @elseif($pr->status === 'di_po') bg-emerald-50 text-emerald-700
                        @elseif($pr->status === 'dibatalkan') bg-gray-100 text-gray-500
                        @else bg-gray-50 text-gray-600
                        @endif">
                        {{ strtoupper($pr->status === 'di_po' ? 'TELAH DI-PO-KAN' : $pr->status) }}
                    </span>

                    <!-- Gudang / Admin Cancel Option -->
                    @if($pr->status === 'pending' && auth()->user()->hasRole(['admin', 'gudang']))
                        <div x-data="{ open: false }">
                            <button type="button" @click="open = true" class="mt-2 inline-flex items-center px-4 py-2 border border-rose-600 hover:bg-rose-50 text-rose-600 text-sm font-semibold rounded-lg transition-colors">
                                Batalkan PR
                            </button>
                            <div x-show="open" x-cloak x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @keydown.escape.window="open = false">
                                <div x-show="open" x-transition.scale @click.outside="open = false" class="w-full max-w-md rounded-xl bg-white shadow-2xl">
                                    <div class="flex items-center justify-between border-b px-6 py-4">
                                        <h3 class="text-lg font-semibold text-gray-900">Konfirmasi Pembatalan PR</h3>
                                        <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
                                    </div>
                                    <div class="px-6 py-5">
                                        <p class="text-sm text-gray-600">Apakah Anda yakin ingin membatalkan PR <span class="font-semibold text-gray-900">{{ $pr->no_pr }}</span>?</p>
                                    </div>
                                    <div class="flex justify-end gap-3 border-t bg-gray-50 px-6 py-4 rounded-b-xl">
                                        <button type="button" @click="open = false" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100">Batal</button>
                                        <form method="POST" action="{{ route('permintaan-pembelian.cancel', $pr->id) }}">
                                            @csrf
                                            <button type="submit" class="rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700">Ya, Batalkan</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Purchasing Process to PO Option -->
                    @if($pr->status === 'pending' && auth()->user()->hasRole(['admin', 'purchasing']))
                        <a href="{{ route('purchase-orders.create', ['pr_id' => $pr->id]) }}" class="mt-2 inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow transition-colors">
                            Proses Jadi PO
                        </a>
                    @endif
                </div>
            </div>

            <!-- Items List -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-50">
                    <h4 class="text-md font-bold text-gray-800">Daftar Detail Barang yang Diminta</h4>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead>
                            <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <th class="px-6 py-4">Kode Barang</th>
                                <th class="px-6 py-4">Nama Barang</th>
                                <th class="px-6 py-4">Lokasi Gudang</th>
                                <th class="px-6 py-4">Stok Saat Ini</th>
                                <th class="px-6 py-4">Jumlah Pengajuan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @foreach($pr->details as $detail)
                                <tr>
                                    <td class="px-6 py-4 font-mono text-gray-600">{{ $detail->barang->kode_barang }}</td>
                                    <td class="px-6 py-4 font-medium text-gray-900">{{ $detail->barang->nama_barang }}</td>
                                    <td class="px-6 py-4 text-gray-600">{{ $detail->barang->lokasi_gudang }}</td>
                                    <td class="px-6 py-4 text-gray-600">{{ $detail->barang->stok }} {{ $detail->barang->satuan }}</td>
                                    <td class="px-6 py-4 font-bold text-gray-800">{{ $detail->jumlah }} {{ $detail->barang->satuan }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Linked Purchase Orders -->
            @if($pr->purchaseOrders->isNotEmpty())
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-50">
                        <h4 class="text-md font-bold text-gray-800">Purchase Order (PO) Terkait</h4>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 text-sm">
                            <thead>
                                <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    <th class="px-6 py-4">No. PO</th>
                                    <th class="px-6 py-4">Supplier</th>
                                    <th class="px-6 py-4">Dibuat Oleh</th>
                                    <th class="px-6 py-4">Status PO</th>
                                    <th class="px-6 py-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($pr->purchaseOrders as $po)
                                    <tr>
                                        <td class="px-6 py-4 font-mono font-medium text-gray-900">{{ $po->no_po }}</td>
                                        <td class="px-6 py-4 text-gray-600">{{ $po->supplier->nama_supplier ?? '-' }}</td>
                                        <td class="px-6 py-4 text-gray-600">{{ $po->user->name ?? '-' }}</td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700">
                                                {{ strtoupper($po->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('purchase-orders.show', $po->id) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold text-xs transition-colors">Detail PO</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
