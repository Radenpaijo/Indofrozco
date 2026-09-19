<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Barang: ') }} {{ $barang->nama_barang }}
            </h2>
            <a href="{{ route('barangs.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg transition-colors">
                Kembali ke Katalog
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- Details Grid Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden grid grid-cols-1 md:grid-cols-3">
                <div class="p-6 border-b md:border-b-0 md:border-r border-gray-100 space-y-1">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Informasi Dasar</p>
                    <h3 class="text-lg font-bold text-gray-800">{{ $barang->nama_barang }}</h3>
                    <p class="text-sm font-mono text-gray-500">{{ $barang->kode_barang }}</p>
                    <p class="text-sm text-gray-500 mt-2">Satuan: <span class="font-semibold text-gray-800">{{ $barang->satuan }}</span></p>
                    <p class="text-sm text-gray-500">Status: 
                        <span class="inline-block px-2 py-0.5 rounded text-xs font-bold {{ $barang->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-50 text-gray-500' }}">
                            {{ $barang->is_active ? 'AKTIF' : 'NON-AKTIF' }}
                        </span>
                    </p>
                </div>
                <div class="p-6 border-b md:border-b-0 md:border-r border-gray-100 space-y-2">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Status Stok & Gudang</p>
                    <div class="flex items-baseline space-x-2">
                        <span class="text-3xl font-extrabold {{ $barang->is_low_stock ? 'text-rose-600' : 'text-gray-800' }}">{{ $barang->stok }}</span>
                        <span class="text-sm text-gray-500 font-medium">{{ $barang->satuan }}</span>
                    </div>
                    <p class="text-xs text-gray-500">Gudang: <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-800 font-medium text-[11px]">{{ $barang->lokasi_gudang }}</span></p>
                    <p class="text-xs text-gray-500">Posisi Rak: <span class="font-mono font-bold text-indigo-600">{{ $barang->kode_rak ?? 'Belum Ditentukan' }}</span></p>
                </div>
                <div class="p-6 space-y-2">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Lifespan FIFO & Umur Simpan</p>
                    @if($barang->batas_umur_hari > 0)
                        <div class="flex items-baseline space-x-2">
                            <span class="text-3xl font-extrabold text-indigo-600">{{ $barang->batas_umur_hari }}</span>
                            <span class="text-sm text-gray-500 font-medium">Hari</span>
                        </div>
                        <p class="text-xs text-gray-500">Sistem memonitor umur simpan batch FIFO.</p>
                    @else
                        <div class="text-gray-400 text-sm italic font-medium py-2">
                            Tanpa batas umur simpan (FIFO standar)
                        </div>
                    @endif
                </div>
            </div>

            <!-- Expiring Batches (FIFO Warning List) -->
            @if($barang->batas_umur_hari > 0)
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center space-x-2">
                        <svg class="h-5 w-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Peringatan Umur Batch (FIFO Warning Batches)</span>
                    </h3>
                    
                    @if($expiringBatches->isEmpty())
                        <div class="text-gray-500 text-sm py-2">Semua batch untuk barang ini dalam kondisi aman (tidak mendekati expiry limit).</div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-100">
                                <thead>
                                    <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        <th class="px-4 py-3">No. Penerimaan</th>
                                        <th class="px-4 py-3">No. Surat Jalan</th>
                                        <th class="px-4 py-3">Tanggal Masuk</th>
                                        <th class="px-4 py-3">Jumlah Masuk</th>
                                        <th class="px-4 py-3">Tanggal Expired</th>
                                        <th class="px-4 py-3">Hari Tersisa</th>
                                        <th class="px-4 py-3">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-sm">
                                    @foreach($expiringBatches as $batch)
                                        <tr>
                                            <td class="px-4 py-3 font-medium text-gray-900">{{ $batch->no_masuk }}</td>
                                            <td class="px-4 py-3 text-gray-600">{{ $batch->no_surat_jalan ?? '-' }}</td>
                                            <td class="px-4 py-3 text-gray-600">{{ \Carbon\Carbon::parse($batch->tanggal)->format('d M Y') }}</td>
                                            <td class="px-4 py-3 font-semibold text-gray-800">{{ $batch->jumlah }}</td>
                                            <td class="px-4 py-3 text-gray-600">{{ $batch->expiry_date->format('d M Y') }}</td>
                                            <td class="px-4 py-3 font-bold">
                                                @if($batch->days_left < 0)
                                                    <span class="text-rose-600">Expired (Terlewat {{ abs($batch->days_left) }} hari)</span>
                                                @else
                                                    <span class="text-amber-600">{{ $batch->days_left }} hari lagi</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $batch->status === 'expired' ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700' }}">
                                                    {{ strtoupper($batch->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Mutations History Tabs (Two columns or lists) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Barang Masuk (Receipts) -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center space-x-2">
                        <span class="p-1.5 bg-emerald-50 rounded text-emerald-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                        </span>
                        <span>Riwayat Barang Masuk</span>
                    </h3>

                    @if($barangMasuk->isEmpty())
                        <p class="text-gray-500 text-sm py-4">Belum ada catatan barang masuk.</p>
                    @else
                        <div class="overflow-y-auto max-h-96 space-y-4">
                            @foreach($barangMasuk as $masuk)
                                <div class="border-b border-gray-50 pb-3 last:border-0 last:pb-0">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <span class="font-mono text-sm font-semibold text-gray-700">{{ $masuk->no_masuk }}</span>
                                            <p class="text-xs text-gray-400 mt-0.5">Surat Jalan: {{ $masuk->no_surat_jalan ?? '-' }}</p>
                                        </div>
                                        <div class="text-right">
                                            <span class="px-2 py-0.5 text-xs font-bold bg-emerald-50 text-emerald-700 rounded">+{{ $masuk->jumlah }} {{ $barang->satuan }}</span>
                                            <p class="text-xs text-gray-400 mt-1">{{ \Carbon\Carbon::parse($masuk->tanggal)->format('d M Y H:i') }}</p>
                                        </div>
                                    </div>
                                    <div class="flex justify-between items-center text-xs text-gray-400 mt-1">
                                        <span>Staf: {{ $masuk->user->name ?? '-' }}</span>
                                        <span>PO: {{ $masuk->purchaseOrder ? $masuk->purchaseOrder->no_po : 'Manual' }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Barang Keluar (Issues) -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center space-x-2">
                        <span class="p-1.5 bg-rose-50 rounded text-rose-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                            </svg>
                        </span>
                        <span>Riwayat Barang Keluar</span>
                    </h3>

                    @if($barangKeluar->isEmpty())
                        <p class="text-gray-500 text-sm py-4">Belum ada catatan barang keluar.</p>
                    @else
                        <div class="overflow-y-auto max-h-96 space-y-4">
                            @foreach($barangKeluar as $keluar)
                                <div class="border-b border-gray-50 pb-3 last:border-0 last:pb-0">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <span class="font-mono text-sm font-semibold text-gray-700">
                                                {{ $keluar->permintaanBarang ? $keluar->permintaanBarang->no_permintaan : 'RILIS MANUAL' }}
                                            </span>
                                            <p class="text-xs text-gray-400 mt-0.5">Pemohon: {{ $keluar->permintaanBarang->user->name ?? 'Manual Gudang' }}</p>
                                        </div>
                                        <div class="text-right">
                                            <span class="px-2 py-0.5 text-xs font-bold bg-rose-50 text-rose-700 rounded">-{{ $keluar->jumlah }} {{ $barang->satuan }}</span>
                                            <p class="text-xs text-gray-400 mt-1">{{ \Carbon\Carbon::parse($keluar->tanggal)->format('d M Y H:i') }}</p>
                                        </div>
                                    </div>
                                    <div class="flex justify-between items-center text-xs text-gray-400 mt-1">
                                        <span>Diserahkan: {{ $keluar->user->name ?? '-' }}</span>
                                        <span>Status: Selesai</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
