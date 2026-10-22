<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-4 min-w-0">
                <x-back-button />
                <div class="min-w-0">
                    <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                        {{ __('Detail Purchase Order (PO): ') }} {{ $po->no_po }}
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">Informasi rincian pesanan & status pengiriman.</p>
                </div>
            </div>
            <div class="flex items-center space-x-3 shrink-0">
                <a href="{{ route('purchase-orders.print', $po) }}" target="_blank" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl transition-all shadow-md shadow-indigo-600/20">
                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak Dokumen PO
                </a>
            </div>
        </div>
    </x-slot>

    <div class="w-full space-y-6">

            <!-- PO Info Card -->
            <div class="bg-white rounded-lg shadow-sm border border-slate-300 p-6 flex flex-wrap justify-between items-center gap-4">
                <div class="space-y-1">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Nomor PO</p>
                    <h3 class="text-2xl font-black text-slate-800 font-mono">{{ $po->no_po }}</h3>
                    <p class="text-sm text-slate-500">Tanggal PO: <span class="font-semibold text-slate-700">{{ \Carbon\Carbon::parse($po->tanggal)->format('d F Y') }}</span></p>
                    <p class="text-sm text-slate-500">Supplier (Vendor): <span class="font-bold text-slate-800">{{ $po->supplier->nama_supplier ?? '-' }}</span></p>
                    <p class="text-sm text-slate-500">Purchasing Staff: <span class="font-semibold text-slate-700">{{ $po->user->name ?? '-' }}</span></p>
                    <p class="text-sm text-slate-500">Payment Terms: <span class="font-semibold text-indigo-700">{{ $po->syarat_pembayaran ?? 'Net 30 Days' }}</span></p>
                </div>

                <div class="flex flex-col items-end gap-3">
                    <span class="px-3 py-1 text-xs font-semibold rounded bg-slate-100 text-slate-700 border border-slate-300">
                        {{ strtoupper($po->status) }}
                    </span>

                    <!-- Action Buttons -->
                    <div class="flex items-center space-x-2">
                        @if(in_array($po->status, ['dikirim', 'diterima_sebagian']) && auth()->user()->hasRole(['admin', 'gudang']))
                            <a href="{{ route('barang-masuk.create', ['po_id' => $po->id]) }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow transition-colors">
                                Catat Penerimaan Barang
                            </a>
                        @endif

                        @if(in_array($po->status, ['dikirim', 'diterima_sebagian']) && auth()->user()->hasRole(['admin', 'purchasing']))
                            <div x-data="{ open: false }">
                                <button type="button" @click="open = true" class="inline-flex items-center px-4 py-2 border border-rose-600 hover:bg-rose-50 text-rose-600 text-xs font-semibold rounded-xl transition-colors">
                                    Batalkan PO
                                </button>
                                <div x-show="open" x-cloak x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @keydown.escape.window="open = false">
                                    <div x-show="open" x-transition.scale @click.outside="open = false" class="w-full max-w-md rounded-xl bg-white shadow-2xl">
                                        <div class="flex items-center justify-between border-b px-6 py-4">
                                            <h3 class="text-lg font-semibold text-gray-900">Konfirmasi Pembatalan PO</h3>
                                            <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
                                        </div>
                                        <div class="px-6 py-5">
                                            <p class="text-sm text-gray-600">Apakah Anda yakin ingin membatalkan PO <span class="font-semibold text-gray-900">{{ $po->no_po }}</span>?</p>
                                        </div>
                                        <div class="flex justify-end gap-3 border-t bg-gray-50 px-6 py-4 rounded-b-xl">
                                            <button type="button" @click="open = false" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100">Batal</button>
                                            <form method="POST" action="{{ route('purchase-orders.cancel', $po->id) }}">
                                                @csrf
                                                <button type="submit" class="rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700">Ya, Batalkan</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Item Details Table & Financial Summary -->
            <div class="bg-white rounded-lg shadow-sm border border-slate-300 overflow-hidden">
                <div class="p-4 border-b border-slate-200 flex justify-between items-center">
                    <h4 class="text-md font-bold text-slate-800">Daftar Detail Barang PO & Pemenuhan</h4>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-100 border-b border-slate-300 text-[11px] font-bold uppercase tracking-wider text-slate-700">
                                <th class="py-3.5 px-4 border-r border-slate-300">Kode Barang</th>
                                <th class="py-3.5 px-4 border-r border-slate-300">Nama Barang (PO)</th>
                                <th class="py-3.5 px-4 border-r border-slate-300">Jumlah Pesan</th>
                                <th class="py-3.5 px-4 border-r border-slate-300">Harga Satuan</th>
                                <th class="py-3.5 px-4 border-r border-slate-300">Subtotal</th>
                                <th class="py-3.5 px-4 border-r border-slate-300">Diterima Aktual</th>
                                <th class="py-3.5 px-4 border-r border-slate-300">Status Pemenuhan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 text-sm">
                            @php $calculatedSubtotal = 0; @endphp
                            @foreach($po->details as $detail)
                                @php 
                                    $receivedQty = $po->barangMasuks->where('barang_id', $detail->barang_id)->sum('jumlah');
                                    $itemSubtotal = $detail->subtotal > 0 ? $detail->subtotal : ($detail->jumlah * $detail->harga_satuan);
                                    $calculatedSubtotal += $itemSubtotal;
                                @endphp
                                <tr class="hover:bg-slate-50">
                                    <td class="py-3.5 px-4 border-r border-slate-200 font-mono font-semibold text-indigo-600">{{ $detail->barang->kode_barang ?? '-' }}</td>
                                    <td class="py-3.5 px-4 border-r border-slate-200 font-semibold text-slate-900">{{ $detail->snapshot_nama_barang ?? ($detail->barang->nama_barang ?? '-') }}</td>
                                    <td class="py-3.5 px-4 border-r border-slate-200 text-slate-800 font-bold">{{ $detail->jumlah }} {{ $detail->barang->satuan ?? '' }}</td>
                                    <td class="py-3.5 px-4 border-r border-slate-200 text-slate-600 font-mono text-xs">Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                                    <td class="py-3.5 px-4 border-r border-slate-200 text-slate-900 font-bold font-mono">Rp {{ number_format($itemSubtotal, 0, ',', '.') }}</td>
                                    <td class="py-3.5 px-4 border-r border-slate-200 font-bold text-slate-800">
                                        {{ $receivedQty }}
                                    </td>
                                    <td class="py-3.5 px-4 border-r border-slate-200">
                                        @if($receivedQty >= $detail->jumlah)
                                            <span class="px-2.5 py-1 rounded text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-300">Selesai</span>
                                        @elseif($receivedQty > 0)
                                            <span class="px-2.5 py-1 rounded text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-300">Kurang {{ $detail->jumlah - $receivedQty }}</span>
                                        @else
                                            <span class="px-2.5 py-1 rounded text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-300">Belum Datang</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Financial Calculation Summary Box -->
                <div class="p-6 bg-slate-50 border-t border-slate-200 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h5 class="text-xs font-bold uppercase text-slate-500 mb-2">Catatan Instruksi PO:</h5>
                        <p class="text-sm text-slate-700 italic bg-white p-3 rounded-xl border border-slate-200">{{ $po->catatan ?? 'Tidak ada catatan instruksi khusus.' }}</p>
                    </div>

                    <div class="space-y-2 bg-white p-4 rounded-xl border border-slate-200 text-sm">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-600 font-medium">Subtotal Item:</span>
                            <span class="font-bold text-slate-800 font-mono">Rp {{ number_format($po->subtotal > 0 ? $po->subtotal : $calculatedSubtotal, 0, ',', '.') }}</span>
                        </div>
                        
                        @if($po->diskon > 0)
                            <div class="flex justify-between items-center text-rose-600">
                                <span class="font-medium">Potongan Diskon:</span>
                                <span class="font-bold font-mono">- Rp {{ number_format($po->diskon, 0, ',', '.') }}</span>
                            </div>
                        @endif

                        <div class="flex justify-between items-center">
                            <span class="text-slate-600 font-medium">Pajak PPN ({{ number_format($po->pajak_persen ?? 11, 1) }}%):</span>
                            <span class="font-bold text-slate-800 font-mono">Rp {{ number_format($po->pajak_nominal, 0, ',', '.') }}</span>
                        </div>

                        <div class="flex justify-between items-center text-lg font-black text-indigo-700 border-t border-slate-200 pt-3">
                            <span>TOTAL KESELURUHAN (GRAND TOTAL):</span>
                            <span class="font-mono">Rp {{ number_format($po->total_harga > 0 ? $po->total_harga : $calculatedSubtotal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Goods Receipt History -->
            @if($po->barangMasuks->isNotEmpty())
                <div class="bg-white rounded-2xl shadow-sm border border-slate-300 overflow-hidden">
                    <div class="p-4 border-b border-slate-200">
                        <h4 class="text-md font-bold text-slate-800">Riwayat Catatan Penerimaan Gudang (Barang Masuk)</h4>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-100 border-b border-slate-300 text-[11px] font-bold uppercase tracking-wider text-slate-700">
                                    <th class="py-3.5 px-4 border-r border-slate-300">No. Penerimaan</th>
                                    <th class="py-3.5 px-4 border-r border-slate-300">Surat Jalan</th>
                                    <th class="py-3.5 px-4 border-r border-slate-300">Nama Barang</th>
                                    <th class="py-3.5 px-4 border-r border-slate-300">Jumlah Diterima</th>
                                    <th class="py-3.5 px-4 border-r border-slate-300">Penerima (Gudang)</th>
                                    <th class="py-3.5 px-4">Tanggal Masuk</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 text-sm">
                                @foreach($po->barangMasuks as $masuk)
                                    <tr class="hover:bg-slate-50">
                                        <td class="py-3.5 px-4 border-r border-slate-200 font-mono font-semibold text-indigo-600">{{ $masuk->no_masuk }}</td>
                                        <td class="py-3.5 px-4 border-r border-slate-200 text-slate-700 font-semibold">{{ $masuk->no_surat_jalan }}</td>
                                        <td class="py-3.5 px-4 border-r border-slate-200 text-slate-900 font-medium">{{ $masuk->snapshot_nama_barang ?? ($masuk->barang->nama_barang ?? '-') }}</td>
                                        <td class="py-3.5 px-4 border-r border-slate-200 font-bold text-emerald-600">+{{ $masuk->jumlah }}</td>
                                        <td class="py-3.5 px-4 border-r border-slate-200 text-slate-600">{{ $masuk->user->name ?? '-' }}</td>
                                        <td class="py-3.5 px-4 text-slate-600">{{ \Carbon\Carbon::parse($masuk->tanggal)->format('d F Y H:i') }}</td>
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
