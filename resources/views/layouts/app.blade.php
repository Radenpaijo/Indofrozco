<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-slate-50/50">
        <!-- Global Fixed Top Header Bar -->
        <header class="h-16 bg-slate-950 text-white flex items-center justify-between px-6 border-b border-slate-800 shadow-md no-print fixed top-0 left-0 right-0 z-50">
            <!-- Left: Mobile Toggle & Brand Logo -->
            <div class="flex items-center space-x-4">
                <button id="mobile-menu-toggle" class="md:hidden p-1 text-slate-400 hover:text-white hover:bg-slate-800 rounded-md focus:outline-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2.5">
                    <img src="{{ asset('images/logo.svg') }}" alt="INDOFROZ Logo" class="h-8 w-8 object-contain">
                    <span class="text-lg font-black tracking-widest text-white">INDOFROZ</span>
                    <span class="px-1.5 py-0.5 text-[9px] font-bold bg-indigo-500/20 text-indigo-400 rounded border border-indigo-500/30">WMS</span>
                </a>
            </div>

            <!-- Right: Elegant Solid User Profile Dropdown -->
            <div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
                <!-- Trigger Button (Solid Background) -->
                <button @click="open = !open" type="button" class="group flex items-center space-x-3 p-1.5 pl-2 pr-3.5 rounded-xl bg-slate-900 hover:bg-slate-850 border border-slate-800 hover:border-slate-700 transition-all duration-150 focus:outline-none shadow-sm">
                    <!-- Avatar Circle -->
                    <div class="h-8 w-8 rounded-lg bg-indigo-600 flex items-center justify-center text-white font-bold text-xs shadow uppercase tracking-wider flex-shrink-0">
                        {{ substr(Auth::user()->name, 0, 2) }}
                    </div>

                    <!-- User Name & Role Info -->
                    <div class="hidden sm:flex flex-col text-left">
                        <div class="flex items-center space-x-2">
                            <span class="text-xs font-semibold text-slate-100 group-hover:text-white transition-colors">{{ Auth::user()->name }}</span>
                            <span class="px-1.5 py-0.5 text-[9px] font-bold rounded bg-indigo-950 text-indigo-300 border border-indigo-700 uppercase tracking-wider">
                                {{ Auth::user()->role }}
                            </span>
                        </div>
                    </div>

                    <!-- Chevron Icon -->
                    <svg class="h-4 w-4 text-slate-400 group-hover:text-slate-200 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <!-- Dropdown Popup Card (100% Solid Dark / Non-transparent) -->
                <div x-show="open"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                     x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                     class="absolute right-0 mt-2 w-60 rounded-2xl shadow-2xl p-2 z-50 divide-y divide-slate-800"
                     style="display: none; background-color: #0f172a; border: 1px solid #334155;"
                     @click="open = false">
                    
                    <!-- Header Info Inside Dropdown -->
                    <div class="px-3 py-2.5 bg-slate-950/60 rounded-xl mb-1.5 border border-slate-800">
                        <p class="text-[11px] font-medium text-slate-400">Masuk sebagai</p>
                        <p class="text-xs font-bold text-white truncate mt-0.5">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] text-slate-400 truncate mt-0.5">{{ Auth::user()->email }}</p>
                    </div>

                    <!-- Actions -->
                    <div class="py-1 space-y-1">
                        <a href="{{ route('profile.edit') }}" class="flex items-center px-3 py-2 text-xs font-semibold text-slate-200 hover:text-white rounded-xl hover:bg-slate-800 transition-colors">
                            <svg class="h-4 w-4 mr-2.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Edit Profil
                        </a>
                    </div>

                    <div class="pt-1.5">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center px-3 py-2 text-xs font-semibold text-rose-400 hover:text-rose-300 rounded-xl hover:bg-rose-950/40 transition-colors text-left">
                                <svg class="h-4 w-4 mr-2.5 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Keluar (Logout)
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Layout Wrapper with fixed height to prevent body scroll (offset by pt-16 for the top bar) -->
        <div class="h-screen pt-16 flex flex-col md:flex-row overflow-hidden">
            @include('layouts.navigation')

            <!-- Sidebar Backdrop Overlay for Mobile -->
            <div id="sidebar-overlay" class="fixed inset-0 z-30 bg-slate-950/60 transition-opacity duration-300 opacity-0 pointer-events-none md:hidden"></div>

            <!-- Main Content Area: Locked to height, layout doesn't scroll -->
            <div class="flex-grow flex flex-col md:pl-64 min-w-0 h-full overflow-hidden">
                <!-- Page Content: The only scrollable container inside the main content area -->
                <main class="flex-grow overflow-y-auto p-6 sm:p-8 w-full animate-fade-in">
                    <div class="max-w-7xl mx-auto space-y-6">
                        <!-- Render page title and specific actions inside the page body at the top of scrollable container -->
                        @isset($header)
                            <div class="pb-6 border-b border-slate-200">
                                {{ $header }}
                            </div>
                        @endisset

                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>

        <!-- Javascript for Mobile Drawer Toggle -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const toggleBtn = document.getElementById('mobile-menu-toggle');
                const sidebar = document.getElementById('sidebar-nav');
                const closeBtn = document.getElementById('sidebar-close-btn');
                const overlay = document.getElementById('sidebar-overlay');

                function openSidebar() {
                    sidebar.classList.remove('-translate-x-full');
                    overlay.classList.remove('opacity-0', 'pointer-events-none');
                    overlay.classList.add('opacity-100');
                    document.body.classList.add('overflow-hidden');
                }

                function closeSidebar() {
                    sidebar.classList.add('-translate-x-full');
                    overlay.classList.remove('opacity-100');
                    overlay.classList.add('opacity-0', 'pointer-events-none');
                    document.body.classList.remove('overflow-hidden');
                }

                if (toggleBtn && sidebar && overlay) {
                    toggleBtn.addEventListener('click', openSidebar);
                }
                
                if (closeBtn && sidebar && overlay) {
                    closeBtn.addEventListener('click', closeSidebar);
                }

                if (overlay) {
                    overlay.addEventListener('click', closeSidebar);
                }
            });
        </script>
    </body>
</html>
