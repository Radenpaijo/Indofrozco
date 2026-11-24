<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'INDOFROZ') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo.svg') }}">

    <!-- Font (Outfit) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Kunci text-size-adjust agar Edge & Chrome di Android tidak melakukan font-boosting / auto-zoom saat rotate landscape */
        html, body {
            -webkit-text-size-adjust: 100% !important;
            text-size-adjust: 100% !important;
        }

        /* Hilangkan spinner bawaan browser pada input number agar tidak menimpa teks satuan/icon */
        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none !important;
            margin: 0 !important;
        }
        input[type="number"] {
            -moz-appearance: textfield !important;
            appearance: textfield !important;
        }
    </style>
</head>

<body class="font-sans antialiased bg-slate-100 text-slate-900">
    <!-- Global Fixed Top Header Bar (Cohesive Dark Theme matching Sidebar) -->
    <header style="background-color: #111720 !important;" class="h-16 text-slate-100 flex items-center justify-between px-3 sm:px-6 border-b border-slate-800 shadow-md no-print fixed top-0 left-0 right-0 z-50">
        <!-- Left: Mobile Toggle & Brand Logo -->
        <div class="flex items-center space-x-2 sm:space-x-4 min-w-0">
            <button id="mobile-menu-toggle" type="button"
                class="p-1.5 text-slate-400 hover:text-white hover:bg-slate-800/80 rounded-lg focus:outline-none transition-colors cursor-pointer touch-manipulation select-none shrink-0"
                title="Buka/Tutup Menu">
                <!-- Hamburger Icon: Tampil saat Sidebar Tertutup -->
                <svg id="icon-menu-open" class="h-6 w-6 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <!-- Silang X Icon: Tampil saat Sidebar Terbuka -->
                <svg id="icon-menu-close" class="h-6 w-6 hidden pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 sm:space-x-2.5 shrink-0">
                <img src="{{ asset('images/logo.svg') }}" alt="INDOFROZ Logo" class="h-7 w-7 sm:h-8 sm:w-8 object-contain">
                <span class="text-base sm:text-lg font-black tracking-wider sm:tracking-widest text-white">INDOFROZ</span>
            </a>
        </div>

        <!-- Right: Actions & User Profile -->
        <div class="flex items-center space-x-1.5 sm:space-x-3 shrink-0">
            <!-- Notification Bell Icon Dropdown (Clean Standard UI) -->
            <div class="relative" x-data="{ notifOpen: false }" @click.outside="notifOpen = false" @close.stop="notifOpen = false">
                <!-- Trigger Button -->
                <button @click="notifOpen = !notifOpen" type="button"
                    class="p-2 text-slate-300 hover:text-white hover:bg-slate-800 rounded-xl relative transition focus:outline-none cursor-pointer"
                    title="Notifikasi">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>

                    @if(($notificationCount ?? 0) > 0)
                        <span class="absolute top-1.5 right-1.5 flex h-4 min-w-4 px-1 items-center justify-center rounded-full bg-rose-500 text-[10px] font-bold text-white ring-2 ring-[#111720]">
                            {{ ($notificationCount ?? 0) > 99 ? '99+' : ($notificationCount ?? 0) }}
                        </span>
                    @endif
                </button>

                <!-- Notification Popup Panel (Clean & Simple) -->
                <div x-show="notifOpen" 
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                    class="fixed inset-x-3 top-[4.25rem] sm:absolute sm:inset-x-auto sm:top-auto sm:right-0 sm:mt-2 sm:w-96 rounded-xl shadow-xl z-50 bg-white border border-slate-200 text-slate-800 overflow-hidden"
                    style="display: none;" @click.stop>
                    
                    <!-- Header -->
                    <div class="px-4 py-3 bg-white border-b border-slate-200 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <h4 class="text-sm font-bold text-slate-900">Notifikasi</h4>
                            @if(($notificationCount ?? 0) > 0)
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-600 border border-rose-200">
                                    {{ $notificationCount }} belum selesai
                                </span>
                            @endif
                        </div>
                        <button type="button" @click="notifOpen = false" class="text-xs text-slate-400 hover:text-slate-600 font-medium cursor-pointer">
                            Tutup
                        </button>
                    </div>

                    <!-- List Content -->
                    <div class="max-h-96 overflow-y-auto divide-y divide-slate-100">
                        @forelse(($systemNotifications ?? []) as $notif)
                            <a href="{{ $notif['url'] }}" @click="notifOpen = false" class="p-3.5 hover:bg-slate-50 transition block group">
                                <div class="flex items-start gap-2.5">
                                    <!-- Indicator dot -->
                                    <span class="mt-1.5 w-2 h-2 rounded-full shrink-0 {{ $notif['type'] === 'warning' ? 'bg-amber-500' : ($notif['type'] === 'danger' ? 'bg-rose-500' : 'bg-blue-500') }}"></span>

                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-baseline justify-between gap-2">
                                            <span class="text-xs font-semibold text-slate-900 group-hover:text-blue-600 transition truncate">
                                                {{ $notif['title'] }}
                                            </span>
                                            <span class="text-[11px] text-slate-400 shrink-0">{{ $notif['time'] }}</span>
                                        </div>
                                        <p class="text-xs text-slate-600 mt-0.5 leading-relaxed">
                                            {{ $notif['message'] }}
                                        </p>
                                        <div class="mt-1.5 flex items-center gap-2">
                                            <span class="inline-block text-[10px] font-medium px-2 py-0.5 rounded bg-slate-100 text-slate-600">
                                                {{ $notif['category'] }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="py-8 px-4 text-center">
                                <p class="text-xs text-slate-500">Tidak ada notifikasi baru.</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Footer -->
                    <div class="p-2.5 bg-slate-50 border-t border-slate-200 text-center">
                        <a href="{{ route('dashboard') }}" @click="notifOpen = false" class="text-xs font-semibold text-blue-600 hover:text-blue-800">
                            Lihat Semua di Dashboard &rarr;
                        </a>
                    </div>
                </div>
            </div>

            <!-- User Profile Dropdown -->
            <div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
                <!-- Trigger Button -->
                <button @click="open = !open" type="button"
                    class="group flex items-center sm:space-x-2 p-0.5 sm:py-1.5 sm:px-2.5 rounded-full sm:rounded-xl hover:bg-slate-800/80 sm:border sm:border-slate-700/60 transition-all duration-150 focus:outline-none cursor-pointer"
                    aria-label="Menu Profil">
                    <!-- Avatar Icon (Circular on Mobile) -->
                    <div class="h-8 w-8 sm:hidden rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-slate-300 shadow-sm shrink-0">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>

                    <!-- User Name Info (Desktop) -->
                    <span class="hidden sm:inline text-xs sm:text-sm font-bold text-slate-200 group-hover:text-white transition-colors">{{ Auth::user()->name }}</span>

                    <!-- Chevron Icon (Desktop) -->
                    <svg class="hidden sm:block h-4 w-4 text-slate-400 group-hover:text-slate-200 transition-transform duration-200"
                        :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <!-- Dropdown Popup Card -->
                <div x-show="open" x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                    class="absolute right-0 mt-2 w-60 rounded-2xl shadow-xl p-2 z-50 divide-y divide-slate-100 bg-white border border-slate-200 text-slate-800"
                    style="display: none;" @click="open = false">

                    <!-- Header Info Inside Dropdown -->
                    <div class="px-3 py-2.5 bg-slate-50 rounded-xl mb-1.5 border border-slate-100">
                        <p class="text-[11px] font-medium text-slate-400">Masuk sebagai</p>
                        <p class="text-xs font-bold text-slate-900 truncate mt-0.5">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] text-slate-500 truncate mt-0.5">{{ Auth::user()->email }}</p>
                    </div>

                    <!-- Actions -->
                    <div class="py-1 space-y-1">
                        <a href="{{ route('shortcuts.index') }}"
                            class="flex items-center justify-between px-3 py-2 text-xs font-semibold text-slate-700 hover:text-slate-900 rounded-xl hover:bg-slate-100 transition-colors">
                            <div class="flex items-center">
                                <svg class="h-4 w-4 mr-2.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                                </svg>
                                Pintasan Keyboard
                            </div>
                            <kbd class="px-1.5 py-0.5 text-[10px] font-mono bg-slate-100 text-slate-500 rounded border border-slate-200">?</kbd>
                        </a>
                        <a href="{{ route('profile.edit') }}"
                            class="flex items-center px-3 py-2 text-xs font-semibold text-slate-700 hover:text-slate-900 rounded-xl hover:bg-slate-100 transition-colors">
                            <svg class="h-4 w-4 mr-2.5 text-slate-500" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Edit Profil
                        </a>
                    </div>

                    <!-- Logout -->
                    <div class="pt-1.5">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center px-3 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 rounded-xl transition-colors text-left">
                                <svg class="h-4 w-4 mr-2.5 text-rose-500" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Keluar Aplikasi
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <style>
        /* Base sidebar behavior: Kunci lebar presisi 256px agar identik di semua browser */
        #sidebar-nav {
            width: 256px !important;
            max-width: 256px !important;
            transform: translateX(-100%);
            transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Default open on standard desktop (width >= 1024px and height > 500px) */
        @media (min-width: 1024px) and (min-height: 501px) {
            body:not(.sidebar-collapsed) #sidebar-nav {
                transform: translateX(0);
            }
            body:not(.sidebar-collapsed) .main-content-layout {
                padding-left: 16rem !important;
            }
        }

        /* When manually opened on any screen (including rotated mobile) */
        body.sidebar-open #sidebar-nav {
            transform: translateX(0) !important;
        }

        /* When collapsed on desktop */
        body.sidebar-collapsed #sidebar-nav {
            transform: translateX(-100%) !important;
        }
        body.sidebar-collapsed .main-content-layout {
            padding-left: 0 !important;
        }

        /* Mobile portrait and rotated landscape phones (max-width: 1023px OR max-height: 500px) */
        @media (max-width: 1023px), (max-height: 500px) {
            #sidebar-nav {
                z-index: 50 !important;
            }
            body.sidebar-open #sidebar-overlay {
                opacity: 1 !important;
                pointer-events: auto !important;
            }
            .main-content-layout {
                padding-left: 0 !important;
            }
        }

        .main-content-layout {
            transition: padding-left 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Toggle Icon Switching: X saat Terbuka, Garis 3 saat Tertutup */
        /* Desktop: default Terbuka -> tampilkan X (#icon-menu-close), sembunyikan Garis 3 (#icon-menu-open) */
        @media (min-width: 1024px) and (min-height: 501px) {
            body:not(.sidebar-collapsed) #icon-menu-open { display: none !important; }
            body:not(.sidebar-collapsed) #icon-menu-close { display: block !important; }
            body.sidebar-collapsed #icon-menu-open { display: block !important; }
            body.sidebar-collapsed #icon-menu-close { display: none !important; }
        }

        /* Mobile / Small Screen: default Tertutup -> tampilkan Garis 3, sembunyikan X */
        @media (max-width: 1023px), (max-height: 500px) {
            body:not(.sidebar-open) #icon-menu-open { display: block !important; }
            body:not(.sidebar-open) #icon-menu-close { display: none !important; }
            body.sidebar-open #icon-menu-open { display: none !important; }
            body.sidebar-open #icon-menu-close { display: block !important; }
        }
    </style>

    <!-- Main Layout Wrapper with fixed height to prevent body scroll (offset by pt-16 for the top bar) -->
    <div class="h-screen pt-16 flex flex-col md:flex-row overflow-hidden">
        @include('layouts.navigation')

        <!-- Sidebar Backdrop Overlay for Mobile & Landscape Rotate -->
        <div id="sidebar-overlay"
            class="fixed inset-0 z-40 bg-slate-950/60 transition-opacity duration-300 opacity-0 pointer-events-none cursor-pointer">
        </div>

        <!-- Main Content Area: Responsive padding controlled smoothly by CSS -->
        <div class="flex-grow flex flex-col main-content-layout min-w-0 h-full overflow-hidden">
            <!-- Page Content: The only scrollable container inside the main content area -->
            <main class="flex-grow overflow-y-auto p-3.5 pb-8 sm:p-6 sm:pb-8 lg:p-8 lg:pb-8 w-full animate-fade-in">
                <div class="w-full space-y-4 sm:space-y-6">
                    <!-- Render page title and specific actions inside the page body at the top of scrollable container -->
                    @isset($header)
                        <div class="pb-4 sm:pb-6 border-b border-slate-200 text-left">
                            {{ $header }}
                        </div>
                    @endisset

                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    <!-- Javascript for Responsive & Collapsible Sidebar (Portrait, Landscape Rotate & Desktop) -->
    <script>
        let isSidebarToggling = false;

        function isDesktop() {
            return window.innerWidth >= 1024 && window.innerHeight > 500;
        }

        function toggleSidebar(e) {
            if (e && e.preventDefault) e.preventDefault();
            if (isSidebarToggling) return;
            isSidebarToggling = true;
            setTimeout(() => { isSidebarToggling = false; }, 250);

            const body = document.body;
            if (isDesktop()) {
                body.classList.toggle('sidebar-collapsed');
            } else {
                if (body.classList.contains('sidebar-open')) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            }
        }

        function openSidebar(e) {
            if (e && e.preventDefault) e.preventDefault();
            const body = document.body;
            if (isDesktop()) {
                body.classList.remove('sidebar-collapsed');
            } else {
                body.classList.add('sidebar-open');
            }
        }

        function closeSidebar(e) {
            if (e && e.preventDefault) e.preventDefault();
            const body = document.body;
            if (isDesktop()) {
                body.classList.add('sidebar-collapsed');
            } else {
                body.classList.remove('sidebar-open');
            }
        }

        window.isDesktop = isDesktop;
        window.toggleSidebar = toggleSidebar;
        window.openSidebar = openSidebar;
        window.closeSidebar = closeSidebar;

        function initSidebarControls() {
            const toggleBtn = document.getElementById('mobile-menu-toggle');
            const closeBtn = document.getElementById('sidebar-close-btn');
            const overlay = document.getElementById('sidebar-overlay');

            if (toggleBtn) {
                toggleBtn.onclick = toggleSidebar;
            }

            if (closeBtn) {
                closeBtn.onclick = closeSidebar;
            }

            if (overlay) {
                overlay.onclick = closeSidebar;
            }

            window.addEventListener('resize', function () {
                if (isDesktop()) {
                    document.body.classList.remove('sidebar-open');
                }
            });

            window.addEventListener('orientationchange', function () {
                // Saat user memutar HP (rotate), otomatis tutup menu agar tampilan langsung lebar
                document.body.classList.remove('sidebar-open');
            });

            // Global Keyboard Shortcuts (? or F1 to open Shortcuts Page)
            window.addEventListener('keydown', function(e) {
                if ((e.key === '?' || (e.shiftKey && e.key === '?') || e.key === 'F1') && 
                    !['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement?.tagName)) {
                    e.preventDefault();
                    window.location.href = "{{ route('shortcuts.index') }}";
                }

                // Global Escape -> Trigger Tombol Kembali jika ada di halaman
                if (e.key === 'Escape' && !e.defaultPrevented) {
                    if (['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement?.tagName)) {
                        document.activeElement.blur();
                        return;
                    }
                    const openModal = document.querySelector('[role="dialog"]:not(.hidden):not([style*="display: none"])');
                    if (openModal) return;

                    const backBtn = document.querySelector('a[data-back-button="true"]');
                    if (backBtn) {
                        e.preventDefault();
                        backBtn.click();
                    }
                }
            });

            // Global Input Form Keyboard Navigation (berlaku untuk semua form input data)
            document.addEventListener('keydown', function(e) {
                if (e.defaultPrevented) return;

                const target = e.target;
                if (!target || !target.closest) return;

                const form = target.closest('main form');
                if (!form) return;

                const method = (form.getAttribute('method') || 'GET').toUpperCase();
                if (method === 'GET' || (form.getAttribute('action') || '').includes('logout')) {
                    return;
                }

                const getNavigableInputs = () => Array.from(form.querySelectorAll(
                    'input:not([type="hidden"]):not([type="button"]):not([type="submit"]):not([type="reset"]):not([disabled]):not([readonly]):not([tabindex="-1"]), select:not([disabled]):not([tabindex="-1"]), textarea:not([disabled]):not([readonly]):not([tabindex="-1"])'
                )).filter(el => el.offsetParent !== null);

                // Ctrl + Enter / Cmd + Enter -> Submit form langsung dari field mana saja
                if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                    e.preventDefault();
                    const submitBtn = form.querySelector('button[type="submit"]:not([disabled]), input[type="submit"]:not([disabled])');
                    if (submitBtn) {
                        submitBtn.click();
                    } else {
                        form.requestSubmit();
                    }
                    return;
                }

                // Enter -> Pindah ke kolom input berikutnya (kecuali textarea & button/link)
                if (e.key === 'Enter' && !e.shiftKey && !e.ctrlKey && !e.altKey && !e.metaKey) {
                    if (target.tagName === 'BUTTON' || target.tagName === 'A' || target.tagName === 'TEXTAREA' || target.type === 'submit') {
                        return;
                    }

                    if (target.matches('input, select')) {
                        e.preventDefault();
                        const inputs = getNavigableInputs();
                        const idx = inputs.indexOf(target);
                        if (idx >= 0 && idx < inputs.length - 1) {
                            const nextEl = inputs[idx + 1];
                            nextEl.focus();
                            if (nextEl.select && typeof nextEl.select === 'function' && nextEl.type !== 'date') {
                                nextEl.select();
                            }
                        } else if (idx === inputs.length - 1) {
                            const submitBtn = form.querySelector('button[type="submit"]:not([disabled]), input[type="submit"]:not([disabled])');
                            if (submitBtn) submitBtn.focus();
                        }
                    }
                    return;
                }

                // Shift + Enter -> Kembali ke kolom input sebelumnya
                if (e.key === 'Enter' && e.shiftKey && !e.ctrlKey && !e.altKey && !e.metaKey) {
                    if (target.tagName === 'TEXTAREA') return;

                    if (target.matches('input, select')) {
                        e.preventDefault();
                        const inputs = getNavigableInputs();
                        const idx = inputs.indexOf(target);
                        if (idx > 0) {
                            const prevEl = inputs[idx - 1];
                            prevEl.focus();
                            if (prevEl.select && typeof prevEl.select === 'function' && prevEl.type !== 'date') {
                                prevEl.select();
                            }
                        }
                    }
                }
            });

            // Auto-focus kolom input pertama yang bisa diisi saat membuka halaman form (tanpa perlu klik mouse/trackpad)
            const isTouchOnly = window.matchMedia && window.matchMedia('(hover: none) and (pointer: coarse)').matches;
            if (!isTouchOnly) {
                requestAnimationFrame(function() {
                    const active = document.activeElement;
                    if (active && ['INPUT', 'SELECT', 'TEXTAREA'].includes(active.tagName) && !active.readOnly && !active.disabled) {
                        return;
                    }

                    const forms = Array.from(document.querySelectorAll('main form'));
                    for (const form of forms) {
                        const method = (form.getAttribute('method') || 'GET').toUpperCase();
                        if (method === 'GET' || (form.getAttribute('action') || '').includes('logout')) {
                            continue;
                        }

                        const firstEditable = Array.from(form.querySelectorAll(
                            'input:not([type="hidden"]):not([type="button"]):not([type="submit"]):not([type="reset"]):not([type="checkbox"]):not([type="radio"]):not([disabled]):not([readonly]):not([tabindex="-1"]), select:not([disabled]):not([tabindex="-1"]), textarea:not([disabled]):not([readonly]):not([tabindex="-1"])'
                        )).find(el => el.offsetParent !== null);

                        if (firstEditable) {
                            firstEditable.focus({ preventScroll: true });
                            if (firstEditable.select && typeof firstEditable.select === 'function' && firstEditable.type !== 'date' && !firstEditable.value) {
                                firstEditable.select();
                            }
                            break;
                        }
                    }
                });
            }
            // Tampilkan pintasan keyboard otomatis pada atribut title tombol saat kursor menyentuh (hover tooltip)
            initShortcutTooltips();
        }

        function initShortcutTooltips() {
            // 1. Tombol Simpan Form -> (Ctrl + Enter)
            document.querySelectorAll('form button[type="submit"], form input[type="submit"]').forEach(btn => {
                const text = btn.innerText?.trim() || btn.value?.trim() || 'Simpan';
                if (!btn.title) {
                    btn.title = `${text} (Ctrl + Enter)`;
                } else if (!btn.title.includes('Ctrl')) {
                    btn.title = `${btn.title} (Ctrl + Enter)`;
                }
            });

            // 2. Tombol Tambah Baris Transaksi -> (Alt + A)
            document.querySelectorAll('button[id*="add-row"], button[id*="tambah-baris"], button[onclick*="addRow"], button[onclick*="tambahBaris"]').forEach(btn => {
                const text = btn.innerText?.trim() || 'Tambah Baris';
                if (!btn.title) {
                    btn.title = `${text} (Alt + A)`;
                } else if (!btn.title.includes('Alt + A')) {
                    btn.title = `${btn.title} (Alt + A)`;
                }
            });

            // 3. Tombol Cetak Dokumen -> (Ctrl + P)
            document.querySelectorAll('button[onclick*="print"], a[href*="print"]').forEach(btn => {
                const text = btn.innerText?.trim() || 'Cetak';
                if (!btn.title) {
                    btn.title = `${text} (Ctrl + P)`;
                } else if (!btn.title.includes('Ctrl + P')) {
                    btn.title = `${btn.title} (Ctrl + P)`;
                }
            });

            // 4. Input Pencarian Tabel -> (Ctrl + F)
            document.querySelectorAll('input[type="search"], input[name="search"], input[id*="search"], input[placeholder*="Cari"], input[placeholder*="cari"]').forEach(input => {
                const ph = input.placeholder || 'Cari Data';
                if (!input.title) {
                    input.title = `${ph} (Ctrl + F)`;
                } else if (!input.title.includes('Ctrl + F')) {
                    input.title = `${input.title} (Ctrl + F)`;
                }
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initSidebarControls);
        } else {
            initSidebarControls();
        }
    </script>

    <!-- Global Floating Toast Notification Container (Bottom-Right) -->
    <x-toast />
</body>

</html>