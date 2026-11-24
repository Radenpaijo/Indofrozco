<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4 min-w-0">
            <x-back-button />
            <div class="min-w-0">
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    {{ __('Detail Permintaan Barang: ') }} {{ $permintaan->no_permintaan }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="w-full space-y-6">

            <!-- Request Card Header -->
            <div class="bg-white rounded-lg shadow-sm border border-slate-300 p-6 flex flex-wrap justify-between items-center gap-4">
                <div class="space-y-1">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Nomor Pengajuan</p>
                    <h3 class="text-xl font-bold text-gray-800 font-mono">{{ $permintaan->no_permintaan }}</h3>
                    <p class="text-sm text-gray-500">Tanggal: <span class="font-medium text-gray-700">{{ \Carbon\Carbon::parse($permintaan->tanggal)->format('d F Y H:i') }}</span></p>
                    <p class="text-sm text-gray-500">Diajukan Oleh: <span class="font-medium text-gray-700">{{ $permintaan->user->name ?? '-' }} (PRODUKSI)</span></p>
                </div>

                <div class="flex flex-col items-end gap-2">
                    <span class="px-3 py-1 text-xs font-semibold rounded bg-slate-100 text-slate-700 border border-slate-300">
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
            <div class="bg-white rounded-lg shadow-sm border border-slate-300 overflow-hidden">
                <div class="p-4 border-b border-slate-200 flex items-center justify-between">
                    <div>
                        <h4 class="text-md font-bold text-slate-800">Daftar Item & Rekomendasi Batch FEFO</h4>
                    </div>
                    <a href="{{ route('permintaan-barang.print', $permintaan->id) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 rounded-lg transition shadow-sm">
                        <svg class="h-3.5 w-3.5 mr-1 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Cetak Bukti
                    </a>
                </div>
                <div class="md:hidden p-4 space-y-3">
                    @foreach($permintaan->details as $detail)
                        @php
                            $barang = $detail->barang;
                            $batches = $barang->batches ?? collect();
                            $remaining = $detail->jumlah;
                            $fefoPicks = [];
                            foreach ($batches as $b) {
                                if ($remaining <= 0) break;
                                $take = min((float)$b->stok, $remaining);
                                $fefoPicks[] = [
                                    'lot' => $b->batch_number,
                                    'qty' => $take,
                                    'ed' => $b->tanggal_kadaluarsa ? \Carbon\Carbon::parse($b->tanggal_kadaluarsa)->format('d/m/Y') : null,
                                ];
                                $remaining -= $take;
                            }
                        @endphp
                        <div class="p-3.5 rounded-xl bg-slate-50/70 border border-slate-200/80 space-y-2.5 text-xs">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <div class="font-bold text-slate-900 text-sm">{{ $detail->barang->nama_barang }}</div>
                                    <div class="font-mono text-[11px] text-slate-500">{{ $detail->barang->kode_barang }}</div>
                                </div>
                                @if($detail->barang->stok >= $detail->jumlah)
                                    <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 text-[11px] font-bold border border-emerald-200 shrink-0">Tersedia (Sisa: {{ $detail->barang->stok }})</span>
                                @else
                                    <span class="px-2 py-0.5 rounded bg-rose-50 text-rose-700 text-[11px] font-bold border border-rose-200 shrink-0">Kurang {{ $detail->jumlah - $detail->barang->stok }}</span>
                                @endif
                            </div>
                            <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-200/70">
                                <div>
                                    <span class="text-[10px] text-slate-400 uppercase block">Gudang & Rak</span>
                                    <span class="font-medium text-slate-800">{{ $detail->barang->lokasi_gudang ?? 'Cold Storage' }}</span>
                                    @if($detail->barang->kode_rak)
                                        <span class="font-mono text-[11px] text-indigo-600 font-bold block">Rak {{ $detail->barang->kode_rak }}</span>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <span class="text-[10px] text-slate-400 uppercase block">Jumlah Diminta</span>
                                    <span class="font-bold text-slate-900 text-sm">{{ $detail->jumlah }} {{ $detail->barang->satuan }}</span>
                                </div>
                            </div>
                            <div class="pt-2 border-t border-slate-200/70">
                                <span class="text-[10px] text-slate-400 uppercase block mb-1">Alokasi Batch FEFO</span>
                                @if(!empty($fefoPicks))
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($fefoPicks as $pick)
                                            <span class="px-2 py-1 rounded bg-white border border-slate-200 font-mono text-[11px] text-slate-800">
                                                <strong>#{{ $pick['lot'] }}</strong>: {{ $pick['qty'] }} {{ $barang->satuan }}
                                                @if($pick['ed'])
                                                    <span class="text-amber-700 font-bold ml-1">(ED: {{ $pick['ed'] }})</span>
                                                @endif
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-slate-400 italic">Tidak ada batch aktif</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-100 border-b border-slate-300 text-[11px] font-bold uppercase tracking-wider text-slate-700">
                                <th class="py-3.5 px-4 border-r border-slate-300">Kode Barang</th>
                                <th class="py-3.5 px-4 border-r border-slate-300">Nama Barang</th>
                                <th class="py-3.5 px-4 border-r border-slate-300">Gudang & Rak</th>
                                <th class="py-3.5 px-4 border-r border-slate-300">Alokasi Batch FEFO (Prioritas ED)</th>
                                <th class="py-3.5 px-4 border-r border-slate-300 text-center">Jumlah Diminta</th>
                                <th class="py-3.5 px-4 border-r border-slate-300">Status Stok</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 text-sm">
                            @foreach($permintaan->details as $detail)
                                @php
                                    $barang = $detail->barang;
                                    $batches = $barang->batches ?? collect();
                                    $remaining = $detail->jumlah;
                                    $fefoPicks = [];
                                    foreach ($batches as $b) {
                                        if ($remaining <= 0) break;
                                        $take = min((float)$b->stok, $remaining);
                                        $fefoPicks[] = [
                                            'lot' => $b->batch_number,
                                            'qty' => $take,
                                            'ed' => $b->tanggal_kadaluarsa ? \Carbon\Carbon::parse($b->tanggal_kadaluarsa)->format('d/m/Y') : null,
                                        ];
                                        $remaining -= $take;
                                    }
                                @endphp
                                <tr class="hover:bg-slate-50">
                                    <td class="py-3.5 px-4 border-r border-slate-200 font-mono text-slate-600 text-xs">{{ $detail->barang->kode_barang }}</td>
                                    <td class="py-3.5 px-4 border-r border-slate-200 font-bold text-slate-900">{{ $detail->barang->nama_barang }}</td>
                                    <td class="py-3.5 px-4 border-r border-slate-200 text-slate-600 text-xs">
                                        <div class="flex flex-col">
                                            <span class="font-medium text-slate-800">{{ $detail->barang->lokasi_gudang ?? 'Cold Storage' }}</span>
                                            @if($detail->barang->kode_rak)
                                                <span class="font-mono text-xs text-indigo-600 font-bold mt-0.5">Rak {{ $detail->barang->kode_rak }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 border-r border-slate-200 text-xs">
                                        @if(!empty($fefoPicks))
                                            <div class="flex flex-wrap gap-1.5">
                                                @foreach($fefoPicks as $pick)
                                                    <span class="px-2 py-1 rounded bg-slate-100 border border-slate-300 font-mono text-[11px] text-slate-800">
                                                        <strong>#{{ $pick['lot'] }}</strong>: {{ $pick['qty'] }} {{ $barang->satuan }}
                                                        @if($pick['ed'])
                                                            <span class="text-amber-700 font-bold ml-1">(ED: {{ $pick['ed'] }})</span>
                                                        @endif
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-slate-400 italic">Tidak ada batch aktif</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 border-r border-slate-200 font-bold text-slate-900 text-center">{{ $detail->jumlah }} {{ $detail->barang->satuan }}</td>
                                    <td class="py-3.5 px-4 border-r border-slate-200">
                                        @if($detail->barang->stok >= $detail->jumlah)
                                            <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-200">Tersedia (Sisa: {{ $detail->barang->stok }})</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded bg-rose-50 text-rose-700 text-xs font-bold border border-rose-200">Kurang {{ $detail->jumlah - $detail->barang->stok }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- QC DEFROST & SUSUT BERAT (FRM.JFD.01.10 Rev. 0) -->
            @if($permintaan->status === 'selesai')
                @php
                    $totalBeratKeluar = (float) $permintaan->barangKeluars->sum('jumlah');
                    $qc = $permintaan->qcInspection;
                @endphp

                @if($qc)
                    <!-- Hasil QC Defrost Selesai -->
                    <div class="bg-white rounded-lg shadow-sm border border-slate-300 overflow-hidden">
                        <div class="bg-slate-50 border-b border-slate-200 p-4 sm:px-6 flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-black bg-blue-600 text-white uppercase tracking-wider">
                                    FRM.JFD.01.10 Rev. 0
                                </span>
                                <div>
                                    <h4 class="text-sm sm:text-base font-bold text-slate-900">Formulir Pengecekan Bahan Baku Sesudah Defrost</h4>
                                    <p class="text-xs text-slate-500">
                                        Diperiksa: {{ \Carbon\Carbon::parse($qc->tanggal_cek)->format('d M Y') }} {{ $qc->jam_cek }} &bull; Pemeriksa: <strong class="text-slate-700">{{ $qc->user->name ?? '-' }} (Tim Produksi)</strong>
                                    </p>
                                </div>
                            </div>
                            <div>
                                @if($qc->status_qc === 'pass')
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                        ✓ LOLOS DEFROST (PASS)
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold bg-rose-100 text-rose-800 border border-rose-300">
                                        ✕ DITOLAK / RETUR (FAIL)
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="p-6 space-y-4">
                            <!-- Ringkasan Timbangan & Susut Berat -->
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200">
                                    <span class="text-[10px] uppercase font-bold text-slate-500 block">Berat Keluar Gudang</span>
                                    <span class="text-lg font-black text-slate-800 font-mono mt-0.5 block">{{ number_format($qc->berat_keluar_gudang, 2) }} Kg</span>
                                </div>
                                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200">
                                    <span class="text-[10px] uppercase font-bold text-slate-500 block">Berat Riil Diterima</span>
                                    <span class="text-lg font-black text-indigo-700 font-mono mt-0.5 block">{{ number_format($qc->berat_riil_diterima, 2) }} Kg</span>
                                </div>
                                <div class="p-3.5 {{ ($qc->susut_persen > 3) ? 'bg-amber-50 border-amber-300' : 'bg-slate-50 border-slate-200' }} rounded-xl border">
                                    <span class="text-[10px] uppercase font-bold text-slate-500 block">Susut (Drip Loss)</span>
                                    <span class="text-lg font-black {{ ($qc->susut_persen > 3) ? 'text-amber-700' : 'text-slate-800' }} font-mono mt-0.5 block">
                                        {{ number_format($qc->susut_kg, 2) }} Kg <span class="text-xs font-bold">({{ number_format($qc->susut_persen, 1) }}%)</span>
                                    </span>
                                </div>
                                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200">
                                    <span class="text-[10px] uppercase font-bold text-slate-500 block">Suhu Sesudah Defrost</span>
                                    <span class="text-lg font-black text-slate-800 font-mono mt-0.5 block">{{ $qc->suhu !== null ? $qc->suhu . ' °C' : '-' }}</span>
                                </div>
                            </div>

                            <!-- Organoleptik Check -->
                            <div class="grid grid-cols-3 gap-3 text-xs">
                                <div class="p-3 rounded-lg border border-slate-200 bg-slate-50 flex items-center justify-between">
                                    <span class="font-bold text-slate-700">Bau / Aroma</span>
                                    <span class="{{ $qc->kondisi_aroma ? 'text-emerald-700 font-bold' : 'text-rose-600 font-bold' }}">
                                        {{ $qc->kondisi_aroma ? '✓ Normal' : '✕ Tidak Normal' }}
                                    </span>
                                </div>
                                <div class="p-3 rounded-lg border border-slate-200 bg-slate-50 flex items-center justify-between">
                                    <span class="font-bold text-slate-700">Tekstur</span>
                                    <span class="{{ $qc->kondisi_tekstur ? 'text-emerald-700 font-bold' : 'text-rose-600 font-bold' }}">
                                        {{ $qc->kondisi_tekstur ? '✓ Normal' : '✕ Tidak Normal' }}
                                    </span>
                                </div>
                                <div class="p-3 rounded-lg border border-slate-200 bg-slate-50 flex items-center justify-between">
                                    <span class="font-bold text-slate-700">Penampakan</span>
                                    <span class="{{ $qc->kondisi_penampakan ? 'text-emerald-700 font-bold' : 'text-rose-600 font-bold' }}">
                                        {{ $qc->kondisi_penampakan ? '✓ Normal' : '✕ Tidak Normal' }}
                                    </span>
                                </div>
                            </div>

                            @if($qc->catatan || $qc->tindakan_koreksi)
                                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs space-y-1">
                                    @if($qc->catatan)
                                        <div><strong class="text-slate-700">Catatan:</strong> <span class="text-slate-600">{{ $qc->catatan }}</span></div>
                                    @endif
                                    @if($qc->tindakan_koreksi)
                                        <div><strong class="text-slate-700">Tindakan Koreksi:</strong> <span class="text-slate-600">{{ $qc->tindakan_koreksi }}</span></div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <!-- Form Verifikasi QC Defrost & Timbangan Terima -->
                    <div class="bg-white rounded-lg shadow-sm border-2 border-blue-300 overflow-hidden" x-data="{
                        beratKeluar: {{ $totalBeratKeluar }},
                        beratRiil: {{ $totalBeratKeluar }},
                        get susutKg() {
                            const s = (this.beratKeluar - (parseFloat(this.beratRiil) || 0));
                            return s > 0 ? s.toFixed(2) : '0.00';
                        },
                        get susutPersen() {
                            if (this.beratKeluar <= 0) return '0.0';
                            const p = ((this.beratKeluar - (parseFloat(this.beratRiil) || 0)) / this.beratKeluar) * 100;
                            return p > 0 ? p.toFixed(2) : '0.0';
                        }
                    }">
                        <div class="bg-blue-50/80 border-b border-blue-200 p-4 sm:px-6 flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-black bg-blue-600 text-white uppercase tracking-wider">
                                    FRM.JFD.01.10 Rev. 0
                                </span>
                                <div>
                                    <h4 class="text-sm sm:text-base font-bold text-slate-900">Verifikasi QC Defrost & Serah Terima Produksi</h4>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                Menunggu Verifikasi Produksi
                            </span>
                        </div>

                        <form method="POST" action="{{ route('permintaan-barang.verify-qc', $permintaan->id) }}" class="p-6 space-y-6">
                            @csrf

                            <!-- Timbangan & Kalkulasi Susut Otomatis -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                        1. Berat Keluar Gudang (Kg)
                                    </label>
                                    <div class="h-10 px-3 bg-slate-100 border border-slate-300 rounded-lg text-sm font-black text-slate-900 flex items-center justify-between">
                                        <span>{{ number_format($totalBeratKeluar, 2) }}</span>
                                        <span class="text-xs text-slate-500 font-bold">Kg</span>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                        2. Berat Riil Terima Produksi (Kg) <span class="text-rose-500 font-bold">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="number" step="0.01" name="berat_riil_diterima" x-model="beratRiil" required min="0.01" class="h-10 w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm font-black text-slate-900 pr-10">
                                        <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-xs font-bold text-slate-400">
                                            Kg
                                        </span>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                        3. Susut Berat (Drip Loss)
                                    </label>
                                    <div class="h-10 px-3 bg-amber-50 border border-amber-300 rounded-lg text-sm font-black text-amber-900 flex items-center justify-between">
                                        <span x-text="susutKg + ' Kg'">0.00 Kg</span>
                                        <span class="text-xs font-bold text-amber-700" x-text="'(' + susutPersen + '%)'">(0%)</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Organoleptik & Suhu -->
                            <div class="pt-4 border-t border-slate-200">
                                <h5 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-3">4. Parameter Pemeriksaan Fisik & Suhu Defrost</h5>
                                <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                                    <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 bg-slate-50/60 hover:bg-white cursor-pointer transition-colors">
                                        <input type="checkbox" name="kondisi_aroma" value="1" checked class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                        <div class="text-xs">
                                            <span class="font-bold text-slate-800 block">Bau / Aroma</span>
                                            <span class="text-slate-500 text-[11px]">Normal khas bahan</span>
                                        </div>
                                    </label>
                                    <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 bg-slate-50/60 hover:bg-white cursor-pointer transition-colors">
                                        <input type="checkbox" name="kondisi_tekstur" value="1" checked class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                        <div class="text-xs">
                                            <span class="font-bold text-slate-800 block">Tekstur</span>
                                            <span class="text-slate-500 text-[11px]">Kenyal tidak lembek</span>
                                        </div>
                                    </label>
                                    <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 bg-slate-50/60 hover:bg-white cursor-pointer transition-colors">
                                        <input type="checkbox" name="kondisi_penampakan" value="1" checked class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                        <div class="text-xs">
                                            <span class="font-bold text-slate-800 block">Penampakan</span>
                                            <span class="text-slate-500 text-[11px]">Warna segar normal</span>
                                        </div>
                                    </label>
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">
                                            Suhu Defrost (°C) <span class="text-rose-500 font-bold">*</span>
                                        </label>
                                        <div class="relative">
                                            <input type="number" step="0.1" name="suhu" value="-12.0" required class="h-10 w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-xs font-bold pr-8">
                                            <span class="absolute inset-y-0 right-0 flex items-center pr-2.5 pointer-events-none text-xs font-bold text-slate-400">°C</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Keputusan & Catatan -->
                            <div class="pt-4 border-t border-slate-200 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                        Keputusan Hasil QC Defrost <span class="text-rose-500 font-bold">*</span>
                                    </label>
                                    <select name="status_qc" class="h-10 w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-xs font-bold text-slate-900">
                                        <option value="pass" class="text-emerald-700 font-bold">✓ LOLOS / SIAP PROSES (PASS)</option>
                                        <option value="fail" class="text-rose-700 font-bold">✕ DITOLAK / RETUR (FAIL)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                        Catatan / Keterangan & Tindakan
                                    </label>
                                    <input type="text" name="catatan" placeholder="Contoh: Defrost sempurna, siap masuk proses olah" class="h-10 w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-xs">
                                </div>
                            </div>

                            <div class="pt-4 border-t border-slate-200 flex items-center justify-end">
                                <x-button type="submit" variant="indigo" class="px-6 py-2.5 text-xs font-bold">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Simpan Verifikasi QC Defrost
                                </x-button>
                            </div>
                        </form>
                    </div>
                @endif
            @endif

            <!-- Linked Release Log -->
            @if($permintaan->barangKeluars->isNotEmpty())
                <div class="bg-white rounded-lg shadow-sm border border-slate-300 overflow-hidden">
                    <div class="p-4 border-b border-slate-200">
                        <h4 class="text-md font-bold text-slate-800">Log Mutasi Keluar Terkait (Audit Trail)</h4>
                    </div>
                    <div class="md:hidden p-4 space-y-2.5">
                        @foreach($permintaan->barangKeluars as $keluar)
                            <div class="p-3.5 rounded-xl bg-slate-50/70 border border-slate-200/80 space-y-1.5 text-xs">
                                <div class="flex items-start justify-between gap-2">
                                    <span class="font-bold text-slate-900">{{ $keluar->snapshot_nama_barang ?? ($keluar->barang->nama_barang ?? '-') }}</span>
                                    <span class="font-bold text-rose-600 shrink-0">-{{ $keluar->jumlah }} {{ $keluar->barang->satuan ?? '' }}</span>
                                </div>
                                <div class="flex items-center justify-between text-[11px] text-slate-500 pt-1 border-t border-slate-200/60">
                                    <span>Oleh: {{ $keluar->user->name ?? '-' }}</span>
                                    <span>{{ \Carbon\Carbon::parse($keluar->tanggal)->format('d M Y H:i') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead>
                                <tr class="bg-slate-100 border-b border-slate-300 text-[11px] font-bold uppercase tracking-wider text-slate-700">
                                    <th class="py-3.5 px-4 border-r border-slate-300">Barang</th>
                                    <th class="py-3.5 px-4 border-r border-slate-300">Jumlah Keluar</th>
                                    <th class="py-3.5 px-4 border-r border-slate-300">Diserahkan Oleh</th>
                                    <th class="py-3.5 px-4">Tanggal Penyerahan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @foreach($permintaan->barangKeluars as $keluar)
                                    <tr class="hover:bg-slate-50">
                                        <td class="py-3.5 px-4 border-r border-slate-200 font-medium text-slate-900">{{ $keluar->snapshot_nama_barang ?? ($keluar->barang->nama_barang ?? '-') }}</td>
                                        <td class="py-3.5 px-4 border-r border-slate-200 font-bold text-rose-600">-{{ $keluar->jumlah }} {{ $keluar->barang->satuan ?? '' }}</td>
                                        <td class="py-3.5 px-4 border-r border-slate-200 text-slate-600">{{ $keluar->user->name ?? '-' }}</td>
                                        <td class="py-3.5 px-4 text-slate-600">{{ \Carbon\Carbon::parse($keluar->tanggal)->format('d F Y H:i') }}</td>
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
