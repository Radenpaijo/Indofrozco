<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3 min-w-0">
            <x-back-button :href="route('audit-logs.index')" />
            <div class="min-w-0">
                <h2 class="font-bold text-xl sm:text-2xl text-slate-800 leading-tight truncate">
                    {{ __('Detail Audit Log #') }}{{ $auditLog->id }}
                </h2>
            </div>
        </div>
    </x-slot>

    @php
        $labelMapping = [
            'kode_barang' => 'Kode Barang',
            'nama_barang' => 'Nama Barang',
            'satuan' => 'Satuan',
            'stok' => 'Stok Fisik',
            'stok_minimum' => 'Stok Minimum',
            'batas_umur_hari' => 'Batas Umur Simpan (Hari)',
            'lokasi_gudang' => 'Lokasi Gudang',
            'kode_rak' => 'Kode Rak',
            'is_active' => 'Status Katalog',
            'code' => 'Kode Master',
            'name' => 'Nama Master',
            'status' => 'Status',
            'description' => 'Deskripsi / Keterangan',
            'capacity' => 'Kapasitas Maksimal',
            'warehouse_type_id' => 'Tipe Gudang',
            'category_id' => 'Kategori Barang',
            'unit_id' => 'Satuan Barang',
            'location_id' => 'Lokasi Rak',
            'nama_supplier' => 'Nama Supplier',
            'kontak' => 'Kontak / Telepon',
            'alamat' => 'Alamat',
            'batch_number' => 'Nomor Batch',
            'qty' => 'Jumlah (Qty)',
            'reason' => 'Alasan / Catatan',
            'url' => 'Halaman Akses (URL)',
            'user_id' => 'User Pelaksana ID',
            'adjustment' => 'Penyesuaian Stok',
            'source_location' => 'Lokasi Asal',
            'target_location' => 'Lokasi Tujuan',
            'created_at' => 'Waktu Dibuat',
            'updated_at' => 'Waktu Diperbarui',
            'deleted_at' => 'Waktu Dihapus',
        ];

        $categories = $categories ?? [];
        $units = $units ?? [];
        $locations = $locations ?? [];
        $warehouseTypes = $warehouseTypes ?? [];

        $parseValues = function($values) {
            if (is_string($values)) {
                $decoded = json_decode($values, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $values = $decoded;
                }
            }
            if (is_array($values)) {
                unset($values['_token'], $values['_method'], $values['password'], $values['password_confirmation']);
            }
            return $values;
        };

        $formatDisplayValue = function($key, $val) use ($categories, $units, $locations, $warehouseTypes) {
            if (is_null($val) || $val === '') {
                return '<span class="text-slate-400 italic font-normal">(Kosong)</span>';
            }

            if (is_bool($val)) {
                $badgeClass = $val ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200';
                $text = $val ? 'Aktif / Ya' : 'Nonaktif / Tidak';
                return '<span class="px-2 py-0.5 rounded text-[10px] font-bold border ' . $badgeClass . '">' . $text . '</span>';
            }

            if ($key === 'category_id' && isset($categories[$val])) {
                return e($categories[$val]);
            }
            if ($key === 'unit_id' && isset($units[$val])) {
                return e($units[$val]);
            }
            if ($key === 'location_id' && isset($locations[$val])) {
                return e($locations[$val]);
            }
            if ($key === 'warehouse_type_id' && isset($warehouseTypes[$val])) {
                return e($warehouseTypes[$val]);
            }

            if (is_array($val)) {
                $html = '<div class="flex flex-wrap gap-1.5 py-0.5">';
                foreach ($val as $k => $v) {
                    $displayK = \Illuminate\Support\Str::headline($k);
                    $displayV = is_array($v) ? implode(', ', $v) : $v;
                    $html .= '<span class="inline-flex items-center px-2 py-1 rounded-md text-[11px] font-medium bg-slate-100 text-slate-800 border border-slate-200">';
                    $html .= '<strong>' . e($displayK) . ':</strong>&nbsp;' . e($displayV);
                    $html .= '</span>';
                }
                $html .= '</div>';
                return $html;
            }

            return e($val);
        };

        $oldValuesParsed = $parseValues($auditLog->old_values);
        $newValuesParsed = $parseValues($auditLog->new_values);
    @endphp

    <div class="space-y-6 text-left">
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
            <!-- Basic Meta Info Grid (5 Columns including Record ID) -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-6 text-sm mb-6 pb-6 border-b border-slate-100 text-left">
                <div>
                    <span class="text-xs font-bold uppercase text-slate-400 block mb-1">Waktu (Timestamp)</span>
                    <span class="font-mono font-bold text-slate-800 text-sm">{{ $auditLog->created_at->format('d/m/Y H:i:s') }}</span>
                </div>
                <div>
                    <span class="text-xs font-bold uppercase text-slate-400 block mb-1">User Pelaksana</span>
                    <span class="font-semibold text-slate-900">{{ $auditLog->user->name ?? 'System' }}</span>
                </div>
                <div>
                    <span class="text-xs font-bold uppercase text-slate-400 block mb-1">Modul & Aksi</span>
                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-200">
                        {{ $auditLog->module }} &bull; {{ $auditLog->action }}
                    </span>
                </div>
                <div>
                    <span class="text-xs font-bold uppercase text-slate-400 block mb-1">Record ID</span>
                    <span class="font-mono font-bold text-slate-700 text-xs bg-slate-100 px-2.5 py-1 rounded-lg border border-slate-200">#{{ $auditLog->record_id ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-xs font-bold uppercase text-slate-400 block mb-1">IP Address</span>
                    <span class="font-mono text-xs text-slate-600 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">{{ $auditLog->ip_address ?? '127.0.0.1' }}</span>
                </div>
            </div>

            <!-- Old Values vs New Values Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-left">
                <!-- Nilai Sebelum (Old Values) -->
                <div class="bg-slate-50/70 border border-slate-300 rounded-2xl p-5">
                    <h4 class="font-bold text-xs uppercase tracking-wider text-slate-600 mb-4 flex items-center">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500 mr-2 shadow-sm"></span> Nilai Sebelum (Old Values)
                    </h4>
                    @if(is_array($oldValuesParsed) && count($oldValuesParsed) > 0)
                        <div class="bg-white rounded-xl border border-slate-300 overflow-hidden shadow-sm">
                            <table class="w-full text-left border-collapse text-xs">
                                <tbody class="divide-y divide-slate-200">
                                    @foreach($oldValuesParsed as $key => $val)
                                        <tr class="hover:bg-slate-50">
                                            <td class="py-2.5 px-3.5 font-bold text-slate-700 bg-slate-100 w-2/5 border-r border-slate-300">
                                                {{ $labelMapping[$key] ?? \Illuminate\Support\Str::headline($key) }}
                                            </td>
                                            <td class="py-2.5 px-3.5 text-slate-800 font-semibold border-r border-slate-200">
                                                {!! $formatDisplayValue($key, $val) !!}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @elseif(is_string($oldValuesParsed) && trim($oldValuesParsed) !== '')
                        <div class="bg-white p-3.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 leading-relaxed">
                            {{ $oldValuesParsed }}
                        </div>
                    @else
                        <p class="text-xs text-slate-400 italic">Tidak ada nilai lama (Record Baru)</p>
                    @endif
                </div>

                <!-- Nilai Sesudah (New Values) -->
                <div class="bg-slate-50/70 border border-slate-300 rounded-2xl p-5">
                    <h4 class="font-bold text-xs uppercase tracking-wider text-slate-600 mb-4 flex items-center">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 mr-2 shadow-sm"></span> Nilai Sesudah (New Values)
                    </h4>
                    @if(is_array($newValuesParsed) && count($newValuesParsed) > 0)
                        <div class="bg-white rounded-xl border border-slate-300 overflow-hidden shadow-sm">
                            <table class="w-full text-left border-collapse text-xs">
                                <tbody class="divide-y divide-slate-200">
                                    @foreach($newValuesParsed as $key => $val)
                                        <tr class="hover:bg-slate-50">
                                            <td class="py-2.5 px-3.5 font-bold text-slate-700 bg-slate-100 w-2/5 border-r border-slate-300">
                                                {{ $labelMapping[$key] ?? \Illuminate\Support\Str::headline($key) }}
                                            </td>
                                            <td class="py-2.5 px-3.5 text-slate-800 font-semibold border-r border-slate-200">
                                                {!! $formatDisplayValue($key, $val) !!}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @elseif(is_string($newValuesParsed) && trim($newValuesParsed) !== '')
                        <div class="bg-white p-3.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 leading-relaxed">
                            {{ $newValuesParsed }}
                        </div>
                    @else
                        <p class="text-xs text-slate-400 italic">Tidak ada perubahan nilai sesudah</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
