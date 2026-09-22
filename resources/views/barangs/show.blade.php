<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4 min-w-0">
            <x-back-button />
            <div class="min-w-0">
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    {{ __('Detail Barang: ') }} {{ $barang->nama_barang }}
                </h2>
                <p class="text-xs text-slate-500 mt-1">Kode: <span class="font-mono font-bold text-indigo-600">{{ $barang->kode_barang }}</span></p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-8">
        <!-- Details Grid Card -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden grid grid-cols-1 md:grid-cols-3">
            <div class="p-6 border-b md:border-b-0 md:border-r border-slate-100 space-y-2">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Informasi Produk</p>
                <h3 class="text-xl font-bold text-slate-900">{{ $barang->nama_barang }}</h3>
                <p class="text-xs font-mono font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded w-fit">{{ $barang->kode_barang }}</p>
                <p class="text-xs text-slate-500 mt-2">Kategori: <span class="font-semibold text-slate-800">{{ $barang->category ? $barang->category->name : '-' }}</span></p>
                <p class="text-xs text-slate-500">Satuan: <span class="font-semibold text-slate-800">{{ $barang->unit ? $barang->unit->name : $barang->satuan }}</span></p>
                <p class="text-xs text-slate-500">Status Katalog: 
                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold {{ $barang->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500' }}">
                        {{ $barang->is_active ? 'AKTIF' : 'NONAKTIF' }}
                    </span>
                </p>
            </div>

            <div class="p-6 border-b md:border-b-0 md:border-r border-slate-100 space-y-2">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Status Stok & Rak</p>
                <div class="flex items-baseline space-x-2">
                    <span class="text-3xl font-extrabold {{ $barang->is_low_stock ? 'text-rose-600' : 'text-slate-900' }}">{{ $barang->stok }}</span>
                    <span class="text-xs text-slate-500 font-semibold">{{ $barang->satuan }}</span>
                </div>
                <p class="text-xs text-slate-500">Stok Minimum: <span class="font-semibold text-slate-800">{{ $barang->stok_minimum }} {{ $barang->satuan }}</span></p>
                <p class="text-xs text-slate-500">Lokasi / Rak: 
                    @if($barang->location)
                        <span class="font-mono font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-200">
                            {{ $barang->location->code }} - {{ $barang->location->name }}
                        </span>
                    @else
                        <span class="text-slate-400 italic">Belum ditentukan</span>
                    @endif
                </p>
            </div>

            <div class="p-6 space-y-2">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">FIFO & Batas Umur Simpan</p>
                @if($barang->batas_umur_hari > 0)
                    <div class="flex items-baseline space-x-2">
                        <span class="text-3xl font-extrabold text-indigo-600">{{ $barang->batas_umur_hari }}</span>
                        <span class="text-xs text-slate-500 font-semibold">Hari</span>
                    </div>
                    <p class="text-xs text-slate-500">Sistem memonitor umur simpan batch FIFO secara otomatis.</p>
                @else
                    <div class="text-slate-400 text-xs italic font-medium py-2">
                        Tanpa batas umur simpan (FIFO standar)
                    </div>
                @endif
            </div>
        </div>

        <!-- Expiring Batches -->
        @if($barang->batas_umur_hari > 0)
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
                <h3 class="text-base font-bold text-slate-800 flex items-center space-x-2">
                    <svg class="h-5 w-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Peringatan Umur Batch FIFO</span>
                </h3>
                
                @if($expiringBatches->isEmpty())
                    <p class="text-slate-400 text-xs italic">Semua batch untuk barang ini dalam kondisi aman (tidak mendekati expiry limit).</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    <th class="px-4 py-3">No. Penerimaan</th>
                                    <th class="px-4 py-3">No. Surat Jalan</th>
                                    <th class="px-4 py-3">Tanggal Masuk</th>
                                    <th class="px-4 py-3">Jumlah Masuk</th>
                                    <th class="px-4 py-3">Tanggal Expired</th>
                                    <th class="px-4 py-3">Hari Tersisa</th>
                                    <th class="px-4 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($expiringBatches as $batch)
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-slate-900">{{ $batch->no_masuk }}</td>
                                        <td class="px-4 py-3 text-slate-600">{{ $batch->no_surat_jalan ?? '-' }}</td>
                                        <td class="px-4 py-3 text-slate-600">{{ \Carbon\Carbon::parse($batch->tanggal)->format('d M Y') }}</td>
                                        <td class="px-4 py-3 font-semibold text-slate-800">{{ $batch->jumlah }}</td>
                                        <td class="px-4 py-3 text-slate-600">{{ $batch->expiry_date->format('d M Y') }}</td>
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

        <!-- Transactions History -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Barang Masuk -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
                <h3 class="text-base font-bold text-slate-800 flex items-center space-x-2">
                    <span class="p-1.5 bg-emerald-50 text-emerald-600 rounded-lg">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </span>
                    <span>Riwayat Barang Masuk</span>
                </h3>

                @if($barangMasuk->isEmpty())
                    <p class="text-slate-400 text-xs italic">Belum ada catatan barang masuk.</p>
                @else
                    <div class="overflow-y-auto max-h-80 space-y-3 pr-2">
                        @foreach($barangMasuk as $masuk)
                            <div class="p-3 bg-slate-50/70 rounded-xl border border-slate-100 flex justify-between items-center text-xs">
                                <div>
                                    <span class="font-mono font-bold text-slate-700">{{ $masuk->no_masuk }}</span>
                                    <p class="text-slate-400 mt-0.5">Surat Jalan: {{ $masuk->no_surat_jalan ?? '-' }}</p>
                                    <p class="text-slate-400">Staf: {{ $masuk->user->name ?? '-' }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="px-2.5 py-1 text-xs font-bold bg-emerald-500/10 text-emerald-600 rounded-lg border border-emerald-500/20">+{{ $masuk->jumlah }} {{ $barang->satuan }}</span>
                                    <p class="text-slate-400 mt-1">{{ \Carbon\Carbon::parse($masuk->tanggal)->format('d M Y H:i') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Barang Keluar -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
                <h3 class="text-base font-bold text-slate-800 flex items-center space-x-2">
                    <span class="p-1.5 bg-rose-50 text-rose-600 rounded-lg">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                        </svg>
                    </span>
                    <span>Riwayat Barang Keluar</span>
                </h3>

                @if($barangKeluar->isEmpty())
                    <p class="text-slate-400 text-xs italic">Belum ada catatan barang keluar.</p>
                @else
                    <div class="overflow-y-auto max-h-80 space-y-3 pr-2">
                        @foreach($barangKeluar as $keluar)
                            <div class="p-3 bg-slate-50/70 rounded-xl border border-slate-100 flex justify-between items-center text-xs">
                                <div>
                                    <span class="font-mono font-bold text-slate-700">
                                        {{ $keluar->permintaanBarang ? $keluar->permintaanBarang->no_permintaan : 'RILIS MANUAL' }}
                                    </span>
                                    <p class="text-slate-400 mt-0.5">Pemohon: {{ $keluar->permintaanBarang->user->name ?? 'Manual Gudang' }}</p>
                                    <p class="text-slate-400">Diserahkan oleh: {{ $keluar->user->name ?? '-' }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="px-2.5 py-1 text-xs font-bold bg-rose-500/10 text-rose-600 rounded-lg border border-rose-500/20">-{{ $keluar->jumlah }} {{ $barang->satuan }}</span>
                                    <p class="text-slate-400 mt-1">{{ \Carbon\Carbon::parse($keluar->tanggal)->format('d M Y H:i') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
