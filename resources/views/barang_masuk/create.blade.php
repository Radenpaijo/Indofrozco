<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4 min-w-0">
            <x-back-button />
            <div class="min-w-0">
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    {{ __('Penerimaan Gudang (Barang Masuk)') }}
                </h2>
                <p class="text-xs text-slate-500 mt-1">Formulir penerimaan stok fisik ke dalam lokasi rak.</p>
            </div>
        </div>
    </x-slot>

    <div class="w-full">

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <!-- PO Selector Bar -->
                <div class="mb-8 p-4 bg-gray-50 rounded-xl border border-gray-100 flex items-center justify-between flex-wrap gap-4">
                    <div>
                        <span class="text-sm font-bold text-gray-800">Hubungkan dengan Purchase Order (PO)</span>
                        <p class="text-xs text-gray-500">Pilih PO yang dikirim oleh supplier untuk menampilkan item bawaannya secara otomatis.</p>
                    </div>
                    <div>
                        <select id="po_selector" class="rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm" onchange="window.location.href = '{{ route('barang-masuk.create') }}?po_id=' + this.value">
                            <option value="">-- Input Manual (Tanpa PO) --</option>
                            @foreach($activePOs as $po)
                                <option value="{{ $po->id }}" {{ request('po_id') == $po->id ? 'selected' : '' }}>
                                    {{ $po->no_po }} - {{ $po->supplier->nama_supplier ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <form method="POST" action="{{ route('barang-masuk.store') }}" class="space-y-6">
                    @csrf

                    @if($selectedPO)
                        <input type="hidden" name="po_id" value="{{ $selectedPO->id }}">
                        <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded shadow-sm flex items-center justify-between mb-6">
                            <div>
                                <span class="text-sm font-bold text-emerald-950">Menerima Barang untuk PO: {{ $selectedPO->no_po }}</span>
                                <p class="text-xs text-emerald-800 mt-0.5">Supplier: {{ $selectedPO->supplier->nama_supplier ?? '-' }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="no_surat_jalan" :value="__('Nomor Surat Jalan (Wajib)')" />
                            <x-text-input id="no_surat_jalan" class="block mt-1 w-full" type="text" name="no_surat_jalan" :value="old('no_surat_jalan')" required placeholder="Contoh: SJ-998822" />
                            <x-input-error :messages="$errors->get('no_surat_jalan')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="tanggal" :value="__('Tanggal Masuk Gudang (Wajib untuk FIFO)')" />
                            <x-text-input id="tanggal" class="block mt-1 w-full" type="date" name="tanggal" :value="old('tanggal', date('Y-m-d'))" required />
                            <x-input-error :messages="$errors->get('tanggal')" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <h4 class="text-md font-bold text-gray-800 mb-2">Item Penerimaan</h4>
                        <p class="text-xs text-gray-400 mb-4">Pastikan jumlah fisik yang dihitung sesuai dengan yang Anda input di bawah ini.</p>

                        <div class="overflow-x-auto border border-slate-300 rounded-xl shadow-sm">
                            <table class="w-full text-left border-collapse" id="items-table">
                                <thead>
                                    <tr class="bg-slate-100 border-b border-slate-300 text-[11px] font-bold uppercase tracking-wider text-slate-700">
                                        <th class="py-3.5 px-4 border-r border-slate-300">Barang</th>
                                        @if($selectedPO)
                                            <th class="py-3.5 px-4 border-r border-slate-300 text-center w-32 whitespace-nowrap">Qty Pesan</th>
                                            <th class="py-3.5 px-4 border-r border-slate-300 text-center w-36 whitespace-nowrap">Sudah Diterima</th>
                                            <th class="py-3.5 px-4 border-r border-slate-300 text-center w-48 whitespace-nowrap">Qty Diterima Aktual</th>
                                        @else
                                            <th class="py-3.5 px-4 border-r border-slate-300 text-center w-56 whitespace-nowrap">Jumlah Diterima</th>
                                        @endif
                                        <th class="py-3.5 px-4 text-center w-24 whitespace-nowrap">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 text-sm" id="items-tbody">
                                    @if($selectedPO && $selectedPO->details->isNotEmpty())
                                        @foreach($selectedPO->details as $idx => $d)
                                            @php 
                                                $alreadyReceived = $selectedPO->barangMasuks->where('barang_id', $d->barang_id)->sum('jumlah');
                                                $remaining = max(0, $d->jumlah - $alreadyReceived);
                                                $unitLabel = $d->barang->unit->name ?? $d->barang->satuan ?? 'Unit';
                                            @endphp
                                            <tr class="item-row hover:bg-slate-50/80">
                                                <td class="p-3.5 align-middle border-r border-slate-200">
                                                    <!-- Show selection, but restrict changing for PO items -->
                                                    <select name="items[{{ $idx }}][barang_id]" required class="h-10 w-full rounded-lg border-slate-300 bg-slate-50 focus:border-indigo-500 focus:ring-indigo-500 text-sm font-medium">
                                                        @foreach($barangs as $barang)
                                                            <option value="{{ $barang->id }}" {{ $d->barang_id == $barang->id ? 'selected' : '' }}>
                                                                {{ $barang->kode_barang }} - {{ $barang->nama_barang }} ({{ $barang->lokasi_gudang }}{{ $barang->kode_rak ? ' • ' . $barang->kode_rak : '' }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="p-3.5 align-middle border-r border-slate-200 text-center">
                                                    <div class="h-10 flex items-center justify-center px-3 bg-slate-100 border border-slate-200 rounded-lg text-xs font-bold text-slate-800">
                                                        {{ $d->jumlah }} {{ $unitLabel }}
                                                    </div>
                                                </td>
                                                <td class="p-3.5 align-middle border-r border-slate-200 text-center">
                                                    <div class="h-10 flex items-center justify-center px-3 {{ $alreadyReceived > 0 ? 'bg-amber-50 border border-amber-200 text-amber-700' : 'bg-slate-50 border border-slate-200 text-slate-500' }} rounded-lg text-xs font-semibold">
                                                        {{ $alreadyReceived }} {{ $unitLabel }}
                                                    </div>
                                                </td>
                                                <td class="p-3.5 align-middle border-r border-slate-200">
                                                    <div class="relative">
                                                        <input type="number" name="items[{{ $idx }}][jumlah]" required min="1" value="{{ $remaining }}" placeholder="0" class="h-10 w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm font-semibold text-slate-900 pr-12 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                                        <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-xs font-semibold text-slate-400 select-none">
                                                            {{ $unitLabel }}
                                                        </span>
                                                    </div>
                                                </td>
                                                <td class="p-3.5 align-middle text-center">
                                                    <div class="h-10 flex items-center justify-center">
                                                        <button type="button" class="h-9 inline-flex items-center justify-center px-3 text-xs font-semibold text-rose-600 hover:text-white hover:bg-rose-600 border border-rose-200 hover:border-rose-600 rounded-lg transition-all duration-150 shadow-sm remove-row-btn">
                                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                            Hapus
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr class="item-row hover:bg-slate-50/80">
                                            <td class="p-3.5 align-middle border-r border-slate-200">
                                                <select name="items[0][barang_id]" required class="h-10 w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm font-medium">
                                                    <option value="">-- Pilih Barang --</option>
                                                    @foreach($barangs as $barang)
                                                        <option value="{{ $barang->id }}">
                                                            {{ $barang->kode_barang }} - {{ $barang->nama_barang }} (Stok: {{ $barang->stok }} {{ $barang->satuan }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="p-3.5 align-middle border-r border-slate-200">
                                                <input type="number" name="items[0][jumlah]" required min="1" placeholder="Masukkan jumlah" class="h-10 w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm font-semibold">
                                            </td>
                                            <td class="p-3.5 align-middle text-center">
                                                <div class="h-10 flex items-center justify-center">
                                                    <button type="button" class="h-9 inline-flex items-center justify-center px-3 text-xs font-semibold text-rose-600 hover:text-white hover:bg-rose-600 border border-rose-200 hover:border-rose-600 rounded-lg transition-all duration-150 shadow-sm remove-row-btn">
                                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                        Hapus
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            <button type="button" id="add-row-btn" class="inline-flex items-center px-3.5 py-2 border border-indigo-600 text-indigo-600 text-xs font-bold rounded-xl hover:bg-indigo-50 transition-colors shadow-sm">
                                <svg class="h-3.5 w-3.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Tambah Item Ekstra (Non-PO)
                            </button>
                        </div>
                    </div>


                    <div class="flex items-center justify-end space-x-3 pt-6 border-t border-slate-200">
                        <a href="{{ route('barang-masuk.index') }}">
                            <x-button variant="outline">Batal</x-button>
                        </a>
                        <x-button type="submit" variant="indigo">
                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Simpan Penerimaan
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Javascript to handle dynamic rows -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tbody = document.getElementById('items-tbody');
            const addBtn = document.getElementById('add-row-btn');
            let rowIndex = {{ $selectedPO ? $selectedPO->details->count() : 1 }};

            // Master data of barangs for extra items select box
            const barangs = @json($barangs);

            addBtn.addEventListener('click', function() {
                // Create a row from scratch since clone node might duplicate read-only select box values from PO
                const tr = document.createElement('tr');
                tr.className = 'item-row hover:bg-slate-50/80';
                
                let selectOptions = '<option value="">-- Pilih Barang Ekstra --</option>';
                barangs.forEach(function(b) {
                    const rakText = b.kode_rak ? ' • ' + b.kode_rak : '';
                    selectOptions += `<option value="${b.id}">${b.kode_barang} - ${b.nama_barang} (${b.lokasi_gudang}${rakText})</option>`;
                });

                // Conditional column structure depending if PO is selected
                let columnsHtml = `
                    <td class="p-3.5 align-middle border-r border-slate-200">
                        <select name="items[${rowIndex}][barang_id]" required class="h-10 w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm font-medium">
                            ${selectOptions}
                        </select>
                    </td>
                `;

                if ('{{ $selectedPO }}') {
                    columnsHtml += `
                        <td class="p-3.5 align-middle border-r border-slate-200 text-center">
                            <div class="h-10 flex items-center justify-center px-3 bg-slate-50 border border-dashed border-slate-200 rounded-lg text-xs italic text-slate-400">
                                Non-PO
                            </div>
                        </td>
                        <td class="p-3.5 align-middle border-r border-slate-200 text-center">
                            <div class="h-10 flex items-center justify-center px-3 bg-slate-50 border border-dashed border-slate-200 rounded-lg text-xs italic text-slate-400">
                                -
                            </div>
                        </td>
                    `;
                }

                columnsHtml += `
                    <td class="p-3.5 align-middle border-r border-slate-200">
                        <input type="number" name="items[${rowIndex}][jumlah]" required min="1" placeholder="Jumlah" class="h-10 w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm font-semibold">
                    </td>
                    <td class="p-3.5 align-middle text-center">
                        <div class="h-10 flex items-center justify-center">
                            <button type="button" class="h-9 inline-flex items-center justify-center px-3 text-xs font-semibold text-rose-600 hover:text-white hover:bg-rose-600 border border-rose-200 hover:border-rose-600 rounded-lg transition-all duration-150 shadow-sm remove-row-btn">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Hapus
                            </button>
                        </div>
                    </td>
                `;

                tr.innerHTML = columnsHtml;
                tbody.appendChild(tr);
                rowIndex++;

                registerDeleteBtn(tr.querySelector('.remove-row-btn'));
            });

            function registerDeleteBtn(btn) {
                btn.addEventListener('click', function() {
                    const rowsCount = document.querySelectorAll('.item-row').length;
                    if (rowsCount > 1) {
                        btn.closest('.item-row').remove();
                    } else {
                        alert('Minimal harus ada 1 item penerimaan.');
                    }
                });
            }

            document.querySelectorAll('.remove-row-btn').forEach(registerDeleteBtn);
        });
    </script>
</x-app-layout>
