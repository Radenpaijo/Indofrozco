<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3 sm:gap-4 min-w-0">
            <x-back-button :href="route('barang-masuk.index')" />
            <h2 class="text-2xl font-bold text-[#1e295d] tracking-tight truncate">
                {{ __('Penerimaan Barang') }}
            </h2>
        </div>
    </x-slot>

    <!-- 1 Card Utama (Full Width, Tanpa Ditumpuk-tumpuk) -->
    <div class="w-full bg-white rounded-2xl border border-slate-200/80 shadow-2xs p-5 sm:p-8 space-y-6">
        <form id="barang-masuk-form" method="POST" action="{{ route('barang-masuk.store') }}" class="space-y-6">
            @csrf

            <!-- Section 1: Informasi Dasar & PO -->
            <div class="space-y-4">
                <!-- PO Selector -->
                <div>
                    <label for="po_selector" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Hubungkan Purchase Order (PO)
                    </label>
                    <select id="po_selector" class="h-11 w-full rounded-xl border-slate-300 bg-white text-slate-800 text-xs sm:text-sm font-medium focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition" onchange="window.location.href = '{{ route('barang-masuk.create') }}?po_id=' + this.value">
                        <option value="">-- Input Manual (Tanpa PO) --</option>
                        @foreach($activePOs as $po)
                            <option value="{{ $po->id }}" {{ request('po_id') == $po->id ? 'selected' : '' }}>
                                {{ $po->no_po }} - {{ $po->supplier->nama_supplier ?? '-' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Nomor Surat Jalan -->
                <div>
                    <label for="no_surat_jalan" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Nomor Surat Jalan <span class="text-rose-500 font-bold">*</span>
                    </label>
                    <input id="no_surat_jalan" class="h-11 w-full rounded-xl border-slate-300 bg-white text-slate-800 text-sm placeholder:text-slate-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition" type="text" name="no_surat_jalan" value="{{ old('no_surat_jalan') }}" required placeholder="Contoh: SJ-998822" autofocus />
                    <x-input-error :messages="$errors->get('no_surat_jalan')" class="mt-1" />
                </div>

                <!-- Tanggal Masuk Gudang -->
                <div>
                    <label for="tanggal" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Tanggal Masuk Gudang <span class="text-rose-500 font-bold">*</span>
                    </label>
                    <input id="tanggal" class="h-11 w-full rounded-xl border-slate-300 bg-white text-slate-800 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition" type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required />
                    <x-input-error :messages="$errors->get('tanggal')" class="mt-1" />
                </div>
            </div>

            @if($selectedPO)
                <input type="hidden" name="po_id" value="{{ $selectedPO->id }}">
                <div class="bg-slate-50 border border-slate-200 text-slate-800 px-4 py-3 rounded-xl flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center justify-center px-2 py-0.5 rounded text-[11px] font-bold bg-blue-600 text-white">PO</span>
                        <span class="text-xs font-bold">{{ $selectedPO->no_po }} &bull; {{ $selectedPO->supplier->nama_supplier ?? '-' }}</span>
                    </div>
                    <a href="{{ route('barang-masuk.create') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800 underline">Ganti / Lepas PO</a>
                </div>
            @endif

            <!-- Section 2: Daftar Item Barang (Tabel di dalam kartu utama) -->
            <div class="space-y-3 pt-2">
                <div>
                    <h3 class="text-sm font-bold text-[#1e295d]">Item Penerimaan & Identitas Karung</h3>
                </div>

                <div class="w-full lg:overflow-x-auto lg:border lg:border-slate-200 lg:rounded-xl">
                    <table class="w-full text-left border-collapse block lg:table responsive-card-lg" id="items-table">
                        <thead class="hidden lg:table-header-group">
                            <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-600 whitespace-nowrap">
                                <th class="py-3 px-4 border-r border-slate-200 min-w-[240px]">Barang <span class="text-rose-500 font-bold">*</span></th>
                                <th class="py-3 px-4 border-r border-slate-200 min-w-[180px]">Lokasi Rak <span class="text-rose-500 font-bold">*</span></th>
                                <th class="py-3 px-4 border-r border-slate-200 min-w-[140px]">No. Batch</th>
                                <th class="py-3 px-4 border-r border-slate-200 min-w-[140px]">Tgl Expired</th>
                                <th class="py-3 px-4 border-r border-slate-200 w-28 text-center">Jml Pack</th>
                                @if($selectedPO)
                                    <th class="py-3 px-4 border-r border-slate-200 text-center w-28">Qty Pesan</th>
                                    <th class="py-3 px-4 border-r border-slate-200 text-center w-28">Diterima</th>
                                    <th class="py-3 px-4 border-r border-slate-200 text-center min-w-[150px]">Qty Diterima <span class="text-rose-500 font-bold">*</span></th>
                                @else
                                    <th class="py-3 px-4 border-r border-slate-200 text-center min-w-[150px]">Berat Netto (Kg) <span class="text-rose-500 font-bold">*</span></th>
                                @endif
                                <th class="py-3 px-4 text-center w-16">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="block lg:table-row-group lg:divide-y lg:divide-slate-200 text-sm space-y-4 lg:space-y-0" id="items-tbody">
                            @if($selectedPO && $selectedPO->details->isNotEmpty())
                                @foreach($selectedPO->details as $idx => $d)
                                    @php 
                                        $alreadyReceived = $selectedPO->barangMasuks->where('barang_id', $d->barang_id)->sum('jumlah');
                                        $remaining = max(0, $d->jumlah - $alreadyReceived);
                                        $unitLabel = $d->barang->unit->name ?? $d->barang->satuan ?? '';
                                    @endphp
                                    <tr class="item-row grid grid-cols-2 gap-3 p-3.5 sm:p-4 bg-slate-50/50 rounded-xl border border-slate-200/70 lg:table-row lg:p-0 lg:border-0 lg:rounded-none lg:bg-transparent lg:gap-0 hover:bg-slate-50/70 transition-colors">
                                        <td class="col-span-2 block lg:hidden p-0">
                                            <div class="flex items-center justify-between pb-2 mb-1 border-b border-slate-200/70">
                                                <span class="text-xs font-bold text-slate-800 font-sans item-badge">Item #{{ $idx + 1 }}</span>
                                                <button type="button" class="text-xs font-semibold text-rose-500 hover:text-rose-700 remove-row-btn transition">
                                                    Hapus
                                                </button>
                                            </div>
                                        </td>
                                        <td class="col-span-2 lg:col-auto p-0 lg:p-3 align-middle border-0 lg:border-r border-slate-200">
                                            <label class="block lg:hidden text-xs font-semibold text-slate-700 mb-1">
                                                Barang <span class="text-rose-500 font-bold">*</span>
                                            </label>
                                            <select name="items[{{ $idx }}][barang_id]" required class="h-11 lg:h-10 w-full rounded-xl lg:rounded-lg border-slate-300 bg-white text-xs font-semibold focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                                @foreach($barangs as $barang)
                                                    <option value="{{ $barang->id }}" {{ $d->barang_id == $barang->id ? 'selected' : '' }}>
                                                        {{ $barang->kode_barang }} - {{ $barang->nama_barang }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="col-span-2 lg:col-auto p-0 lg:p-3 align-middle border-0 lg:border-r border-slate-200">
                                            <label class="block lg:hidden text-xs font-semibold text-slate-700 mb-1">
                                                Lokasi Rak <span class="text-rose-500 font-bold">*</span>
                                            </label>
                                            <select name="items[{{ $idx }}][location_id]" class="location-select h-11 lg:h-10 w-full rounded-xl lg:rounded-lg border-slate-300 bg-white text-xs font-medium focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                                <option value="">-- Pilih Rak --</option>
                                                @foreach($locations as $loc)
                                                    <option value="{{ $loc->id }}" {{ ($d->barang->location_id ?? null) == $loc->id ? 'selected' : '' }}>
                                                        {{ $loc->code }} - {{ $loc->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="col-span-1 lg:col-auto p-0 lg:p-3 align-middle border-0 lg:border-r border-slate-200">
                                            <label class="block lg:hidden text-xs font-semibold text-slate-700 mb-1">
                                                No. Batch
                                            </label>
                                            <input type="text" name="items[{{ $idx }}][batch_number]" placeholder="No. Batch" class="h-11 lg:h-10 w-full rounded-xl lg:rounded-lg border-slate-300 bg-white text-xs font-medium focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                        </td>
                                        <td class="col-span-1 lg:col-auto p-0 lg:p-3 align-middle border-0 lg:border-r border-slate-200">
                                            <label class="block lg:hidden text-xs font-semibold text-slate-700 mb-1">
                                                Tgl Expired
                                            </label>
                                            <input type="date" name="items[{{ $idx }}][tanggal_kadaluarsa]" class="h-11 lg:h-10 w-full rounded-xl lg:rounded-lg border-slate-300 bg-white text-xs font-medium focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                        </td>
                                        <td class="col-span-1 lg:col-auto p-0 lg:p-3 align-middle border-0 lg:border-r border-slate-200">
                                            <label class="block lg:hidden text-xs font-semibold text-slate-700 mb-1">
                                                Jumlah Pack
                                            </label>
                                            <input type="number" name="items[{{ $idx }}][jumlah_pack]" min="1" placeholder="Pack" class="h-11 lg:h-10 w-full text-left lg:text-center rounded-xl lg:rounded-lg border-slate-300 bg-white text-xs font-medium placeholder:text-slate-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                        </td>
                                        <td class="col-span-1 lg:col-auto p-0 lg:p-3 align-middle border-0 lg:border-r border-slate-200 text-center">
                                            <label class="block lg:hidden text-xs font-semibold text-slate-700 mb-1 text-left">
                                                Qty Pesan
                                            </label>
                                            <div class="h-11 lg:h-10 flex items-center justify-center px-2 bg-slate-100 border border-slate-200 rounded-xl lg:rounded-lg text-xs font-bold text-slate-800">
                                                {{ $d->jumlah }} {{ $unitLabel }}
                                            </div>
                                        </td>
                                        <td class="col-span-1 lg:col-auto p-0 lg:p-3 align-middle border-0 lg:border-r border-slate-200 text-center">
                                            <label class="block lg:hidden text-xs font-semibold text-slate-700 mb-1 text-left">
                                                Diterima
                                            </label>
                                            <div class="h-11 lg:h-10 flex items-center justify-center px-2 {{ $alreadyReceived > 0 ? 'bg-amber-50 border border-amber-200 text-amber-700' : 'bg-slate-50 border border-slate-200 text-slate-500' }} rounded-xl lg:rounded-lg text-xs font-semibold">
                                                {{ $alreadyReceived }}
                                            </div>
                                        </td>
                                        <td class="col-span-1 lg:col-auto p-0 lg:p-3 align-middle border-0 lg:border-r border-slate-200">
                                            <label class="block lg:hidden text-xs font-semibold text-slate-700 mb-1">
                                                Qty Diterima <span class="text-rose-500 font-bold">*</span>
                                            </label>
                                            <div class="relative">
                                                <input type="number" step="0.01" name="items[{{ $idx }}][jumlah]" required min="0.01" value="{{ $remaining }}" placeholder="0" class="h-11 lg:h-10 w-full rounded-xl lg:rounded-lg border-slate-300 bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm font-bold text-slate-900 pr-10">
                                                <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none text-[11px] font-bold text-slate-400">
                                                    {{ $unitLabel }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="hidden lg:table-cell p-3 align-middle text-center">
                                            <button type="button" class="h-8 w-8 inline-flex items-center justify-center text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition remove-row-btn" title="Hapus Baris" tabindex="-1">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr class="item-row grid grid-cols-2 gap-3 p-3.5 sm:p-4 bg-slate-50/50 rounded-xl border border-slate-200/70 lg:table-row lg:p-0 lg:border-0 lg:rounded-none lg:bg-transparent lg:gap-0 hover:bg-slate-50/70 transition-colors">
                                    <td class="col-span-2 block lg:hidden p-0">
                                        <div class="flex items-center justify-between pb-2 mb-1 border-b border-slate-200/70">
                                            <span class="text-xs font-bold text-slate-800 font-sans item-badge">Item #1</span>
                                            <button type="button" class="text-xs font-semibold text-rose-500 hover:text-rose-700 remove-row-btn transition">
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                    <td class="col-span-2 lg:col-auto p-0 lg:p-3 align-middle border-0 lg:border-r border-slate-200">
                                        <label class="block lg:hidden text-xs font-semibold text-slate-700 mb-1">
                                            Barang <span class="text-rose-500 font-bold">*</span>
                                        </label>
                                        <select name="items[0][barang_id]" required class="barang-select h-11 lg:h-10 w-full rounded-xl lg:rounded-lg border-slate-300 bg-white text-xs font-semibold focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                            <option value="">-- Pilih Barang --</option>
                                            @foreach($barangs as $barang)
                                                <option value="{{ $barang->id }}" data-location="{{ $barang->location_id }}">
                                                    {{ $barang->kode_barang }} - {{ $barang->nama_barang }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="col-span-2 lg:col-auto p-0 lg:p-3 align-middle border-0 lg:border-r border-slate-200">
                                        <label class="block lg:hidden text-xs font-semibold text-slate-700 mb-1">
                                            Lokasi Rak <span class="text-rose-500 font-bold">*</span>
                                        </label>
                                        <select name="items[0][location_id]" class="location-select h-11 lg:h-10 w-full rounded-xl lg:rounded-lg border-slate-300 bg-white text-xs font-medium focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                            <option value="">-- Pilih Rak --</option>
                                            @foreach($locations as $loc)
                                                <option value="{{ $loc->id }}">
                                                    {{ $loc->code }} - {{ $loc->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="col-span-1 lg:col-auto p-0 lg:p-3 align-middle border-0 lg:border-r border-slate-200">
                                        <label class="block lg:hidden text-xs font-semibold text-slate-700 mb-1">
                                            No. Batch
                                        </label>
                                        <input type="text" name="items[0][batch_number]" placeholder="No. Batch" class="h-11 lg:h-10 w-full rounded-xl lg:rounded-lg border-slate-300 bg-white text-xs font-medium focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                    </td>
                                    <td class="col-span-1 lg:col-auto p-0 lg:p-3 align-middle border-0 lg:border-r border-slate-200">
                                        <label class="block lg:hidden text-xs font-semibold text-slate-700 mb-1">
                                            Tgl Expired
                                        </label>
                                        <input type="date" name="items[0][tanggal_kadaluarsa]" class="h-11 lg:h-10 w-full rounded-xl lg:rounded-lg border-slate-300 bg-white text-xs font-medium focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                    </td>
                                    <td class="col-span-1 lg:col-auto p-0 lg:p-3 align-middle border-0 lg:border-r border-slate-200">
                                        <label class="block lg:hidden text-xs font-semibold text-slate-700 mb-1">
                                            Jumlah Pack
                                        </label>
                                        <input type="number" name="items[0][jumlah_pack]" min="1" placeholder="Pack" class="h-11 lg:h-10 w-full text-left lg:text-center rounded-xl lg:rounded-lg border-slate-300 bg-white text-xs font-medium placeholder:text-slate-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                    </td>
                                    <td class="col-span-1 lg:col-auto p-0 lg:p-3 align-middle border-0 lg:border-r border-slate-200">
                                        <label class="block lg:hidden text-xs font-semibold text-slate-700 mb-1">
                                            Berat Netto (Kg) <span class="text-rose-500 font-bold">*</span>
                                        </label>
                                        <input type="number" step="0.01" name="items[0][jumlah]" required min="0.01" placeholder="Berat (Kg)" class="h-11 lg:h-10 w-full rounded-xl lg:rounded-lg border-slate-300 bg-white text-xs sm:text-sm font-semibold text-slate-900 placeholder:font-normal placeholder:text-slate-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                    </td>
                                    <td class="hidden lg:table-cell p-3 align-middle text-center">
                                        <button type="button" class="h-8 w-8 inline-flex items-center justify-center text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition remove-row-btn" title="Hapus Baris" tabindex="-1">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <div class="mt-2">
                    <button type="button" id="add-row-btn" class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 hover:text-blue-800 transition py-1">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Baris Barang
                    </button>
                </div>
            </div>

            <!-- Section 3: FORMULIR QC PENERIMAAN BAHAN (FRM.JFD.01.01 Rev. 0) - Langsung di dalam card utama -->
            <div class="space-y-4 pt-4 border-t border-slate-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-[#1e295d]">Pemeriksaan QC Inbound</h3>
                        <p class="text-[11px] text-slate-500 font-mono">FRM.JFD.01.01 Rev. 0</p>
                    </div>
                    <label class="inline-flex items-center cursor-pointer gap-2">
                        <input type="checkbox" name="qc_aktif" value="1" checked class="sr-only peer" id="toggle_qc">
                        <div class="relative w-9 h-5 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600"></div>
                        <span class="text-xs font-bold text-slate-700">Aktifkan</span>
                    </label>
                </div>

                <div id="qc_section" class="space-y-4">
                    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3">
                        <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-medium text-slate-700 hover:text-slate-900 transition">
                            <input type="checkbox" name="qc_kebersihan_kendaraan" value="1" checked class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            <span>Kendaraan Bersih</span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-medium text-slate-700 hover:text-slate-900 transition">
                            <input type="checkbox" name="qc_kemasan_utuh" value="1" checked class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            <span>Kemasan Utuh</span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-medium text-slate-700 hover:text-slate-900 transition">
                            <input type="checkbox" name="qc_expire_date_aman" value="1" checked class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            <span>Expired Aman</span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-medium text-slate-700 hover:text-slate-900 transition">
                            <input type="checkbox" name="qc_kondisi_aroma" value="1" checked class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            <span>Aroma Normal</span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-medium text-slate-700 hover:text-slate-900 transition">
                            <input type="checkbox" name="qc_kondisi_tekstur" value="1" checked class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            <span>Tekstur Normal</span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-medium text-slate-700 hover:text-slate-900 transition">
                            <input type="checkbox" name="qc_kondisi_penampakan" value="1" checked class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            <span>Warna Normal</span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-medium text-slate-700 hover:text-slate-900 transition col-span-2 sm:col-span-1 lg:col-span-1">
                            <input type="checkbox" name="qc_bebas_benda_asing" value="1" checked class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            <span>Bebas Asing</span>
                        </label>
                    </div>

                    <div class="pt-3 border-t border-slate-100 space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Suhu Bahan (°C)
                            </label>
                            <div class="relative">
                                <input type="number" step="0.1" name="qc_suhu" placeholder="-18.0" class="h-11 w-full rounded-xl border-slate-300 bg-white text-slate-800 text-sm font-bold focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition pr-10">
                                <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-xs font-bold text-slate-400">
                                    °C
                                </span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Keputusan QC <span class="text-rose-500 font-bold">*</span>
                            </label>
                            <select name="qc_status" class="h-11 w-full rounded-xl border-slate-300 bg-white text-slate-800 text-sm font-bold focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                                <option value="pass" class="text-emerald-700 font-bold">✓ Lolos (Pass)</option>
                                <option value="fail" class="text-rose-700 font-bold">✕ Ditolak (Fail)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Catatan QC
                            </label>
                            <input type="text" name="qc_catatan" placeholder="Kondisi beku baik..." class="h-11 w-full rounded-xl border-slate-300 bg-white text-slate-800 text-sm placeholder:text-slate-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 4: Tombol Aksi Bawah -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                <a href="{{ route('barang-masuk.index') }}" class="px-5 py-2.5 text-xs font-semibold text-slate-600 hover:text-slate-800 rounded-xl hover:bg-slate-100 transition">
                    Batal
                </a>
                <button type="submit" class="h-11 px-6 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan Penerimaan
                </button>
            </div>
        </form>
    </div>

    <!-- Javascript to handle dynamic rows & seamless mouse-free keyboard navigation -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('barang-masuk-form');
            const tbody = document.getElementById('items-tbody');
            const addBtn = document.getElementById('add-row-btn');
            let rowIndex = {{ $selectedPO ? $selectedPO->details->count() : 1 }};

            // Master data of barangs for extra items select box
            const barangs = @json($barangs);
            const locations = @json($locations);

            let locOptions = '<option value="">-- Pilih Rak --</option>';
            locations.forEach(function(l) {
                locOptions += `<option value="${l.id}">${l.code} - ${l.name}</option>`;
            });

            function bindBarangChange(row) {
                const bSelect = row.querySelector('.barang-select');
                const lSelect = row.querySelector('.location-select');
                if (bSelect && lSelect) {
                    bSelect.addEventListener('change', function() {
                        const bId = parseInt(this.value);
                        const found = barangs.find(b => b.id === bId);
                        if (found && found.location_id) {
                            lSelect.value = found.location_id;
                        }
                    });
                }
            }

            document.querySelectorAll('.item-row').forEach(bindBarangChange);

            function reindexRows() {
                const rows = document.querySelectorAll('.item-row');
                rows.forEach((row, index) => {
                    const badge = row.querySelector('.item-badge');
                    if (badge) {
                        badge.textContent = `Item #${index + 1}`;
                    }
                    const inputs = row.querySelectorAll('input, select');
                    inputs.forEach(el => {
                        if (el.name && el.name.startsWith('items[')) {
                            el.name = el.name.replace(/^items\[\d+\]/, `items[${index}]`);
                        }
                    });
                });
                rowIndex = rows.length;
            }

            function addNewRow(focusFirst = true) {
                const tr = document.createElement('tr');
                tr.className = 'item-row grid grid-cols-2 gap-3 p-3.5 sm:p-4 bg-slate-50/50 rounded-xl border border-slate-200/70 lg:table-row lg:p-0 lg:border-0 lg:rounded-none lg:bg-transparent lg:gap-0 hover:bg-slate-50/70 transition-colors';
                
                let selectOptions = '<option value="">-- Pilih Barang --</option>';
                barangs.forEach(function(b) {
                    selectOptions += `<option value="${b.id}">${b.kode_barang} - ${b.nama_barang}</option>`;
                });

                const currentCount = document.querySelectorAll('.item-row').length + 1;

                let columnsHtml = `
                    <td class="col-span-2 block lg:hidden p-0">
                        <div class="flex items-center justify-between pb-2 mb-1 border-b border-slate-200/70">
                            <span class="text-xs font-bold text-slate-800 font-sans item-badge">Item #${currentCount}</span>
                            <button type="button" class="text-xs font-semibold text-rose-500 hover:text-rose-700 remove-row-btn transition">
                                Hapus
                            </button>
                        </div>
                    </td>
                    <td class="col-span-2 lg:col-auto p-0 lg:p-3 align-middle border-0 lg:border-r border-slate-200">
                        <label class="block lg:hidden text-xs font-semibold text-slate-700 mb-1">
                            Barang <span class="text-rose-500 font-bold">*</span>
                        </label>
                        <select name="items[${rowIndex}][barang_id]" required class="barang-select h-11 lg:h-10 w-full rounded-xl lg:rounded-lg border-slate-300 bg-white text-xs font-semibold focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            ${selectOptions}
                        </select>
                    </td>
                    <td class="col-span-2 lg:col-auto p-0 lg:p-3 align-middle border-0 lg:border-r border-slate-200">
                        <label class="block lg:hidden text-xs font-semibold text-slate-700 mb-1">
                            Lokasi Rak <span class="text-rose-500 font-bold">*</span>
                        </label>
                        <select name="items[${rowIndex}][location_id]" class="location-select h-11 lg:h-10 w-full rounded-xl lg:rounded-lg border-slate-300 bg-white text-xs font-medium focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            ${locOptions}
                        </select>
                    </td>
                    <td class="col-span-1 lg:col-auto p-0 lg:p-3 align-middle border-0 lg:border-r border-slate-200">
                        <label class="block lg:hidden text-xs font-semibold text-slate-700 mb-1">
                            No. Batch
                        </label>
                        <input type="text" name="items[${rowIndex}][batch_number]" placeholder="No. Batch" class="h-11 lg:h-10 w-full rounded-xl lg:rounded-lg border-slate-300 bg-white text-xs font-medium focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </td>
                    <td class="col-span-1 lg:col-auto p-0 lg:p-3 align-middle border-0 lg:border-r border-slate-200">
                        <label class="block lg:hidden text-xs font-semibold text-slate-700 mb-1">
                            Tgl Expired
                        </label>
                        <input type="date" name="items[${rowIndex}][tanggal_kadaluarsa]" class="h-11 lg:h-10 w-full rounded-xl lg:rounded-lg border-slate-300 bg-white text-xs font-medium focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </td>
                    <td class="col-span-1 lg:col-auto p-0 lg:p-3 align-middle border-0 lg:border-r border-slate-200">
                        <label class="block lg:hidden text-xs font-semibold text-slate-700 mb-1">
                            Jumlah Pack
                        </label>
                        <input type="number" name="items[${rowIndex}][jumlah_pack]" min="1" placeholder="Pack" class="h-11 lg:h-10 w-full text-left lg:text-center rounded-xl lg:rounded-lg border-slate-300 bg-white text-xs font-medium placeholder:text-slate-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </td>
                `;

                if ('{{ $selectedPO }}') {
                    columnsHtml += `
                        <td class="col-span-1 lg:col-auto p-0 lg:p-3 align-middle border-0 lg:border-r border-slate-200 text-center">
                            <label class="block lg:hidden text-xs font-semibold text-slate-700 mb-1 text-left">Qty Pesan</label>
                            <div class="h-11 lg:h-10 flex items-center justify-center px-2 bg-slate-50 border border-dashed border-slate-200 rounded-xl lg:rounded-lg text-xs italic text-slate-400">
                                Non-PO
                            </div>
                        </td>
                        <td class="col-span-1 lg:col-auto p-0 lg:p-3 align-middle border-0 lg:border-r border-slate-200 text-center">
                            <label class="block lg:hidden text-xs font-semibold text-slate-700 mb-1 text-left">Diterima</label>
                            <div class="h-11 lg:h-10 flex items-center justify-center px-2 bg-slate-50 border border-dashed border-slate-200 rounded-xl lg:rounded-lg text-xs italic text-slate-400">
                                -
                            </div>
                        </td>
                    `;
                }

                columnsHtml += `
                    <td class="col-span-1 lg:col-auto p-0 lg:p-3 align-middle border-0 lg:border-r border-slate-200">
                        <label class="block lg:hidden text-xs font-semibold text-slate-700 mb-1">
                            Berat Netto (Kg) <span class="text-rose-500 font-bold">*</span>
                        </label>
                        <input type="number" step="0.01" name="items[${rowIndex}][jumlah]" required min="0.01" placeholder="Berat (Kg)" class="h-11 lg:h-10 w-full rounded-xl lg:rounded-lg border-slate-300 bg-white text-xs sm:text-sm font-semibold text-slate-900 placeholder:font-normal placeholder:text-slate-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </td>
                    <td class="hidden lg:table-cell p-3 align-middle text-center">
                        <button type="button" class="h-8 w-8 inline-flex items-center justify-center text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition remove-row-btn" title="Hapus Baris" tabindex="-1">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </td>
                `;

                tr.innerHTML = columnsHtml;
                tbody.appendChild(tr);
                bindBarangChange(tr);
                registerDeleteBtn(tr.querySelector('.remove-row-btn'));
                reindexRows();

                if (focusFirst) {
                    const firstInput = tr.querySelector('select, input');
                    if (firstInput) {
                        setTimeout(() => firstInput.focus(), 20);
                    }
                }
            }

            if (addBtn) addBtn.addEventListener('click', () => addNewRow(true));

            function registerDeleteBtn(btn) {
                if (!btn) return;
                btn.addEventListener('click', function() {
                    const rowsCount = document.querySelectorAll('.item-row').length;
                    if (rowsCount > 1) {
                        const row = btn.closest('.item-row');
                        const prev = row.previousElementSibling;
                        const next = row.nextElementSibling;
                        row.remove();
                        reindexRows();
                        if (prev) {
                            const inp = prev.querySelector('input[type="number"]') || prev.querySelector('select, input');
                            if (inp) inp.focus();
                        } else if (next) {
                            const inp = next.querySelector('select, input');
                            if (inp) inp.focus();
                        }
                    } else {
                        if (window.toast) {
                            window.toast.warning('Minimal harus ada 1 item penerimaan.', 'Peringatan');
                        } else {
                            alert('Minimal harus ada 1 item penerimaan.');
                        }
                    }
                });
            }

            document.querySelectorAll('.remove-row-btn').forEach(registerDeleteBtn);

            // Toggle QC Inbound Section
            const toggleQc = document.getElementById('toggle_qc');
            const qcSection = document.getElementById('qc_section');
            if (toggleQc && qcSection) {
                toggleQc.addEventListener('change', function() {
                    qcSection.style.display = this.checked ? 'block' : 'none';
                });
            }

            // ==========================================
            // MOUSE-FREE KEYBOARD NAVIGATION ENGINE
            // ==========================================
            function getNavigableInputs() {
                return Array.from(form.querySelectorAll(
                    'input:not([type="hidden"]):not([disabled]):not([tabindex="-1"]), select:not([disabled]):not([tabindex="-1"]), textarea:not([disabled]):not([tabindex="-1"])'
                )).filter(el => el.offsetParent !== null);
            }

            // Autofocus on first input (no_surat_jalan) on page load
            const firstInput = document.getElementById('no_surat_jalan');
            if (firstInput) {
                firstInput.focus();
                if (firstInput.select) firstInput.select();
            }

            form.addEventListener('keydown', function(e) {
                // 1. Submit Shortcut: Ctrl + Enter or Cmd + Enter
                if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                    e.preventDefault();
                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.click();
                    } else {
                        form.requestSubmit();
                    }
                    return;
                }

                // 2. Add Row Shortcut: Alt + A or Insert
                if ((e.altKey && (e.key === 'a' || e.key === 'A')) || e.key === 'Insert') {
                    e.preventDefault();
                    addNewRow(true);
                    return;
                }

                // 3. Delete Row Shortcut: Shift + Delete or Alt + Delete (when inside a row)
                if ((e.shiftKey || e.altKey) && (e.key === 'Delete' || (e.altKey && e.key === 'Backspace'))) {
                    const row = document.activeElement ? document.activeElement.closest('.item-row') : null;
                    if (row) {
                        e.preventDefault();
                        const deleteBtn = row.querySelector('.remove-row-btn');
                        if (deleteBtn) deleteBtn.click();
                        return;
                    }
                }

                // 4. Enter Key: Seamless forward navigation & auto-add row
                if (e.key === 'Enter' && !e.shiftKey && !e.ctrlKey && !e.altKey && !e.metaKey) {
                    const target = e.target;

                    if (target.tagName === 'BUTTON' || target.tagName === 'A') {
                        return;
                    }

                    const row = target.closest('.item-row');
                    const isQtyField = target.matches('input[type="number"]') || (target.name && target.name.includes('[jumlah]'));

                    if (row && isQtyField) {
                        e.preventDefault();
                        const rows = Array.from(tbody.querySelectorAll('.item-row'));
                        const isLastRow = rows[rows.length - 1] === row;

                        if (isLastRow) {
                            addNewRow(true);
                        } else {
                            const nextRow = row.nextElementSibling;
                            if (nextRow) {
                                const nextInput = nextRow.querySelector('select:not([disabled]), input:not([disabled])');
                                if (nextInput) {
                                    nextInput.focus();
                                    if (nextInput.select && nextInput.type !== 'date') nextInput.select();
                                }
                            }
                        }
                        return;
                    }

                    if (target.matches('input, select, textarea')) {
                        e.preventDefault();
                        const inputs = getNavigableInputs();
                        const currentIndex = inputs.indexOf(target);
                        if (currentIndex >= 0 && currentIndex < inputs.length - 1) {
                            const nextInput = inputs[currentIndex + 1];
                            nextInput.focus();
                            if (nextInput.select && typeof nextInput.select === 'function' && nextInput.type !== 'date') {
                                nextInput.select();
                            }
                        }
                        return;
                    }
                }

                // 5. Shift + Enter: Move to previous field
                if (e.key === 'Enter' && e.shiftKey && !e.ctrlKey && !e.altKey) {
                    const target = e.target;
                    if (target.matches('input, select, textarea')) {
                        e.preventDefault();
                        const inputs = getNavigableInputs();
                        const currentIndex = inputs.indexOf(target);
                        if (currentIndex > 0) {
                            const prevInput = inputs[currentIndex - 1];
                            prevInput.focus();
                            if (prevInput.select && typeof prevInput.select === 'function' && prevInput.type !== 'date') {
                                prevInput.select();
                            }
                        }
                        return;
                    }
                }
            });
        });
    </script>
</x-app-layout>
