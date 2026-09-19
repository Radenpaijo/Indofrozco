<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-slate-800 leading-tight">
                {{ __('Detail Purchase Order (PO): ') }} {{ $po->no_po }}
            </h2>
            <div class="flex items-center space-x-3">
                <a href="{{ route('purchase-orders.print', $po) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-all shadow-md shadow-indigo-600/20">
                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak Dokumen PO
                </a>
                <a href="{{ route('purchase-orders.index') }}" class="inline-flex items-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-xl transition-colors">
                    &larr; Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Alert Messages -->
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-xl shadow-sm">
                    <span class="text-emerald-800 font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-rose-50 border border-rose-200 p-4 rounded-xl shadow-sm">
                    <span class="text-rose-800 font-medium">{{ session('error') }}</span>
                </div>
            @endif

            <!-- PO Info Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 flex flex-wrap justify-between items-center gap-4">
                <div class="space-y-1">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Nomor PO</p>
                    <h3 class="text-2xl font-black text-slate-800 font-mono">{{ $po->no_po }}</h3>
                    <p class="text-sm text-slate-500">Tanggal PO: <span class="font-semibold text-slate-700">{{ \Carbon\Carbon::parse($po->tanggal)->format('d F Y') }}</span></p>
                    <p class="text-sm text-slate-500">Supplier (Vendor): <span class="font-bold text-slate-800">{{ $po->supplier->nama_supplier ?? '-' }}</span></p>
                    <p class="text-sm text-slate-500">Purchasing Staff: <span class="font-semibold text-slate-700">{{ $po->user->name ?? '-' }}</span></p>
                    <p class="text-sm text-slate-500">Payment Terms: <span class="font-semibold text-indigo-700">{{ $po->syarat_pembayaran ?? 'Net 30 Days' }}</span></p>
                </div>

                <div class="flex flex-col items-end gap-3">
                    <span class="px-4 py-1.5 text-xs font-bold rounded-full 
                        @if($po->status === 'dikirim') bg-indigo-100 text-indigo-700
                        @elseif($po->status === 'diterima_sebagian') bg-amber-100 text-amber-700
                        @elseif($po->status === 'selesai') bg-emerald-100 text-emerald-700
                        @elseif($po->status === 'dibatalkan') bg-slate-100 text-slate-500
                        @else bg-slate-50 text-slate-600
                        @endif">
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
                            <form method="POST" action="{{ route('purchase-orders.cancel', $po->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan PO ini?')">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-rose-600 hover:bg-rose-50 text-rose-600 text-xs font-semibold rounded-xl transition-colors">
                                    Batalkan PO
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Item Details Table & Financial Summary -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                    <h4 class="text-md font-bold text-slate-800">Daftar Detail Barang PO & Pemenuhan</h4>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead>
                            <tr class="bg-slate-50 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                <th class="px-6 py-4">Kode Barang</th>
                                <th class="px-6 py-4">Nama Barang (PO)</th>
                                <th class="px-6 py-4">Jumlah Pesan</th>
                                <th class="px-6 py-4">Harga Satuan</th>
                                <th class="px-6 py-4">Subtotal</th>
                                <th class="px-6 py-4">Diterima Aktual</th>
                                <th class="px-6 py-4">Status Pemenuhan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @php $calculatedSubtotal = 0; @endphp
                            @foreach($po->details as $detail)
                                @php 
                                    $receivedQty = $po->barangMasuks->where('barang_id', $detail->barang_id)->sum('jumlah');
                                    $itemSubtotal = $detail->subtotal > 0 ? $detail->subtotal : ($detail->jumlah * $detail->harga_satuan);
                                    $calculatedSubtotal += $itemSubtotal;
                                @endphp
                                <tr class="hover:bg-slate-50/80">
                                    <td class="px-6 py-4 font-mono font-semibold text-indigo-600">{{ $detail->barang->kode_barang ?? '-' }}</td>
                                    <td class="px-6 py-4 font-semibold text-slate-900">{{ $detail->snapshot_nama_barang ?? ($detail->barang->nama_barang ?? '-') }}</td>
                                    <td class="px-6 py-4 text-slate-800 font-bold">{{ $detail->jumlah }} {{ $detail->barang->satuan ?? '' }}</td>
                                    <td class="px-6 py-4 text-slate-600 font-mono text-xs">Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-slate-900 font-bold font-mono">Rp {{ number_format($itemSubtotal, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 font-bold {{ $receivedQty >= $detail->jumlah ? 'text-emerald-600' : 'text-amber-600' }}">
                                        {{ $receivedQty }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($receivedQty >= $detail->jumlah)
                                            <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold">Selesai</span>
                                        @elseif($receivedQty > 0)
                                            <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-700 text-xs font-bold">Kurang {{ $detail->jumlah - $receivedQty }}</span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-500 text-xs font-bold">Belum Datang</span>
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
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
                    <div class="p-6 border-b border-slate-100">
                        <h4 class="text-md font-bold text-slate-800">Riwayat Catatan Penerimaan Gudang (Barang Masuk)</h4>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100 text-sm">
                            <thead>
                                <tr class="bg-slate-50 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                    <th class="px-6 py-4">No. Penerimaan</th>
                                    <th class="px-6 py-4">Surat Jalan</th>
                                    <th class="px-6 py-4">Nama Barang</th>
                                    <th class="px-6 py-4">Jumlah Diterima</th>
                                    <th class="px-6 py-4">Penerima (Gudang)</th>
                                    <th class="px-6 py-4">Tanggal Masuk</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($po->barangMasuks as $masuk)
                                    <tr>
                                        <td class="px-6 py-4 font-mono font-semibold text-indigo-600">{{ $masuk->no_masuk }}</td>
                                        <td class="px-6 py-4 text-slate-700 font-semibold">{{ $masuk->no_surat_jalan }}</td>
                                        <td class="px-6 py-4 text-slate-900 font-medium">{{ $masuk->snapshot_nama_barang ?? ($masuk->barang->nama_barang ?? '-') }}</td>
                                        <td class="px-6 py-4 font-bold text-emerald-600">+{{ $masuk->jumlah }}</td>
                                        <td class="px-6 py-4 text-slate-600">{{ $masuk->user->name ?? '-' }}</td>
                                        <td class="px-6 py-4 text-slate-600">{{ \Carbon\Carbon::parse($masuk->tanggal)->format('d F Y H:i') }}</td>
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
