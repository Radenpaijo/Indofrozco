<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4 min-w-0">
            <x-back-button />
            <div class="min-w-0">
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    {{ __('Detail Permintaan Barang: ') }} {{ $permintaan->no_permintaan }}
                </h2>
                <p class="text-xs text-slate-500 mt-1">Status dan rincian alokasi barang produksi.</p>
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
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Nomor Pengajuan</p>
                    <h3 class="text-xl font-bold text-gray-800 font-mono">{{ $permintaan->no_permintaan }}</h3>
                    <p class="text-sm text-gray-500">Tanggal: <span class="font-medium text-gray-700">{{ \Carbon\Carbon::parse($permintaan->tanggal)->format('d F Y H:i') }}</span></p>
                    <p class="text-sm text-gray-500">Diajukan Oleh: <span class="font-medium text-gray-700">{{ $permintaan->user->name ?? '-' }} (PRODUKSI)</span></p>
                </div>

                <div class="flex flex-col items-end gap-2">
                    <span class="px-3 py-1 text-sm font-bold rounded-full 
                        @if($permintaan->status === 'pending') bg-amber-50 text-amber-700
                        @elseif($permintaan->status === 'selesai') bg-emerald-50 text-emerald-700
                        @elseif($permintaan->status === 'ditolak') bg-rose-50 text-rose-700
                        @else bg-gray-50 text-gray-600
                        @endif">
                        {{ strtoupper($permintaan->status) }}
                    </span>

                    <!-- Gudang / Admin Action Controls -->
                    @if($permintaan->status === 'pending' && auth()->user()->hasRole(['admin', 'gudang']))
                        <div class="flex space-x-2 mt-2">
                            <!-- Reject Modal -->
                            <div x-data="{ open: false }">
                                <button type="button" @click="open = true" class="inline-flex items-center px-4 py-2 border border-rose-600 hover:bg-rose-50 text-rose-600 text-sm font-semibold rounded-lg transition-colors">
                                    Tolak Permintaan
                                </button>
                                <div x-show="open" x-cloak x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @keydown.escape.window="open = false">
                                    <div x-show="open" x-transition.scale @click.outside="open = false" class="w-full max-w-md rounded-xl bg-white shadow-2xl">
                                        <div class="flex items-center justify-between border-b px-6 py-4">
                                            <h3 class="text-lg font-semibold text-gray-900">Konfirmasi Penolakan</h3>
                                            <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
                                        </div>
                                        <div class="px-6 py-5">
                                            <p class="text-sm text-gray-600">Apakah Anda yakin ingin <span class="font-bold text-rose-600">MENOLAK</span> permintaan {{ $permintaan->no_permintaan }}?</p>
                                        </div>
                                        <div class="flex justify-end gap-3 border-t bg-gray-50 px-6 py-4 rounded-b-xl">
                                            <button type="button" @click="open = false" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100">Batal</button>
                                            <form method="POST" action="{{ route('permintaan-barang.reject', $permintaan->id) }}">
                                                @csrf
                                                <button type="submit" class="rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700">Ya, Tolak</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Approve Modal -->
                            <div x-data="{ open: false }">
                                <button type="button" @click="open = true" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow transition-colors">
                                    Setujui & Rilis Stok
                                </button>
                                <div x-show="open" x-cloak x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @keydown.escape.window="open = false">
                                    <div x-show="open" x-transition.scale @click.outside="open = false" class="w-full max-w-md rounded-xl bg-white shadow-2xl">
                                        <div class="flex items-center justify-between border-b px-6 py-4">
                                            <h3 class="text-lg font-semibold text-gray-900">Konfirmasi Persetujuan</h3>
                                            <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
                                        </div>
                                        <div class="px-6 py-5">
                                            <p class="text-sm text-gray-600">Apakah Anda yakin ingin <span class="font-bold text-emerald-600">MENYETUJUI</span> permintaan ini? Persetujuan akan otomatis memotong stok fisik.</p>
                                        </div>
                                        <div class="flex justify-end gap-3 border-t bg-gray-50 px-6 py-4 rounded-b-xl">
                                            <button type="button" @click="open = false" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100">Batal</button>
                                            <form method="POST" action="{{ route('permintaan-barang.approve', $permintaan->id) }}">
                                                @csrf
                                                <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Ya, Setujui & Rilis</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Items List -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-50">
                    <h4 class="text-md font-bold text-gray-800">Daftar Item Detail</h4>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead>
                            <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <th class="px-6 py-4">Kode Barang</th>
                                <th class="px-6 py-4">Nama Barang</th>
                                <th class="px-6 py-4">Gudang Penyimpanan</th>
                                <th class="px-6 py-4">Stok Saat Ini</th>
                                <th class="px-6 py-4">Jumlah Diminta</th>
                                <th class="px-6 py-4">Status Ketersediaan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @foreach($permintaan->details as $detail)
                                <tr>
                                    <td class="px-6 py-4 font-mono text-gray-600">{{ $detail->barang->kode_barang }}</td>
                                    <td class="px-6 py-4 font-medium text-gray-900">{{ $detail->barang->nama_barang }}</td>
                                    <td class="px-6 py-4 text-gray-600">
                                        <div class="flex flex-col">
                                            <span class="font-medium text-slate-800">{{ $detail->barang->lokasi_gudang }}</span>
                                            @if($detail->barang->kode_rak)
                                                <span class="font-mono text-xs text-indigo-600 font-bold mt-0.5">{{ $detail->barang->kode_rak }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">{{ $detail->barang->stok }} {{ $detail->barang->satuan }}</td>
                                    <td class="px-6 py-4 font-bold text-gray-800">{{ $detail->jumlah }} {{ $detail->barang->satuan }}</td>
                                    <td class="px-6 py-4">
                                        @if($detail->barang->stok >= $detail->jumlah)
                                            <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 text-xs font-semibold">Tersedia</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded bg-rose-50 text-rose-700 text-xs font-semibold">Stok Kurang (Kurang {{ $detail->jumlah - $detail->barang->stok }})</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Linked Release Log -->
            @if($permintaan->barangKeluars->isNotEmpty())
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-50">
                        <h4 class="text-md font-bold text-gray-800">Log Mutasi Keluar Terkait (Audit Trail)</h4>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 text-sm">
                            <thead>
                                <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    <th class="px-6 py-4">Barang</th>
                                    <th class="px-6 py-4">Jumlah Keluar</th>
                                    <th class="px-6 py-4">Diserahkan Oleh</th>
                                    <th class="px-6 py-4">Tanggal Penyerahan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($permintaan->barangKeluars as $keluar)
                                    <tr>
                                        <td class="px-6 py-4 font-medium text-gray-900">{{ $keluar->snapshot_nama_barang ?? ($keluar->barang->nama_barang ?? '-') }}</td>
                                        <td class="px-6 py-4 font-bold text-rose-600">-{{ $keluar->jumlah }} {{ $keluar->barang->satuan ?? '' }}</td>
                                        <td class="px-6 py-4 text-gray-600">{{ $keluar->user->name ?? '-' }}</td>
                                        <td class="px-6 py-4 text-gray-600">{{ \Carbon\Carbon::parse($keluar->tanggal)->format('d F Y H:i') }}</td>
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
