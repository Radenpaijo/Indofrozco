@php
    $masterActive = request()->routeIs('barangs.*') || request()->routeIs('suppliers.*') || request()->routeIs('warehouses.*') || request()->routeIs('warehouse-types.*') || request()->routeIs('locations.*') || request()->routeIs('categories.*') || request()->routeIs('units.*');
    $procurementActive = request()->routeIs('permintaan-pembelian.*') || request()->routeIs('purchase-orders.*');
    $operasiActive = request()->routeIs('barang-masuk.*') || request()->routeIs('barang-keluar.*') || request()->routeIs('permintaan-barang.*') || request()->routeIs('stock-opnames.*') || request()->routeIs('stock-adjustments.*') || request()->routeIs('stock-mutations.*');
    $laporanActive = request()->routeIs('reports.*') || request()->routeIs('audit-logs.*');
    $sistemActive = request()->routeIs('users.*');
@endphp

<aside id="sidebar-nav" 
    style="background-color: #161d27 !important;"
    class="fixed top-0 bottom-0 md:top-16 left-0 z-40 w-64 bg-slate-950 text-slate-300 flex flex-col justify-between transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out border-r border-slate-800 shadow-2xl font-sans">
    
    <!-- Top & Navigation Links -->
    <div class="flex flex-col overflow-y-auto no-scrollbar flex-grow bg-slate-950" style="background-color: #161d27 !important;">
        <!-- Sidebar Navigation Header (Matching Reference Image: ☰ NAVIGATION) -->
        <div class="h-11 flex items-center justify-between px-4 bg-slate-900 border-b border-slate-800" style="background-color: #111720 !important;">
            <div class="flex items-center space-x-2.5 text-slate-200">
                <svg class="h-4 w-4 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <span class="text-xs font-black tracking-wider uppercase text-slate-100">NAVIGATION</span>
            </div>
            <!-- Mobile Close Button -->
            <button id="sidebar-close-btn" class="md:hidden p-1 text-slate-400 hover:text-white rounded focus:outline-none">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Nav Links List -->
        <nav class="p-2 space-y-1 bg-slate-950" style="background-color: #161d27 !important;">
            
            <!-- DASHBOARD -->
            <a href="{{ route('dashboard') }}" 
                style="{{ request()->routeIs('dashboard') ? 'background-color: #1c2635 !important;' : '' }}"
                class="flex items-center justify-between px-3 py-2.5 text-xs font-medium rounded transition-all duration-150 group 
                {{ request()->routeIs('dashboard') ? 'text-sky-400 font-semibold border border-slate-700' : 'text-slate-300 hover:bg-slate-900 hover:text-white border border-transparent' }}">
                <div class="flex items-center space-x-3">
                    <svg class="h-4 w-4 flex-shrink-0 {{ request()->routeIs('dashboard') ? 'text-sky-400' : 'text-slate-400 group-hover:text-slate-200' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span>Dashboard</span>
                </div>
            </a>


            <!-- MASTER DATA ACCORDION -->
            @if(auth()->user()->hasPermission('barangs') || auth()->user()->hasPermission('categories') || auth()->user()->hasPermission('units') || auth()->user()->hasPermission('suppliers') || auth()->user()->hasPermission('warehouses') || auth()->user()->hasPermission('locations') || auth()->user()->hasPermission('warehouse_types'))
                <div class="space-y-1">
                    <button type="button" 
                        onclick="toggleNavAccordion(this)"
                        style="{{ $masterActive ? 'background-color: #1c2635 !important;' : '' }}"
                        class="w-full flex items-center justify-between px-3 py-2.5 text-xs font-medium rounded transition-all duration-150 group cursor-pointer select-none 
                        {{ $masterActive ? 'text-sky-400 font-semibold border border-slate-600 shadow-inner' : 'text-slate-300 hover:bg-slate-900 hover:text-white border border-transparent' }}">
                        <div class="flex items-center space-x-3">
                            <svg class="h-4 w-4 flex-shrink-0 {{ $masterActive ? 'text-sky-400' : 'text-slate-400 group-hover:text-slate-200' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            <span>Master Data</span>
                        </div>
                        <svg class="accordion-chevron h-3.5 w-3.5 transform transition-transform duration-200 {{ $masterActive ? 'rotate-180 text-sky-400' : 'text-sky-500 group-hover:text-sky-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Submenu Items with » bullet prefix -->
                    <div class="accordion-content space-y-1 pt-0.5 pb-1 transition-all duration-200 bg-slate-950 rounded-md {{ $masterActive ? '' : 'hidden' }}" style="background-color: #121922 !important;">
                        @if(auth()->user()->hasPermission('barangs'))
                            <a href="{{ route('barangs.index') }}" class="flex items-center pl-7 pr-3 py-2 text-xs font-normal transition-all duration-150 group 
                                {{ request()->routeIs('barangs.*') ? 'text-sky-400 font-semibold' : 'text-slate-400 hover:text-slate-200' }}">
                                <span class="mr-2 text-sky-400 font-bold text-[11px]">&raquo;</span>
                                Daftar Barang
                            </a>
                        @endif

                        @if(auth()->user()->hasPermission('categories'))
                            <a href="{{ route('categories.index') }}" class="flex items-center pl-7 pr-3 py-2 text-xs font-normal transition-all duration-150 group 
                                {{ request()->routeIs('categories.*') ? 'text-sky-400 font-semibold' : 'text-slate-400 hover:text-slate-200' }}">
                                <span class="mr-2 text-sky-400 font-bold text-[11px]">&raquo;</span>
                                Kategori Barang
                            </a>
                        @endif

                        @if(auth()->user()->hasPermission('units'))
                            <a href="{{ route('units.index') }}" class="flex items-center pl-7 pr-3 py-2 text-xs font-normal transition-all duration-150 group 
                                {{ request()->routeIs('units.*') ? 'text-sky-400 font-semibold' : 'text-slate-400 hover:text-slate-200' }}">
                                <span class="mr-2 text-sky-400 font-bold text-[11px]">&raquo;</span>
                                Satuan
                            </a>
                        @endif

                        @if(auth()->user()->hasPermission('suppliers'))
                            <a href="{{ route('suppliers.index') }}" class="flex items-center pl-7 pr-3 py-2 text-xs font-normal transition-all duration-150 group 
                                {{ request()->routeIs('suppliers.*') ? 'text-sky-400 font-semibold' : 'text-slate-400 hover:text-slate-200' }}">
                                <span class="mr-2 text-sky-400 font-bold text-[11px]">&raquo;</span>
                                Supplier
                            </a>
                        @endif

                        @if(auth()->user()->hasPermission('warehouses'))
                            <a href="{{ route('warehouses.index') }}" class="flex items-center pl-7 pr-3 py-2 text-xs font-normal transition-all duration-150 group 
                                {{ request()->routeIs('warehouses.*') ? 'text-sky-400 font-semibold' : 'text-slate-400 hover:text-slate-200' }}">
                                <span class="mr-2 text-sky-400 font-bold text-[11px]">&raquo;</span>
                                Daftar Gudang
                            </a>
                        @endif

                        @if(auth()->user()->hasPermission('warehouse_types'))
                            <a href="{{ route('warehouse-types.index') }}" class="flex items-center pl-7 pr-3 py-2 text-xs font-normal transition-all duration-150 group 
                                {{ request()->routeIs('warehouse-types.*') ? 'text-sky-400 font-semibold' : 'text-slate-400 hover:text-slate-200' }}">
                                <span class="mr-2 text-sky-400 font-bold text-[11px]">&raquo;</span>
                                Jenis Gudang
                            </a>
                        @endif

                        @if(auth()->user()->hasPermission('locations'))
                            <a href="{{ route('locations.index') }}" class="flex items-center pl-7 pr-3 py-2 text-xs font-normal transition-all duration-150 group 
                                {{ request()->routeIs('locations.*') ? 'text-sky-400 font-semibold' : 'text-slate-400 hover:text-slate-200' }}">
                                <span class="mr-2 text-sky-400 font-bold text-[11px]">&raquo;</span>
                                Lokasi Rak
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            <!-- PEMBELIAN ACCORDION -->
            @if(auth()->user()->hasPermission('permintaan_pembelian') || auth()->user()->hasPermission('purchase_orders'))
                <div class="space-y-1">
                    <button type="button" 
                        onclick="toggleNavAccordion(this)"
                        style="{{ $procurementActive ? 'background-color: #1c2635 !important;' : '' }}"
                        class="w-full flex items-center justify-between px-3 py-2.5 text-xs font-medium rounded transition-all duration-150 group cursor-pointer select-none 
                        {{ $procurementActive ? 'text-sky-400 font-semibold border border-slate-600 shadow-inner' : 'text-slate-300 hover:bg-slate-900 hover:text-white border border-transparent' }}">
                        <div class="flex items-center space-x-3">
                            <svg class="h-4 w-4 flex-shrink-0 {{ $procurementActive ? 'text-sky-400' : 'text-slate-400 group-hover:text-slate-200' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z" />
                            </svg>
                            <span>Pembelian</span>
                        </div>
                        <svg class="accordion-chevron h-3.5 w-3.5 transform transition-transform duration-200 {{ $procurementActive ? 'rotate-180 text-sky-400' : 'text-sky-500 group-hover:text-sky-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div class="accordion-content space-y-1 pt-0.5 pb-1 transition-all duration-200 bg-slate-950 rounded-md {{ $procurementActive ? '' : 'hidden' }}" style="background-color: #121922 !important;">
                        @if(auth()->user()->hasPermission('permintaan_pembelian'))
                            <a href="{{ route('permintaan-pembelian.index') }}" class="flex items-center pl-7 pr-3 py-2 text-xs font-normal transition-all duration-150 group 
                                {{ request()->routeIs('permintaan-pembelian.*') ? 'text-sky-400 font-semibold' : 'text-slate-400 hover:text-slate-200' }}">
                                <span class="mr-2 text-sky-400 font-bold text-[11px]">&raquo;</span>
                                Pengajuan Beli (PR)
                            </a>
                        @endif

                        @if(auth()->user()->hasPermission('purchase_orders'))
                            <a href="{{ route('purchase-orders.index') }}" class="flex items-center pl-7 pr-3 py-2 text-xs font-normal transition-all duration-150 group 
                                {{ request()->routeIs('purchase-orders.*') ? 'text-sky-400 font-semibold' : 'text-slate-400 hover:text-slate-200' }}">
                                <span class="mr-2 text-sky-400 font-bold text-[11px]">&raquo;</span>
                                Pesanan Pembelian (PO)
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            <!-- GUDANG ACCORDION -->
            @if(auth()->user()->hasPermission('barang_masuk') || auth()->user()->hasPermission('barang_keluar') || auth()->user()->hasPermission('permintaan_barang') || auth()->user()->hasPermission('stock_opnames') || auth()->user()->hasPermission('stock_adjustments') || auth()->user()->hasPermission('stock_mutations'))
                <div class="space-y-1">
                    <button type="button" 
                        onclick="toggleNavAccordion(this)"
                        style="{{ $operasiActive ? 'background-color: #1c2635 !important;' : '' }}"
                        class="w-full flex items-center justify-between px-3 py-2.5 text-xs font-medium rounded transition-all duration-150 group cursor-pointer select-none 
                        {{ $operasiActive ? 'text-sky-400 font-semibold border border-slate-600 shadow-inner' : 'text-slate-300 hover:bg-slate-900 hover:text-white border border-transparent' }}">
                        <div class="flex items-center space-x-3">
                            <svg class="h-4 w-4 flex-shrink-0 {{ $operasiActive ? 'text-sky-400' : 'text-slate-400 group-hover:text-slate-200' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <span>Gudang</span>
                        </div>
                        <svg class="accordion-chevron h-3.5 w-3.5 transform transition-transform duration-200 {{ $operasiActive ? 'rotate-180 text-sky-400' : 'text-sky-500 group-hover:text-sky-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div class="accordion-content space-y-1 pt-0.5 pb-1 transition-all duration-200 bg-slate-950 rounded-md {{ $operasiActive ? '' : 'hidden' }}" style="background-color: #121922 !important;">
                        @if(auth()->user()->hasPermission('barang_masuk'))
                            <a href="{{ route('barang-masuk.index') }}" class="flex items-center pl-7 pr-3 py-2 text-xs font-normal transition-all duration-150 group 
                                {{ request()->routeIs('barang-masuk.*') ? 'text-sky-400 font-semibold' : 'text-slate-400 hover:text-slate-200' }}">
                                <span class="mr-2 text-sky-400 font-bold text-[11px]">&raquo;</span>
                                Barang Masuk
                            </a>
                        @endif

                        @if(auth()->user()->hasPermission('barang_keluar'))
                            <a href="{{ route('barang-keluar.index') }}" class="flex items-center pl-7 pr-3 py-2 text-xs font-normal transition-all duration-150 group 
                                {{ request()->routeIs('barang-keluar.*') ? 'text-sky-400 font-semibold' : 'text-slate-400 hover:text-slate-200' }}">
                                <span class="mr-2 text-sky-400 font-bold text-[11px]">&raquo;</span>
                                Barang Keluar
                            </a>
                        @endif

                        @if(auth()->user()->hasPermission('permintaan_barang'))
                            <a href="{{ route('permintaan-barang.index') }}" class="flex items-center pl-7 pr-3 py-2 text-xs font-normal transition-all duration-150 group 
                                {{ request()->routeIs('permintaan-barang.*') ? 'text-sky-400 font-semibold' : 'text-slate-400 hover:text-slate-200' }}">
                                <span class="mr-2 text-sky-400 font-bold text-[11px]">&raquo;</span>
                                Permintaan Barang
                            </a>
                        @endif

                        @if(auth()->user()->hasPermission('stock_opnames'))
                            <a href="{{ route('stock-opnames.index') }}" class="flex items-center pl-7 pr-3 py-2 text-xs font-normal transition-all duration-150 group 
                                {{ request()->routeIs('stock-opnames.*') ? 'text-sky-400 font-semibold' : 'text-slate-400 hover:text-slate-200' }}">
                                <span class="mr-2 text-sky-400 font-bold text-[11px]">&raquo;</span>
                                Cek Fisik Stok (Opname)
                            </a>
                        @endif

                        @if(auth()->user()->hasPermission('stock_adjustments'))
                            <a href="{{ route('stock-adjustments.index') }}" class="flex items-center pl-7 pr-3 py-2 text-xs font-normal transition-all duration-150 group 
                                {{ request()->routeIs('stock-adjustments.*') ? 'text-sky-400 font-semibold' : 'text-slate-400 hover:text-slate-200' }}">
                                <span class="mr-2 text-sky-400 font-bold text-[11px]">&raquo;</span>
                                Penyesuaian Stok
                            </a>
                        @endif

                        @if(auth()->user()->hasPermission('stock_mutations'))
                            <a href="{{ route('stock-mutations.index') }}" class="flex items-center pl-7 pr-3 py-2 text-xs font-normal transition-all duration-150 group 
                                {{ request()->routeIs('stock-mutations.*') ? 'text-sky-400 font-semibold' : 'text-slate-400 hover:text-slate-200' }}">
                                <span class="mr-2 text-sky-400 font-bold text-[11px]">&raquo;</span>
                                Pindah Rak / Lokasi
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            <!-- LAPORAN ACCORDION -->
            @if(auth()->user()->hasPermission('reports') || auth()->user()->hasPermission('audit_logs'))
                <div class="space-y-1">
                    <button type="button" 
                        onclick="toggleNavAccordion(this)"
                        style="{{ $laporanActive ? 'background-color: #1c2635 !important;' : '' }}"
                        class="w-full flex items-center justify-between px-3 py-2.5 text-xs font-medium rounded transition-all duration-150 group cursor-pointer select-none 
                        {{ $laporanActive ? 'text-sky-400 font-semibold border border-slate-600 shadow-inner' : 'text-slate-300 hover:bg-slate-900 hover:text-white border border-transparent' }}">
                        <div class="flex items-center space-x-3">
                            <svg class="h-4 w-4 flex-shrink-0 {{ $laporanActive ? 'text-sky-400' : 'text-slate-400 group-hover:text-slate-200' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>Laporan</span>
                        </div>
                        <svg class="accordion-chevron h-3.5 w-3.5 transform transition-transform duration-200 {{ $laporanActive ? 'rotate-180 text-sky-400' : 'text-sky-500 group-hover:text-sky-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div class="accordion-content space-y-1 pt-0.5 pb-1 transition-all duration-200 bg-slate-950 rounded-md {{ $laporanActive ? '' : 'hidden' }}" style="background-color: #121922 !important;">
                        @if(auth()->user()->hasPermission('reports'))
                            <a href="{{ route('reports.index') }}" class="flex items-center pl-7 pr-3 py-2 text-xs font-normal transition-all duration-150 group 
                                {{ request()->routeIs('reports.*') ? 'text-sky-400 font-semibold' : 'text-slate-400 hover:text-slate-200' }}">
                                <span class="mr-2 text-sky-400 font-bold text-[11px]">&raquo;</span>
                                Laporan Gudang & Stok
                            </a>
                        @endif

                        @if(auth()->user()->hasPermission('audit_logs'))
                            <a href="{{ route('audit-logs.index') }}" class="flex items-center pl-7 pr-3 py-2 text-xs font-normal transition-all duration-150 group 
                                {{ request()->routeIs('audit-logs.*') ? 'text-sky-400 font-semibold' : 'text-slate-400 hover:text-slate-200' }}">
                                <span class="mr-2 text-sky-400 font-bold text-[11px]">&raquo;</span>
                                Riwayat Aktivitas
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            <!-- PENGATURAN ACCORDION -->
            @if(auth()->user()->hasPermission('users'))
                <div class="space-y-1">
                    <button type="button" 
                        onclick="toggleNavAccordion(this)"
                        style="{{ $sistemActive ? 'background-color: #1c2635 !important;' : '' }}"
                        class="w-full flex items-center justify-between px-3 py-2.5 text-xs font-medium rounded transition-all duration-150 group cursor-pointer select-none 
                        {{ $sistemActive ? 'text-sky-400 font-semibold border border-slate-600 shadow-inner' : 'text-slate-300 hover:bg-slate-900 hover:text-white border border-transparent' }}">
                        <div class="flex items-center space-x-3">
                            <svg class="h-4 w-4 flex-shrink-0 {{ $sistemActive ? 'text-sky-400' : 'text-slate-400 group-hover:text-slate-200' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            </svg>
                            <span>Pengaturan</span>
                        </div>
                        <svg class="accordion-chevron h-3.5 w-3.5 transform transition-transform duration-200 {{ $sistemActive ? 'rotate-180 text-sky-400' : 'text-sky-500 group-hover:text-sky-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div class="accordion-content space-y-1 pt-0.5 pb-1 transition-all duration-200 bg-slate-950 rounded-md {{ $sistemActive ? '' : 'hidden' }}" style="background-color: #121922 !important;">
                        <a href="{{ route('users.index') }}" class="flex items-center pl-7 pr-3 py-2 text-xs font-normal transition-all duration-150 group 
                            {{ request()->routeIs('users.*') ? 'text-sky-400 font-semibold' : 'text-slate-400 hover:text-slate-200' }}">
                            <span class="mr-2 text-sky-400 font-bold text-[11px]">&raquo;</span>
                            Kelola Pengguna
                        </a>
                    </div>
                </div>
            @endif

        </nav>
    </div>

    <!-- Bottom Mobile Profile -->
    <div class="p-3 border-t border-slate-800 bg-slate-900 space-y-1 md:hidden" style="background-color: #111720 !important;">
        <a href="{{ route('profile.edit') }}" class="flex items-center px-3 py-2 text-xs font-semibold text-slate-400 hover:text-white rounded hover:bg-slate-800 transition-colors">
            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            Edit Profil
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center px-3 py-2 text-xs font-semibold text-rose-400 hover:text-rose-300 rounded hover:bg-rose-900 transition-colors text-left">
                <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Keluar (Log Out)
            </button>
        </form>
    </div>
</aside>

<script>
    function toggleNavAccordion(button) {
        const parent = button.closest('.space-y-1');
        if (!parent) return;
        
        const content = parent.querySelector('.accordion-content');
        const chevron = parent.querySelector('.accordion-chevron');
        
        if (content) {
            content.classList.toggle('hidden');
        }
        if (chevron) {
            chevron.classList.toggle('rotate-180');
        }
    }
</script>