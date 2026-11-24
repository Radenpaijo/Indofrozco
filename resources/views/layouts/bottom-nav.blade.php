@php
    $isDashboardActive = request()->routeIs('dashboard');
    $isMasterActive = request()->routeIs('barangs.*') || request()->routeIs('categories.*') || request()->routeIs('units.*') || request()->routeIs('suppliers.*') || request()->routeIs('warehouses.*') || request()->routeIs('warehouse-types.*') || request()->routeIs('locations.*');
    $isProcurementActive = request()->routeIs('permintaan-pembelian.*') || request()->routeIs('purchase-orders.*');
    $isGudangActive = request()->routeIs('barang-masuk.*') || request()->routeIs('barang-keluar.*') || request()->routeIs('permintaan-barang.*') || request()->routeIs('stock-opnames.*') || request()->routeIs('stock-adjustments.*') || request()->routeIs('stock-mutations.*');
    $isLaporanActive = request()->routeIs('reports.*') || request()->routeIs('audit-logs.*');
    $isSistemActive = request()->routeIs('users.*');
@endphp

<!-- Mobile Bottom Navigation & Sub-Menu Tray (Side Menu alternative for Mobile) -->
<div x-data="{ activeSubMenu: null }" class="lg:hidden">

    <!-- 1. Soft Backdrop Overlay when Sub-Menu is Open -->
    <div x-show="activeSubMenu !== null"
         x-transition:enter="transition-opacity ease-out duration-150"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="activeSubMenu = null"
         class="fixed inset-0 z-40 bg-slate-950/70 backdrop-blur-xs"
         style="display: none;">
    </div>

    <!-- 2. Sub-Menu Tray (Docks directly above bottom bar, styled identically to Side Menu: text list only, no boxes) -->
    <div x-show="activeSubMenu !== null"
         x-transition:enter="transition ease-out duration-200 transform"
         x-transition:enter-start="opacity-0 translate-y-3"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150 transform"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-3"
         style="background-color: #161d27 !important; display: none;"
         class="fixed bottom-14 sm:bottom-16 inset-x-0 max-w-lg mx-auto z-50 border-t border-x border-slate-800 rounded-t-xl shadow-2xl font-sans overflow-hidden">

        <!-- Sub-Menu Header Bar -->
        <div class="h-10 px-4 flex items-center justify-between border-b border-slate-800 shrink-0" style="background-color: #111720 !important;">
            <div class="flex items-center space-x-2">
                <span class="w-2 h-2 rounded-full bg-sky-400"></span>
                <span class="text-xs font-black tracking-wider uppercase text-slate-100"
                      x-text="activeSubMenu === 'master' ? 'Master Data' : 
                             (activeSubMenu === 'pembelian' ? 'Pembelian' : 
                             (activeSubMenu === 'gudang' ? 'Operasional Gudang' : 
                             (activeSubMenu === 'laporan' ? 'Laporan' : 'Pengaturan')))">
                </span>
            </div>
            <button @click="activeSubMenu = null" type="button" 
                    class="p-1 text-slate-400 hover:text-white hover:bg-slate-800 rounded focus:outline-none transition-colors"
                    title="Tutup Menu">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Sub-Menu Text Links (Exact same layout & styling as Side Menu sub-items) -->
        <div class="py-1 max-h-[60vh] overflow-y-auto divide-y divide-slate-800/40" style="background-color: #121922 !important;">

            <!-- SUB-MENU: MASTER DATA -->
            <div x-show="activeSubMenu === 'master'" class="space-y-0.5">
                @if(auth()->user()->hasPermission('barangs'))
                    <a href="{{ route('barangs.index') }}" 
                       class="flex items-center justify-between px-4 py-2.5 text-xs font-normal transition-colors duration-150 {{ request()->routeIs('barangs.*') ? 'text-sky-400 font-semibold bg-slate-800/60' : 'text-slate-300 hover:text-white hover:bg-slate-800/40' }}">
                        <div class="flex items-center">
                            <span class="mr-2.5 text-sky-400 font-bold text-xs">&raquo;</span>
                            <span>Daftar Barang</span>
                        </div>
                        @if(request()->routeIs('barangs.*'))
                            <span class="w-1.5 h-1.5 rounded-full bg-sky-400"></span>
                        @endif
                    </a>
                @endif

                @if(auth()->user()->hasPermission('categories'))
                    <a href="{{ route('categories.index') }}" 
                       class="flex items-center justify-between px-4 py-2.5 text-xs font-normal transition-colors duration-150 {{ request()->routeIs('categories.*') ? 'text-sky-400 font-semibold bg-slate-800/60' : 'text-slate-300 hover:text-white hover:bg-slate-800/40' }}">
                        <div class="flex items-center">
                            <span class="mr-2.5 text-sky-400 font-bold text-xs">&raquo;</span>
                            <span>Kategori Barang</span>
                        </div>
                        @if(request()->routeIs('categories.*'))
                            <span class="w-1.5 h-1.5 rounded-full bg-sky-400"></span>
                        @endif
                    </a>
                @endif

                @if(auth()->user()->hasPermission('units'))
                    <a href="{{ route('units.index') }}" 
                       class="flex items-center justify-between px-4 py-2.5 text-xs font-normal transition-colors duration-150 {{ request()->routeIs('units.*') ? 'text-sky-400 font-semibold bg-slate-800/60' : 'text-slate-300 hover:text-white hover:bg-slate-800/40' }}">
                        <div class="flex items-center">
                            <span class="mr-2.5 text-sky-400 font-bold text-xs">&raquo;</span>
                            <span>Satuan</span>
                        </div>
                        @if(request()->routeIs('units.*'))
                            <span class="w-1.5 h-1.5 rounded-full bg-sky-400"></span>
                        @endif
                    </a>
                @endif

                @if(auth()->user()->hasPermission('suppliers'))
                    <a href="{{ route('suppliers.index') }}" 
                       class="flex items-center justify-between px-4 py-2.5 text-xs font-normal transition-colors duration-150 {{ request()->routeIs('suppliers.*') ? 'text-sky-400 font-semibold bg-slate-800/60' : 'text-slate-300 hover:text-white hover:bg-slate-800/40' }}">
                        <div class="flex items-center">
                            <span class="mr-2.5 text-sky-400 font-bold text-xs">&raquo;</span>
                            <span>Supplier</span>
                        </div>
                        @if(request()->routeIs('suppliers.*'))
                            <span class="w-1.5 h-1.5 rounded-full bg-sky-400"></span>
                        @endif
                    </a>
                @endif

                @if(auth()->user()->hasPermission('warehouses'))
                    <a href="{{ route('warehouses.index') }}" 
                       class="flex items-center justify-between px-4 py-2.5 text-xs font-normal transition-colors duration-150 {{ request()->routeIs('warehouses.*') ? 'text-sky-400 font-semibold bg-slate-800/60' : 'text-slate-300 hover:text-white hover:bg-slate-800/40' }}">
                        <div class="flex items-center">
                            <span class="mr-2.5 text-sky-400 font-bold text-xs">&raquo;</span>
                            <span>Daftar Gudang</span>
                        </div>
                        @if(request()->routeIs('warehouses.*'))
                            <span class="w-1.5 h-1.5 rounded-full bg-sky-400"></span>
                        @endif
                    </a>
                @endif

                @if(auth()->user()->hasPermission('warehouse_types'))
                    <a href="{{ route('warehouse-types.index') }}" 
                       class="flex items-center justify-between px-4 py-2.5 text-xs font-normal transition-colors duration-150 {{ request()->routeIs('warehouse-types.*') ? 'text-sky-400 font-semibold bg-slate-800/60' : 'text-slate-300 hover:text-white hover:bg-slate-800/40' }}">
                        <div class="flex items-center">
                            <span class="mr-2.5 text-sky-400 font-bold text-xs">&raquo;</span>
                            <span>Jenis Gudang</span>
                        </div>
                        @if(request()->routeIs('warehouse-types.*'))
                            <span class="w-1.5 h-1.5 rounded-full bg-sky-400"></span>
                        @endif
                    </a>
                @endif

                @if(auth()->user()->hasPermission('locations'))
                    <a href="{{ route('locations.index') }}" 
                       class="flex items-center justify-between px-4 py-2.5 text-xs font-normal transition-colors duration-150 {{ request()->routeIs('locations.*') ? 'text-sky-400 font-semibold bg-slate-800/60' : 'text-slate-300 hover:text-white hover:bg-slate-800/40' }}">
                        <div class="flex items-center">
                            <span class="mr-2.5 text-sky-400 font-bold text-xs">&raquo;</span>
                            <span>Lokasi Rak</span>
                        </div>
                        @if(request()->routeIs('locations.*'))
                            <span class="w-1.5 h-1.5 rounded-full bg-sky-400"></span>
                        @endif
                    </a>
                @endif
            </div>

            <!-- SUB-MENU: PEMBELIAN -->
            <div x-show="activeSubMenu === 'pembelian'" class="space-y-0.5">
                @if(auth()->user()->hasPermission('permintaan_pembelian'))
                    <a href="{{ route('permintaan-pembelian.index') }}" 
                       class="flex items-center justify-between px-4 py-2.5 text-xs font-normal transition-colors duration-150 {{ request()->routeIs('permintaan-pembelian.*') ? 'text-sky-400 font-semibold bg-slate-800/60' : 'text-slate-300 hover:text-white hover:bg-slate-800/40' }}">
                        <div class="flex items-center">
                            <span class="mr-2.5 text-sky-400 font-bold text-xs">&raquo;</span>
                            <span>Pengajuan Beli (PR)</span>
                        </div>
                        @if(request()->routeIs('permintaan-pembelian.*'))
                            <span class="w-1.5 h-1.5 rounded-full bg-sky-400"></span>
                        @endif
                    </a>
                @endif

                @if(auth()->user()->hasPermission('purchase_orders'))
                    <a href="{{ route('purchase-orders.index') }}" 
                       class="flex items-center justify-between px-4 py-2.5 text-xs font-normal transition-colors duration-150 {{ request()->routeIs('purchase-orders.*') ? 'text-sky-400 font-semibold bg-slate-800/60' : 'text-slate-300 hover:text-white hover:bg-slate-800/40' }}">
                        <div class="flex items-center">
                            <span class="mr-2.5 text-sky-400 font-bold text-xs">&raquo;</span>
                            <span>Pesanan Pembelian (PO)</span>
                        </div>
                        @if(request()->routeIs('purchase-orders.*'))
                            <span class="w-1.5 h-1.5 rounded-full bg-sky-400"></span>
                        @endif
                    </a>
                @endif
            </div>

            <!-- SUB-MENU: GUDANG -->
            <div x-show="activeSubMenu === 'gudang'" class="space-y-0.5">
                @if(auth()->user()->hasPermission('barang_masuk'))
                    <a href="{{ route('barang-masuk.index') }}" 
                       class="flex items-center justify-between px-4 py-2.5 text-xs font-normal transition-colors duration-150 {{ request()->routeIs('barang-masuk.*') ? 'text-sky-400 font-semibold bg-slate-800/60' : 'text-slate-300 hover:text-white hover:bg-slate-800/40' }}">
                        <div class="flex items-center">
                            <span class="mr-2.5 text-sky-400 font-bold text-xs">&raquo;</span>
                            <span>Barang Masuk</span>
                        </div>
                        @if(request()->routeIs('barang-masuk.*'))
                            <span class="w-1.5 h-1.5 rounded-full bg-sky-400"></span>
                        @endif
                    </a>
                @endif

                @if(auth()->user()->hasPermission('barang_keluar'))
                    <a href="{{ route('barang-keluar.index') }}" 
                       class="flex items-center justify-between px-4 py-2.5 text-xs font-normal transition-colors duration-150 {{ request()->routeIs('barang-keluar.*') ? 'text-sky-400 font-semibold bg-slate-800/60' : 'text-slate-300 hover:text-white hover:bg-slate-800/40' }}">
                        <div class="flex items-center">
                            <span class="mr-2.5 text-sky-400 font-bold text-xs">&raquo;</span>
                            <span>Barang Keluar</span>
                        </div>
                        @if(request()->routeIs('barang-keluar.*'))
                            <span class="w-1.5 h-1.5 rounded-full bg-sky-400"></span>
                        @endif
                    </a>
                @endif

                @if(auth()->user()->hasPermission('permintaan_barang'))
                    <a href="{{ route('permintaan-barang.index') }}" 
                       class="flex items-center justify-between px-4 py-2.5 text-xs font-normal transition-colors duration-150 {{ request()->routeIs('permintaan-barang.*') ? 'text-sky-400 font-semibold bg-slate-800/60' : 'text-slate-300 hover:text-white hover:bg-slate-800/40' }}">
                        <div class="flex items-center">
                            <span class="mr-2.5 text-sky-400 font-bold text-xs">&raquo;</span>
                            <span>Permintaan Barang</span>
                        </div>
                        @if(request()->routeIs('permintaan-barang.*'))
                            <span class="w-1.5 h-1.5 rounded-full bg-sky-400"></span>
                        @endif
                    </a>
                @endif

                @if(auth()->user()->hasPermission('stock_opnames'))
                    <a href="{{ route('stock-opnames.index') }}" 
                       class="flex items-center justify-between px-4 py-2.5 text-xs font-normal transition-colors duration-150 {{ request()->routeIs('stock-opnames.*') ? 'text-sky-400 font-semibold bg-slate-800/60' : 'text-slate-300 hover:text-white hover:bg-slate-800/40' }}">
                        <div class="flex items-center">
                            <span class="mr-2.5 text-sky-400 font-bold text-xs">&raquo;</span>
                            <span>Cek Fisik Stok (Opname)</span>
                        </div>
                        @if(request()->routeIs('stock-opnames.*'))
                            <span class="w-1.5 h-1.5 rounded-full bg-sky-400"></span>
                        @endif
                    </a>
                @endif

                @if(auth()->user()->hasPermission('stock_adjustments'))
                    <a href="{{ route('stock-adjustments.index') }}" 
                       class="flex items-center justify-between px-4 py-2.5 text-xs font-normal transition-colors duration-150 {{ request()->routeIs('stock-adjustments.*') ? 'text-sky-400 font-semibold bg-slate-800/60' : 'text-slate-300 hover:text-white hover:bg-slate-800/40' }}">
                        <div class="flex items-center">
                            <span class="mr-2.5 text-sky-400 font-bold text-xs">&raquo;</span>
                            <span>Penyesuaian Stok</span>
                        </div>
                        @if(request()->routeIs('stock-adjustments.*'))
                            <span class="w-1.5 h-1.5 rounded-full bg-sky-400"></span>
                        @endif
                    </a>
                @endif

                @if(auth()->user()->hasPermission('stock_mutations'))
                    <a href="{{ route('stock-mutations.index') }}" 
                       class="flex items-center justify-between px-4 py-2.5 text-xs font-normal transition-colors duration-150 {{ request()->routeIs('stock-mutations.*') ? 'text-sky-400 font-semibold bg-slate-800/60' : 'text-slate-300 hover:text-white hover:bg-slate-800/40' }}">
                        <div class="flex items-center">
                            <span class="mr-2.5 text-sky-400 font-bold text-xs">&raquo;</span>
                            <span>Pindah Rak / Lokasi</span>
                        </div>
                        @if(request()->routeIs('stock-mutations.*'))
                            <span class="w-1.5 h-1.5 rounded-full bg-sky-400"></span>
                        @endif
                    </a>
                @endif
            </div>

            <!-- SUB-MENU: LAPORAN -->
            <div x-show="activeSubMenu === 'laporan'" class="space-y-0.5">
                @if(auth()->user()->hasPermission('reports'))
                    <a href="{{ route('reports.index') }}" 
                       class="flex items-center justify-between px-4 py-2.5 text-xs font-normal transition-colors duration-150 {{ request()->routeIs('reports.*') ? 'text-sky-400 font-semibold bg-slate-800/60' : 'text-slate-300 hover:text-white hover:bg-slate-800/40' }}">
                        <div class="flex items-center">
                            <span class="mr-2.5 text-sky-400 font-bold text-xs">&raquo;</span>
                            <span>Laporan Gudang & Stok</span>
                        </div>
                        @if(request()->routeIs('reports.*'))
                            <span class="w-1.5 h-1.5 rounded-full bg-sky-400"></span>
                        @endif
                    </a>
                @endif

                @if(auth()->user()->hasPermission('audit_logs'))
                    <a href="{{ route('audit-logs.index') }}" 
                       class="flex items-center justify-between px-4 py-2.5 text-xs font-normal transition-colors duration-150 {{ request()->routeIs('audit-logs.*') ? 'text-sky-400 font-semibold bg-slate-800/60' : 'text-slate-300 hover:text-white hover:bg-slate-800/40' }}">
                        <div class="flex items-center">
                            <span class="mr-2.5 text-sky-400 font-bold text-xs">&raquo;</span>
                            <span>Riwayat Aktivitas</span>
                        </div>
                        @if(request()->routeIs('audit-logs.*'))
                            <span class="w-1.5 h-1.5 rounded-full bg-sky-400"></span>
                        @endif
                    </a>
                @endif
            </div>

            <!-- SUB-MENU: PENGATURAN -->
            @if(auth()->user()->hasPermission('users'))
                <div x-show="activeSubMenu === 'pengaturan'" class="space-y-0.5">
                    <a href="{{ route('users.index') }}" 
                       class="flex items-center justify-between px-4 py-2.5 text-xs font-normal transition-colors duration-150 {{ request()->routeIs('users.*') ? 'text-sky-400 font-semibold bg-slate-800/60' : 'text-slate-300 hover:text-white hover:bg-slate-800/40' }}">
                        <div class="flex items-center">
                            <span class="mr-2.5 text-sky-400 font-bold text-xs">&raquo;</span>
                            <span>Kelola Pengguna</span>
                        </div>
                        @if(request()->routeIs('users.*'))
                            <span class="w-1.5 h-1.5 rounded-full bg-sky-400"></span>
                        @endif
                    </a>
                </div>
            @endif

        </div>
    </div>

    <!-- 3. Bottom Navigation Bar (Docked to bottom, matching top bar navy color & side menu icons) -->
    <div id="mobile-bottom-nav" 
         style="background-color: #111720 !important;"
         class="fixed bottom-0 inset-x-0 z-40 border-t border-slate-800 shadow-2xl select-none">
        <nav class="max-w-lg mx-auto h-14 sm:h-16 flex items-stretch justify-around px-1"
             aria-label="Navigasi Bawah">

            <!-- 1. DASHBOARD -->
            <a href="{{ route('dashboard') }}" @click="activeSubMenu = null"
               class="flex-1 flex flex-col items-center justify-center py-1 transition-colors relative"
               :class="(activeSubMenu === null && {{ $isDashboardActive ? 'true' : 'false' }}) ? 'text-sky-400 font-semibold' : 'text-slate-400 hover:text-slate-200'">
                <span class="absolute top-0 inset-x-2 h-0.5 rounded-full transition-all"
                      :class="(activeSubMenu === null && {{ $isDashboardActive ? 'true' : 'false' }}) ? 'bg-sky-400' : 'bg-transparent'"></span>
                <svg class="h-4 w-4 sm:h-5 sm:w-5 mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span class="text-[9.5px] sm:text-[10px] leading-tight">Dashboard</span>
            </a>

            <!-- 2. MASTER DATA -->
            @if(auth()->user()->hasPermission('barangs') || auth()->user()->hasPermission('categories') || auth()->user()->hasPermission('units') || auth()->user()->hasPermission('suppliers') || auth()->user()->hasPermission('warehouses') || auth()->user()->hasPermission('locations') || auth()->user()->hasPermission('warehouse_types'))
                <button type="button" @click="activeSubMenu = (activeSubMenu === 'master' ? null : 'master')"
                        class="flex-1 flex flex-col items-center justify-center py-1 transition-colors relative focus:outline-none"
                        :class="(activeSubMenu === 'master' || ({{ $isMasterActive ? 'true' : 'false' }} && activeSubMenu === null)) ? 'text-sky-400 font-semibold' : 'text-slate-400 hover:text-slate-200'">
                    <span class="absolute top-0 inset-x-2 h-0.5 rounded-full transition-all"
                          :class="(activeSubMenu === 'master' || ({{ $isMasterActive ? 'true' : 'false' }} && activeSubMenu === null)) ? 'bg-sky-400' : 'bg-transparent'"></span>
                    <svg class="h-4 w-4 sm:h-5 sm:w-5 mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <span class="text-[9.5px] sm:text-[10px] leading-tight flex items-center">
                        Master
                        <span class="text-[7px] ml-0.5 transform transition-transform" :class="activeSubMenu === 'master' ? 'rotate-180 text-sky-400' : 'text-slate-500'">▲</span>
                    </span>
                </button>
            @endif

            <!-- 3. PEMBELIAN -->
            @if(auth()->user()->hasPermission('permintaan_pembelian') || auth()->user()->hasPermission('purchase_orders'))
                <button type="button" @click="activeSubMenu = (activeSubMenu === 'pembelian' ? null : 'pembelian')"
                        class="flex-1 flex flex-col items-center justify-center py-1 transition-colors relative focus:outline-none"
                        :class="(activeSubMenu === 'pembelian' || ({{ $isProcurementActive ? 'true' : 'false' }} && activeSubMenu === null)) ? 'text-sky-400 font-semibold' : 'text-slate-400 hover:text-slate-200'">
                    <span class="absolute top-0 inset-x-2 h-0.5 rounded-full transition-all"
                          :class="(activeSubMenu === 'pembelian' || ({{ $isProcurementActive ? 'true' : 'false' }} && activeSubMenu === null)) ? 'bg-sky-400' : 'bg-transparent'"></span>
                    <svg class="h-4 w-4 sm:h-5 sm:w-5 mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z" />
                    </svg>
                    <span class="text-[9.5px] sm:text-[10px] leading-tight flex items-center">
                        Beli
                        <span class="text-[7px] ml-0.5 transform transition-transform" :class="activeSubMenu === 'pembelian' ? 'rotate-180 text-sky-400' : 'text-slate-500'">▲</span>
                    </span>
                </button>
            @endif

            <!-- 4. GUDANG -->
            @if(auth()->user()->hasPermission('barang_masuk') || auth()->user()->hasPermission('barang_keluar') || auth()->user()->hasPermission('permintaan_barang') || auth()->user()->hasPermission('stock_opnames') || auth()->user()->hasPermission('stock_adjustments') || auth()->user()->hasPermission('stock_mutations'))
                <button type="button" @click="activeSubMenu = (activeSubMenu === 'gudang' ? null : 'gudang')"
                        class="flex-1 flex flex-col items-center justify-center py-1 transition-colors relative focus:outline-none"
                        :class="(activeSubMenu === 'gudang' || ({{ $isGudangActive ? 'true' : 'false' }} && activeSubMenu === null)) ? 'text-sky-400 font-semibold' : 'text-slate-400 hover:text-slate-200'">
                    <span class="absolute top-0 inset-x-2 h-0.5 rounded-full transition-all"
                          :class="(activeSubMenu === 'gudang' || ({{ $isGudangActive ? 'true' : 'false' }} && activeSubMenu === null)) ? 'bg-sky-400' : 'bg-transparent'"></span>
                    <svg class="h-4 w-4 sm:h-5 sm:w-5 mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <span class="text-[9.5px] sm:text-[10px] leading-tight flex items-center">
                        Gudang
                        <span class="text-[7px] ml-0.5 transform transition-transform" :class="activeSubMenu === 'gudang' ? 'rotate-180 text-sky-400' : 'text-slate-500'">▲</span>
                    </span>
                </button>
            @endif

            <!-- 5. LAPORAN -->
            @if(auth()->user()->hasPermission('reports') || auth()->user()->hasPermission('audit_logs'))
                <button type="button" @click="activeSubMenu = (activeSubMenu === 'laporan' ? null : 'laporan')"
                        class="flex-1 flex flex-col items-center justify-center py-1 transition-colors relative focus:outline-none"
                        :class="(activeSubMenu === 'laporan' || ({{ $isLaporanActive ? 'true' : 'false' }} && activeSubMenu === null)) ? 'text-sky-400 font-semibold' : 'text-slate-400 hover:text-slate-200'">
                    <span class="absolute top-0 inset-x-2 h-0.5 rounded-full transition-all"
                          :class="(activeSubMenu === 'laporan' || ({{ $isLaporanActive ? 'true' : 'false' }} && activeSubMenu === null)) ? 'bg-sky-400' : 'bg-transparent'"></span>
                    <svg class="h-4 w-4 sm:h-5 sm:w-5 mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="text-[9.5px] sm:text-[10px] leading-tight flex items-center">
                        Laporan
                        <span class="text-[7px] ml-0.5 transform transition-transform" :class="activeSubMenu === 'laporan' ? 'rotate-180 text-sky-400' : 'text-slate-500'">▲</span>
                    </span>
                </button>
            @endif

            <!-- 6. PENGATURAN (Only for users with permission) -->
            @if(auth()->user()->hasPermission('users'))
                <button type="button" @click="activeSubMenu = (activeSubMenu === 'pengaturan' ? null : 'pengaturan')"
                        class="flex-1 flex flex-col items-center justify-center py-1 transition-colors relative focus:outline-none"
                        :class="(activeSubMenu === 'pengaturan' || ({{ $isSistemActive ? 'true' : 'false' }} && activeSubMenu === null)) ? 'text-sky-400 font-semibold' : 'text-slate-400 hover:text-slate-200'">
                    <span class="absolute top-0 inset-x-2 h-0.5 rounded-full transition-all"
                          :class="(activeSubMenu === 'pengaturan' || ({{ $isSistemActive ? 'true' : 'false' }} && activeSubMenu === null)) ? 'bg-sky-400' : 'bg-transparent'"></span>
                    <svg class="h-4 w-4 sm:h-5 sm:w-5 mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    </svg>
                    <span class="text-[9.5px] sm:text-[10px] leading-tight flex items-center">
                        Setting
                        <span class="text-[7px] ml-0.5 transform transition-transform" :class="activeSubMenu === 'pengaturan' ? 'rotate-180 text-sky-400' : 'text-slate-500'">▲</span>
                    </span>
                </button>
            @endif

        </nav>
    </div>
</div>
