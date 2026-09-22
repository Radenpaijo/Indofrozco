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

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <!-- Error Alert -->
            @if(session('error'))
                <div class="bg-rose-50 border-l-4 border-rose-500 p-4 rounded shadow-sm mb-6">
                    <span class="text-rose-800 font-medium">{{ session('error') }}</span>
                </div>
            @endif

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

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-100" id="items-table">
                                <thead>
                                    <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        <th class="px-4 py-3">Barang</th>
                                        @if($selectedPO)
                                            <th class="px-4 py-3">Qty Pesan</th>
                                            <th class="px-4 py-3">Qty Diterima Aktual</th>
                                        @else
                                            <th class="px-4 py-3">Jumlah Diterima</th>
                                        @endif
                                        <th class="px-4 py-3 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-sm" id="items-tbody">
                                    @if($selectedPO && $selectedPO->details->isNotEmpty())
                                        @foreach($selectedPO->details as $idx => $d)
                                            <tr class="item-row">
                                                <td class="px-4 py-3">
                                                    <!-- Show selection, but restrict changing for PO items -->
                                                    <select name="items[{{ $idx }}][barang_id]" required class="w-full rounded-lg border-gray-300 bg-gray-50 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                                        @foreach($barangs as $barang)
                                                            <option value="{{ $barang->id }}" {{ $d->barang_id == $barang->id ? 'selected' : '' }}>
                                                                {{ $barang->kode_barang }} - {{ $barang->nama_barang }} ({{ $barang->lokasi_gudang }}{{ $barang->kode_rak ? ' • ' . $barang->kode_rak : '' }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="px-4 py-3 text-gray-500 font-semibold">
                                                    {{ $d->jumlah }} {{ $d->barang->satuan ?? '' }}
                                                </td>
                                                <td class="px-4 py-3">
                                                    @php 
                                                        $alreadyReceived = $selectedPO->barangMasuks->where('barang_id', $d->barang_id)->sum('jumlah');
                                                        $remaining = max(0, $d->jumlah - $alreadyReceived);
                                                    @endphp
                                                    <input type="number" name="items[{{ $idx }}][jumlah]" required min="1" value="{{ $remaining }}" placeholder="Jumlah" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                                    <p class="text-[10px] text-gray-400 mt-1">Sudah diterima sebelumnya: {{ $alreadyReceived }}</p>
                                                </td>
                                                <td class="px-4 py-3 text-right">
                                                    <button type="button" class="text-rose-600 hover:text-rose-900 font-semibold text-xs remove-row-btn">Hapus</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr class="item-row">
                                            <td class="px-4 py-3">
                                                <select name="items[0][barang_id]" required class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                                    <option value="">-- Pilih Barang --</option>
                                                    @foreach($barangs as $barang)
                                                        <option value="{{ $barang->id }}">
                                                            {{ $barang->kode_barang }} - {{ $barang->nama_barang }} (Stok: {{ $barang->stok }} {{ $barang->satuan }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="px-4 py-3">
                                                <input type="number" name="items[0][jumlah]" required min="1" placeholder="Masukkan jumlah" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                            </td>
                                            <td class="px-4 py-3 text-right">
                                                <button type="button" class="text-rose-600 hover:text-rose-900 font-semibold text-xs remove-row-btn">Hapus</button>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            <button type="button" id="add-row-btn" class="inline-flex items-center px-3 py-1.5 border border-indigo-600 text-indigo-600 text-xs font-bold rounded-lg hover:bg-indigo-50 transition-colors">
                                <svg class="h-3 w-3 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                [+] Tambah Item Ekstra (Non-PO)
                            </button>
                        </div>
                    </div>

                    <!-- Special warning about Shrimp -->
                    <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded text-sm text-amber-800">
                        <span class="font-bold">💡 Aturan Bisnis Indofroz:</span> Udang segar yang diterima akan langsung dialokasikan otomatis ke Produksi (tanpa masuk ke cold storage), namun pencatatannya tetap melalui alur masuk dan keluar untuk keakuratan batch FIFO.
                    </div>

                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-100">
                        <a href="{{ route('barang-masuk.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm font-semibold rounded-lg transition-colors">
                            Batal
                        </a>
                        <button type="submit" class="inline-flex items-center px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow transition-colors">
                            Simpan Penerimaan
                        </button>
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
                tr.className = 'item-row';
                
                let selectOptions = '<option value="">-- Pilih Barang Ekstra --</option>';
                barangs.forEach(function(b) {
                    const rakText = b.kode_rak ? ' • ' + b.kode_rak : '';
                    selectOptions += `<option value="${b.id}">${b.kode_barang} - ${b.nama_barang} (${b.lokasi_gudang}${rakText})</option>`;
                });

                // Conditional column structure depending if PO is selected
                let columnsHtml = `
                    <td class="px-4 py-3">
                        <select name="items[${rowIndex}][barang_id]" required class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            ${selectOptions}
                        </select>
                    </td>
                `;

                if ('{{ $selectedPO }}') {
                    columnsHtml += `
                        <td class="px-4 py-3 text-gray-400 italic">Non-PO Item</td>
                    `;
                }

                columnsHtml += `
                    <td class="px-4 py-3">
                        <input type="number" name="items[${rowIndex}][jumlah]" required min="1" placeholder="Jumlah" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </td>
                    <td class="px-4 py-3 text-right">
                        <button type="button" class="text-rose-600 hover:text-rose-900 font-semibold text-xs remove-row-btn">Hapus</button>
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
