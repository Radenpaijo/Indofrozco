<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3 sm:gap-4 min-w-0">
            <x-back-button :href="route('permintaan-pembelian.index')" />
            <div class="min-w-0">
                <h2 class="font-bold text-xl sm:text-2xl text-slate-800 leading-tight truncate">
                    {{ __('Detail Requisition (PR): ') }} {{ $pr->no_pr }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="w-full space-y-6">

            <!-- Request Card Header -->
            <div class="bg-white rounded-lg shadow-sm border border-slate-300 p-6 flex flex-wrap justify-between items-center gap-4">
                <div class="space-y-1">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Nomor Requisition</p>
                    <h3 class="text-xl font-bold text-gray-800 font-mono">{{ $pr->no_pr }}</h3>
                    <p class="text-sm text-gray-500">Tanggal Diajukan: <span class="font-medium text-gray-700">{{ \Carbon\Carbon::parse($pr->tanggal)->format('d F Y H:i') }}</span></p>
                    <p class="text-sm text-gray-500">Dibuat Oleh: <span class="font-medium text-gray-700">{{ $pr->user->name ?? '-' }} (GUDANG)</span></p>
                </div>

                <div class="flex flex-col items-end gap-2">
                    <span class="px-3 py-1 text-xs font-semibold rounded bg-slate-100 text-slate-700 border border-slate-300">
                        {{ strtoupper($pr->status === 'di_po' ? 'TELAH DI-PO-KAN' : $pr->status) }}
                    </span>

                    <!-- Gudang / Admin Cancel Option -->
                    @if($pr->status === 'pending' && auth()->user()->hasRole(['admin', 'gudang']))
                        <div x-data="{ open: false }">
                            <button type="button" @click="open = true" class="mt-2 inline-flex items-center px-4 py-2 border border-rose-600 hover:bg-rose-50 text-rose-600 text-xs font-semibold rounded-xl transition-colors">
                                Batalkan PR
                            </button>
                            <div x-show="open" x-cloak x-transition.opacity 
                                class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/60 backdrop-blur-xs p-0 sm:p-4 text-left" 
                                @keydown.escape.window="open = false">
                                <div x-show="open" 
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="translate-y-full sm:translate-y-0 sm:opacity-0 sm:scale-95"
                                    x-transition:enter-end="translate-y-0 sm:opacity-100 sm:scale-100"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="translate-y-0 sm:opacity-100 sm:scale-100"
                                    x-transition:leave-end="translate-y-full sm:translate-y-0 sm:opacity-0 sm:scale-95"
                                    @click.outside="open = false" 
                                    class="w-full max-w-md rounded-t-3xl sm:rounded-2xl bg-white shadow-2xl overflow-hidden border-t sm:border border-slate-100 max-h-[92vh] flex flex-col">
                                    <div class="w-10 h-1 bg-slate-300 rounded-full mx-auto my-2.5 sm:hidden shrink-0"></div>
                                    <div class="flex items-center justify-between border-b border-slate-100 px-5 sm:px-6 py-3.5 sm:py-4 shrink-0">
                                        <h3 class="text-base font-bold text-slate-900">Konfirmasi Pembatalan PR</h3>
                                        <button type="button" @click="open = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition-colors">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="px-5 sm:px-6 py-4 sm:py-5 space-y-3 overflow-y-auto">
                                        <p class="text-sm text-slate-700">Apakah Anda yakin ingin membatalkan PR <span class="font-mono font-bold text-slate-900">{{ $pr->no_pr }}</span>?</p>
                                    </div>
                                    <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2 border-t border-slate-100 bg-slate-50/70 p-4 sm:px-6 sm:py-3.5 shrink-0">
                                        <button type="button" @click="open = false" class="w-full sm:w-auto h-11 sm:h-9 px-4 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-100 active:bg-slate-200 border border-slate-300 rounded-xl transition-colors flex items-center justify-center">Batal</button>
                                        <form method="POST" action="{{ route('permintaan-pembelian.cancel', $pr->id) }}" class="w-full sm:w-auto">
                                            @csrf
                                            <button type="submit" class="w-full sm:w-auto h-12 sm:h-9 px-5 text-sm sm:text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 active:bg-rose-800 rounded-xl shadow-sm transition-colors flex items-center justify-center">Ya, Batalkan</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Purchasing Process to PO Option -->
                    @if($pr->status === 'pending' && auth()->user()->hasRole(['admin', 'purchasing']))
                        <a href="{{ route('purchase-orders.create', ['pr_id' => $pr->id]) }}" class="mt-2 inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow transition-colors">
                            Proses Jadi PO
                        </a>
                    @endif
                </div>
            </div>

            <!-- Items List: Mobile Cards (block sm:hidden) & Desktop Table (hidden sm:block) -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-4 border-b border-slate-200">
                    <h4 class="text-sm sm:text-base font-bold text-slate-800">Daftar Detail Barang yang Diminta</h4>
                </div>

                <!-- 1. Mobile Cards View (block sm:hidden) -->
                <div class="p-3 space-y-3 sm:hidden">
                    @foreach($pr->details as $detail)
                        <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/50 space-y-2.5">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <span class="font-bold text-sm text-slate-900 block leading-tight">{{ $detail->barang->nama_barang }}</span>
                                    <span class="font-mono text-xs text-indigo-600 mt-0.5 block">{{ $detail->barang->kode_barang }}</span>
                                </div>
                                <div class="shrink-0 text-right">
                                    <span class="text-[10px] font-semibold uppercase text-slate-400 block">Jumlah Minta</span>
                                    <span class="font-black text-sm text-slate-900 font-mono">{{ $detail->jumlah }} {{ $detail->barang->satuan }}</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-2 text-xs pt-2 border-t border-slate-200/70">
                                <div>
                                    <span class="text-[10px] font-semibold uppercase text-slate-400 block">Lokasi Gudang</span>
                                    <span class="font-medium text-slate-700">{{ $detail->barang->lokasi_gudang ?? '-' }}</span>
                                </div>
                                <div>
                                    <span class="text-[10px] font-semibold uppercase text-slate-400 block">Stok Saat Ini</span>
                                    <span class="font-medium text-slate-700">{{ $detail->barang->stok }} {{ $detail->barang->satuan }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- 2. Desktop Table View (hidden sm:block) -->
                <div class="hidden sm:block overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[650px]">
                        <thead>
                            <tr class="bg-slate-100 border-b border-slate-300 text-[11px] font-bold uppercase tracking-wider text-slate-700 whitespace-nowrap">
                                <th class="py-3.5 px-4 border-r border-slate-300">Kode Barang</th>
                                <th class="py-3.5 px-4 border-r border-slate-300">Nama Barang</th>
                                <th class="py-3.5 px-4 border-r border-slate-300">Lokasi Gudang</th>
                                <th class="py-3.5 px-4 border-r border-slate-300">Stok Saat Ini</th>
                                <th class="py-3.5 px-4 border-r border-slate-300">Jumlah Pengajuan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 text-sm">
                            @foreach($pr->details as $detail)
                                <tr class="hover:bg-slate-50">
                                    <td class="py-3.5 px-4 border-r border-slate-200 font-mono text-slate-600">{{ $detail->barang->kode_barang }}</td>
                                    <td class="py-3.5 px-4 border-r border-slate-200 font-medium text-slate-900">{{ $detail->barang->nama_barang }}</td>
                                    <td class="py-3.5 px-4 border-r border-slate-200 text-slate-600">{{ $detail->barang->lokasi_gudang }}</td>
                                    <td class="py-3.5 px-4 border-r border-slate-200 text-slate-600">{{ $detail->barang->stok }} {{ $detail->barang->satuan }}</td>
                                    <td class="py-3.5 px-4 font-bold text-slate-800 border-r border-slate-200">{{ $detail->jumlah }} {{ $detail->barang->satuan }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Linked Purchase Orders -->
            @if($pr->purchaseOrders->isNotEmpty())
                <div class="bg-white rounded-lg shadow-sm border border-slate-300 overflow-hidden">
                    <div class="p-4 border-b border-slate-200">
                        <h4 class="text-md font-bold text-slate-800">Purchase Order (PO) Terkait</h4>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead>
                                <tr class="bg-slate-100 border-b border-slate-300 text-[11px] font-bold uppercase tracking-wider text-slate-700">
                                    <th class="py-3.5 px-4 border-r border-slate-300">No. PO</th>
                                    <th class="py-3.5 px-4 border-r border-slate-300">Supplier</th>
                                    <th class="py-3.5 px-4 border-r border-slate-300">Dibuat Oleh</th>
                                    <th class="py-3.5 px-4 border-r border-slate-300">Status PO</th>
                                    <th class="py-3.5 px-4 text-right border-r border-slate-300">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @foreach($pr->purchaseOrders as $po)
                                    <tr class="hover:bg-slate-50">
                                        <td class="py-3.5 px-4 border-r border-slate-200 font-mono font-medium text-slate-900">{{ $po->no_po }}</td>
                                        <td class="py-3.5 px-4 border-r border-slate-200 text-slate-600">{{ $po->supplier->nama_supplier ?? '-' }}</td>
                                        <td class="py-3.5 px-4 border-r border-slate-200 text-slate-600">{{ $po->user->name ?? '-' }}</td>
                                        <td class="py-3.5 px-4 border-r border-slate-200">
                                            <span class="px-2.5 py-1 text-xs font-semibold rounded bg-slate-100 text-slate-700 border border-slate-300">
                                                {{ strtoupper($po->status) }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 text-right">
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
