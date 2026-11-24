<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-4 min-w-0">
                <x-back-button />
                <div class="min-w-0">
                    <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                        {{ __('Pusat Kontrol Administrator') }}
                    </h2>
                </div>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                Administrator Access
            </span>
        </div>
    </x-slot>

    <div class="space-y-8">
        <!-- Top Stats Overview Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total User System</p>
                    <h3 class="text-2xl font-black text-slate-900 mt-1">{{ number_format($stats['total_users']) }}</h3>
                    <p class="text-[11px] text-emerald-600 font-medium mt-1">Pengguna Terdaftar</p>
                </div>
                <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Katalog Barang</p>
                    <h3 class="text-2xl font-black text-slate-900 mt-1">{{ number_format($stats['total_barangs']) }}</h3>
                    <p class="text-[11px] text-indigo-600 font-medium mt-1">Item Produk Terdaftar</p>
                </div>
                <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Gudang</p>
                    <h3 class="text-2xl font-black text-slate-900 mt-1">{{ number_format($stats['total_warehouses']) }}</h3>
                    <p class="text-[11px] text-emerald-600 font-medium mt-1">{{ $stats['active_warehouses'] }} Gudang Aktif</p>
                </div>
                <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Lokasi / Rak Gudang</p>
                    <h3 class="text-2xl font-black text-slate-900 mt-1">{{ number_format($stats['total_locations']) }}</h3>
                    <p class="text-[11px] text-emerald-600 font-medium mt-1">{{ $stats['active_locations'] }} Rak Aktif</p>
                </div>
                <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s-8-1.79-8-4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Master Data Management Section -->
        <div>
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Pengaturan Master Data Dinamis</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Kelola data dasar sistem tanpa mengubah kode program.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <!-- Card 1: Kelompok / Kategori Barang -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between space-y-4 hover:border-indigo-300 transition-all">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="p-2.5 bg-indigo-50 text-indigo-600 rounded-xl">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h10M7 12h10M7 17h10M3 7h.01M3 12h.01M3 17h.01" />
                                </svg>
                            </span>
                            <span class="text-xs font-bold px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg">
                                {{ $stats['active_categories'] }} / {{ $stats['total_categories'] }} Aktif
                            </span>
                        </div>
                        <h4 class="font-bold text-base text-slate-900 mt-4">Kelompok / Kategori Barang</h4>
                        <p class="text-xs text-slate-500 mt-1">Klasifikasi kategori untuk mengelompokkan bahan baku dan produk.</p>
                    </div>
                    <div class="flex items-center space-x-2 pt-2 border-t border-slate-100">
                        <a href="{{ route('categories.create') }}" class="flex-1 text-center py-2 px-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold transition-all">
                            + Tambah Baru
                        </a>
                        <a href="{{ route('categories.index') }}" class="flex-1 text-center py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition-all">
                            Kelola Data
                        </a>
                    </div>
                </div>

                <!-- Card 2: Satuan Barang -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between space-y-4 hover:border-indigo-300 transition-all">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="p-2.5 bg-indigo-50 text-indigo-600 rounded-xl">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 18h12l3-18H3zm3 0V4a2 2 0 012-2h8a2 2 0 012 2v2" />
                                </svg>
                            </span>
                            <span class="text-xs font-bold px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg">
                                {{ $stats['active_units'] }} / {{ $stats['total_units'] }} Aktif
                            </span>
                        </div>
                        <h4 class="font-bold text-base text-slate-900 mt-4">Satuan Barang</h4>
                        <p class="text-xs text-slate-500 mt-1">Pengaturan unit pengukuran (Kg, Pcs, Box, Pack, Ball, dll).</p>
                    </div>
                    <div class="flex items-center space-x-2 pt-2 border-t border-slate-100">
                        <a href="{{ route('units.create') }}" class="flex-1 text-center py-2 px-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold transition-all">
                            + Tambah Baru
                        </a>
                        <a href="{{ route('units.index') }}" class="flex-1 text-center py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition-all">
                            Kelola Data
                        </a>
                    </div>
                </div>

                <!-- Card 3: Data Gudang -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between space-y-4 hover:border-indigo-300 transition-all">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="p-2.5 bg-emerald-50 text-emerald-600 rounded-xl">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </span>
                            <span class="text-xs font-bold px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg">
                                {{ $stats['active_warehouses'] }} / {{ $stats['total_warehouses'] }} Aktif
                            </span>
                        </div>
                        <h4 class="font-bold text-base text-slate-900 mt-4">Data Gudang</h4>
                        <p class="text-xs text-slate-500 mt-1">Kelola daftar gudang fisik penyimpanan stok operasional.</p>
                    </div>
                    <div class="flex items-center space-x-2 pt-2 border-t border-slate-100">
                        <a href="{{ route('warehouses.create') }}" class="flex-1 text-center py-2 px-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold transition-all">
                            + Tambah Baru
                        </a>
                        <a href="{{ route('warehouses.index') }}" class="flex-1 text-center py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition-all">
                            Kelola Data
                        </a>
                    </div>
                </div>

                <!-- Card 4: Tipe Gudang -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between space-y-4 hover:border-indigo-300 transition-all">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="p-2.5 bg-indigo-50 text-indigo-600 rounded-xl">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                                </svg>
                            </span>
                            <span class="text-xs font-bold px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg">
                                {{ $stats['active_warehouse_types'] }} / {{ $stats['total_warehouse_types'] }} Aktif
                            </span>
                        </div>
                        <h4 class="font-bold text-base text-slate-900 mt-4">Tipe Gudang</h4>
                        <p class="text-xs text-slate-500 mt-1">Jenis spesifikasi gudang (Cold Storage, Dry Warehouse, dll).</p>
                    </div>
                    <div class="flex items-center space-x-2 pt-2 border-t border-slate-100">
                        <a href="{{ route('warehouse-types.create') }}" class="flex-1 text-center py-2 px-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold transition-all">
                            + Tambah Baru
                        </a>
                        <a href="{{ route('warehouse-types.index') }}" class="flex-1 text-center py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition-all">
                            Kelola Data
                        </a>
                    </div>
                </div>

                <!-- Card 5: Lokasi & Rak Gudang -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between space-y-4 hover:border-indigo-300 transition-all">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="p-2.5 bg-indigo-50 text-indigo-600 rounded-xl">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s-8-1.79-8-4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                                </svg>
                            </span>
                            <span class="text-xs font-bold px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg">
                                {{ $stats['active_locations'] }} / {{ $stats['total_locations'] }} Aktif
                            </span>
                        </div>
                        <h4 class="font-bold text-base text-slate-900 mt-4">Lokasi & Rak Gudang</h4>
                        <p class="text-xs text-slate-500 mt-1">Pemetaan lokasi rak, baris, dan alokasi tempat simpan stok.</p>
                    </div>
                    <div class="flex items-center space-x-2 pt-2 border-t border-slate-100">
                        <a href="{{ route('locations.create') }}" class="flex-1 text-center py-2 px-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold transition-all">
                            + Tambah Baru
                        </a>
                        <a href="{{ route('locations.index') }}" class="flex-1 text-center py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition-all">
                            Kelola Data
                        </a>
                    </div>
                </div>

                <!-- Card 6: Data Supplier -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between space-y-4 hover:border-indigo-300 transition-all">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="p-2.5 bg-indigo-50 text-indigo-600 rounded-xl">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </span>
                            <span class="text-xs font-bold px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg">
                                {{ $stats['active_suppliers'] }} / {{ $stats['total_suppliers'] }} Aktif
                            </span>
                        </div>
                        <h4 class="font-bold text-base text-slate-900 mt-4">Data Supplier</h4>
                        <p class="text-xs text-slate-500 mt-1">Mitra penyedia barang dan kontaknya untuk transaksi PO & PR.</p>
                    </div>
                    <div class="flex items-center space-x-2 pt-2 border-t border-slate-100">
                        <a href="{{ route('suppliers.create') }}" class="flex-1 text-center py-2 px-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold transition-all">
                            + Tambah Baru
                        </a>
                        <a href="{{ route('suppliers.index') }}" class="flex-1 text-center py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition-all">
                            Kelola Data
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- System & Users Control Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- User Management Card -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div>
                        <h4 class="font-bold text-lg text-slate-900">Manajemen Pengguna & Akses</h4>
                        <p class="text-xs text-slate-500 mt-0.5">Kelola akun staf, role (Admin, Gudang, Purchasing, Produksi), dan hak akses.</p>
                    </div>
                    <a href="{{ route('users.index') }}" class="px-3 py-2 bg-indigo-600 text-white rounded-xl text-xs font-semibold hover:bg-indigo-700 transition-all shrink-0">
                        Kelola User
                    </a>
                </div>
                <div class="flex items-center justify-between text-xs text-slate-600 bg-slate-50 p-4 rounded-xl">
                    <span>Total Pengguna Aktif: <strong>{{ $stats['total_users'] }} User</strong></span>
                    <a href="{{ route('users.create') }}" class="text-indigo-600 font-bold hover:underline">+ Buat Akun Baru</a>
                </div>
            </div>

            <!-- Audit Logs Card -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div>
                        <h4 class="font-bold text-lg text-slate-900">Histori Audit Log System</h4>
                        <p class="text-xs text-slate-500 mt-0.5">Pantau jejak aktivitas dan riwayat perubahan data oleh semua pengguna.</p>
                    </div>
                    <a href="{{ route('audit-logs.index') }}" class="px-3 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-semibold hover:bg-slate-200 transition-all shrink-0">
                        Lihat Log
                    </a>
                </div>
                <div class="space-y-2">
                    @forelse($stats['recent_audit_logs'] as $log)
                        <div class="flex items-center justify-between text-xs py-1.5 border-b border-slate-100 last:border-0">
                            <span class="font-medium text-slate-700">{{ $log->user->name ?? 'System' }}: <span class="text-slate-500">{{ Str::limit($log->action, 45) }}</span></span>
                            <span class="text-slate-400 text-[10px] shrink-0">{{ $log->created_at->diffForHumans() }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-2">Belum ada catatan aktivitas.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
