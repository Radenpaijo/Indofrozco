<aside id="sidebar-nav" class="fixed top-0 bottom-0 md:top-16 left-0 z-40 w-64 bg-slate-950 text-slate-300 flex flex-col justify-between transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out border-r border-slate-800 shadow-2xl">
    
    <!-- Top & Navigation Links -->
    <div class="flex flex-col overflow-y-auto no-scrollbar flex-grow">
        <!-- Brand / Header (Mobile Only) -->
        <div class="h-16 flex items-center justify-between px-6 border-b border-slate-800 bg-slate-950 md:hidden">
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-2.5">
                <img src="{{ asset('images/logo.svg') }}" alt="INDOFROZ Logo" class="h-8 w-8 object-contain">
                <span class="text-lg font-black tracking-widest text-white">INDOFROZ</span>
                <span class="px-1.5 py-0.5 text-[9px] font-bold bg-indigo-500/20 text-indigo-400 rounded border border-indigo-500/30">WMS</span>
            </a>
            <!-- Mobile Close Button -->
            <button id="sidebar-close-btn" class="md:hidden p-1 text-slate-400 hover:text-white hover:bg-slate-900 rounded-md focus:outline-none">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Nav Links List -->
        <nav class="p-4 space-y-1 bg-slate-950/20">
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-150 group 
                {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/15' : 'hover:bg-slate-900 hover:text-white' }}">
                <svg class="h-5 w-5 mr-3 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Dashboard
            </a>

            <!-- Master Data Section -->
            @if(auth()->user()->hasPermission('categories') || auth()->user()->hasPermission('units') || auth()->user()->hasPermission('warehouses') || auth()->user()->hasPermission('locations') || auth()->user()->hasPermission('barangs') || auth()->user()->hasPermission('suppliers'))
                <hr class="border-slate-800/40 my-2 mx-2" />
                <div class="pt-1 pb-2 px-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Master Data</div>
                
                @if(auth()->user()->hasPermission('categories'))
                    <a href="{{ route('categories.index') }}" class="flex items-center px-4 py-2.5 text-xs font-medium rounded-xl transition-all duration-150 group 
                        {{ request()->routeIs('categories.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/15' : 'hover:bg-slate-900 hover:text-white' }}">
                        <svg class="h-4 w-4 mr-3 {{ request()->routeIs('categories.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 11h.01M7 15h.01M11 7h8M11 11h8M11 15h8" />
                        </svg>
                        Kategori Barang
                    </a>
                @endif

                @if(auth()->user()->hasPermission('units'))
                    <a href="{{ route('units.index') }}" class="flex items-center px-4 py-2.5 text-xs font-medium rounded-xl transition-all duration-150 group 
                        {{ request()->routeIs('units.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/15' : 'hover:bg-slate-900 hover:text-white' }}">
                        <svg class="h-4 w-4 mr-3 {{ request()->routeIs('units.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5 5 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5 5 0 006.001 0M18 7l3 9m-3-9l-6-2" />
                        </svg>
                        Satuan Barang
                    </a>
                @endif

                @if(auth()->user()->hasPermission('warehouses'))
                    <a href="{{ route('warehouses.index') }}" class="flex items-center px-4 py-2.5 text-xs font-medium rounded-xl transition-all duration-150 group 
                        {{ request()->routeIs('warehouses.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/15' : 'hover:bg-slate-900 hover:text-white' }}">
                        <svg class="h-4 w-4 mr-3 {{ request()->routeIs('warehouses.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Gudang
                    </a>
                @endif

                @if(auth()->user()->hasPermission('locations'))
                    <a href="{{ route('locations.index') }}" class="flex items-center px-4 py-2.5 text-xs font-medium rounded-xl transition-all duration-150 group 
                        {{ request()->routeIs('locations.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/15' : 'hover:bg-slate-900 hover:text-white' }}">
                        <svg class="h-4 w-4 mr-3 {{ request()->routeIs('locations.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Lokasi / Rak Gudang
                    </a>
                @endif

                @if(auth()->user()->hasPermission('barangs'))
                    <a href="{{ route('barangs.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-150 group 
                        {{ request()->routeIs('barangs.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/15' : 'hover:bg-slate-900 hover:text-white' }}">
                        <svg class="h-5 w-5 mr-3 {{ request()->routeIs('barangs.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        Katalog Barang
                    </a>
                @endif

                @if(auth()->user()->hasPermission('suppliers'))
                    <a href="{{ route('suppliers.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-150 group 
                        {{ request()->routeIs('suppliers.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/15' : 'hover:bg-slate-900 hover:text-white' }}">
                        <svg class="h-5 w-5 mr-3 {{ request()->routeIs('suppliers.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Supplier
                    </a>
                @endif
            @endif

            <!-- Pembelian PR & PO Section -->
            @if(auth()->user()->hasPermission('permintaan_pembelian') || auth()->user()->hasPermission('purchase_orders'))
                <hr class="border-slate-800/40 my-2 mx-2" />
                <div class="pt-1 pb-2 px-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Pembelian / PR & PO</div>
                
                @if(auth()->user()->hasPermission('permintaan_pembelian'))
                    <a href="{{ route('permintaan-pembelian.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-150 group 
                        {{ request()->routeIs('permintaan-pembelian.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/15' : 'hover:bg-slate-900 hover:text-white' }}">
                        <svg class="h-5 w-5 mr-3 {{ request()->routeIs('permintaan-pembelian.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Requisition (PR)
                    </a>
                @endif

                @if(auth()->user()->hasPermission('purchase_orders'))
                    <a href="{{ route('purchase-orders.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-150 group 
                        {{ request()->routeIs('purchase-orders.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/15' : 'hover:bg-slate-900 hover:text-white' }}">
                        <svg class="h-5 w-5 mr-3 {{ request()->routeIs('purchase-orders.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        Purchase Order (PO)
                    </a>
                @endif
            @endif

            <!-- Transaksi & Operasi Gudang Section -->
            @if(auth()->user()->hasPermission('permintaan_barang') || auth()->user()->hasPermission('barang_masuk') || auth()->user()->hasPermission('barang_keluar') || auth()->user()->hasPermission('stock_opnames') || auth()->user()->hasPermission('stock_adjustments') || auth()->user()->hasPermission('stock_mutations'))
                <hr class="border-slate-800/40 my-2 mx-2" />
                <div class="pt-1 pb-2 px-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Transaksi & Operasi Gudang</div>

                @if(auth()->user()->hasPermission('permintaan_barang'))
                    <a href="{{ route('permintaan-barang.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-150 group 
                        {{ request()->routeIs('permintaan-barang.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/15' : 'hover:bg-slate-900 hover:text-white' }}">
                        <svg class="h-5 w-5 mr-3 {{ request()->routeIs('permintaan-barang.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Permintaan Barang
                    </a>
                @endif

                @if(auth()->user()->hasPermission('barang_masuk'))
                    <a href="{{ route('barang-masuk.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-150 group 
                        {{ request()->routeIs('barang-masuk.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/15' : 'hover:bg-slate-900 hover:text-white' }}">
                        <svg class="h-5 w-5 mr-3 {{ request()->routeIs('barang-masuk.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 13l-7 7-7-7m14-6l-7 7-7-7" />
                        </svg>
                        Barang Masuk
                    </a>
                @endif

                @if(auth()->user()->hasPermission('barang_keluar'))
                    <a href="{{ route('barang-keluar.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-150 group 
                        {{ request()->routeIs('barang-keluar.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/15' : 'hover:bg-slate-900 hover:text-white' }}">
                        <svg class="h-5 w-5 mr-3 {{ request()->routeIs('barang-keluar.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 11l7-7 7 7M5 19l7-7 7 7" />
                        </svg>
                        Barang Keluar
                    </a>
                @endif

                @if(auth()->user()->hasPermission('stock_opnames'))
                    <a href="{{ route('stock-opnames.index') }}" class="flex items-center px-4 py-2.5 text-xs font-medium rounded-xl transition-all duration-150 group 
                        {{ request()->routeIs('stock-opnames.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/15' : 'hover:bg-slate-900 hover:text-white' }}">
                        <svg class="h-4 w-4 mr-3 {{ request()->routeIs('stock-opnames.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        Stock Opname
                    </a>
                @endif

                @if(auth()->user()->hasPermission('stock_adjustments'))
                    <a href="{{ route('stock-adjustments.index') }}" class="flex items-center px-4 py-2.5 text-xs font-medium rounded-xl transition-all duration-150 group 
                        {{ request()->routeIs('stock-adjustments.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/15' : 'hover:bg-slate-900 hover:text-white' }}">
                        <svg class="h-4 w-4 mr-3 {{ request()->routeIs('stock-adjustments.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                        </svg>
                        Stock Adjustment
                    </a>
                @endif

                @if(auth()->user()->hasPermission('stock_mutations'))
                    <a href="{{ route('stock-mutations.index') }}" class="flex items-center px-4 py-2.5 text-xs font-medium rounded-xl transition-all duration-150 group 
                        {{ request()->routeIs('stock-mutations.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/15' : 'hover:bg-slate-900 hover:text-white' }}">
                        <svg class="h-4 w-4 mr-3 {{ request()->routeIs('stock-mutations.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                        Mutasi Lokasi Rak
                    </a>
                @endif
            @endif

            <!-- Reports & Audit Logs Section -->
            @if(auth()->user()->hasPermission('reports') || auth()->user()->hasPermission('audit_logs'))
                <hr class="border-slate-800/40 my-2 mx-2" />
                <div class="pt-1 pb-2 px-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Analisis & Audit</div>
                
                @if(auth()->user()->hasPermission('reports'))
                    <a href="{{ route('reports.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-150 group 
                        {{ request()->routeIs('reports.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/15' : 'hover:bg-slate-900 hover:text-white' }}">
                        <svg class="h-5 w-5 mr-3 {{ request()->routeIs('reports.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Laporan & Export
                    </a>
                @endif

                @if(auth()->user()->hasPermission('audit_logs'))
                    <a href="{{ route('audit-logs.index') }}" class="flex items-center px-4 py-2.5 text-xs font-medium rounded-xl transition-all duration-150 group 
                        {{ request()->routeIs('audit-logs.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/15' : 'hover:bg-slate-900 hover:text-white' }}">
                        <svg class="h-4 w-4 mr-3 {{ request()->routeIs('audit-logs.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Audit Log Histori
                    </a>
                @endif
            @endif

            <!-- Administrator Settings Section -->
            @if(auth()->user()->hasPermission('users'))
                <hr class="border-slate-800/40 my-2 mx-2" />
                <div class="pt-1 pb-2 px-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Administrator</div>
                <a href="{{ route('users.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-150 group 
                    {{ request()->routeIs('users.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/15' : 'hover:bg-slate-900 hover:text-white' }}">
                    <svg class="h-5 w-5 mr-3 {{ request()->routeIs('users.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Manajemen Akun User
                </a>
            @endif
        </nav>
    </div>

    <!-- Bottom Profile & Logout -->
    <div class="p-4 border-t border-slate-800 bg-slate-950/45 space-y-1 md:hidden">
        <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2.5 text-xs font-semibold text-slate-400 hover:text-white rounded-lg hover:bg-slate-900 transition-colors">
            <svg class="h-4 w-4 mr-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            Edit Profil
        </a>

        <!-- Logout Form -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center px-4 py-2.5 text-xs font-semibold text-rose-400 hover:text-rose-300 rounded-lg hover:bg-rose-500/10 transition-colors text-left">
                <svg class="h-4 w-4 mr-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Keluar (Log Out)
            </button>
        </form>
    </div>
</aside>
