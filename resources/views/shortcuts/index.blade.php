<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                <x-back-button :href="route('dashboard')" />
                <div class="min-w-0">
                    <h2 class="font-extrabold text-xl sm:text-2xl text-slate-900 leading-tight">
                        {{ __('Pintasan Keyboard') }}
                    </h2>
                </div>
            </div>

            <button type="button" onclick="window.print()" class="no-print inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 hover:text-slate-900 transition shadow-xs cursor-pointer">
                <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span>Cetak (A4)</span>
            </button>
        </div>
    </x-slot>

    <!-- Kontainer Penuh Sampai Samping (Tema Web INDOFROZ, Alami Tanpa Tombol Melayang) -->
    <div class="w-full space-y-4 pb-8">

        <!-- Card Pintasan Keyboard Penuh -->
        <div class="w-full bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden font-sans">
            
            <!-- Header Card -->
            <div class="px-4 sm:px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between select-none">
                <div class="flex items-center gap-2.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
                    <h3 class="font-bold text-sm sm:text-base text-slate-900 tracking-tight">
                        Pintasan Keyboard &amp; Aksi Cepat
                    </h3>
                </div>
                <span class="text-xs font-medium text-slate-500 hidden sm:inline">
                    Tekan <kbd class="px-1.5 py-0.5 text-[11px] font-mono font-bold bg-white text-slate-700 border border-slate-300 rounded shadow-2xs">Esc</kbd> untuk kembali
                </span>
            </div>

            <!-- Body: Grid 2 Kolom di Desktop, 1 Kolom Bersih di HP -->
            <div class="p-4 sm:p-6 md:p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-12">
                    
                    <!-- KOLOM 1: FORMULIR & INPUT DATA -->
                    <div class="space-y-1">
                        <div class="flex items-center gap-2 pb-2 mb-2 border-b border-slate-200">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Formulir &amp; Input Data</span>
                        </div>

                        <!-- 1. Simpan Formulir -->
                        <div class="flex items-center justify-between py-2 px-1.5 rounded-lg hover:bg-slate-50 transition-colors border-b border-slate-100">
                            <span class="text-xs sm:text-sm font-semibold text-slate-800 pr-3 truncate">Simpan formulir langsung</span>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <kbd class="kb-key">Ctrl</kbd>
                                <kbd class="kb-key">Enter</kbd>
                            </div>
                        </div>

                        <!-- 2. Pindah Kolom / Tambah Baris -->
                        <div class="flex items-center justify-between py-2 px-1.5 rounded-lg hover:bg-slate-50 transition-colors border-b border-slate-100">
                            <span class="text-xs sm:text-sm font-semibold text-slate-800 pr-3 truncate">Pindah kolom / tambah baris</span>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <kbd class="kb-key">Enter</kbd>
                            </div>
                        </div>

                        <!-- 3. Kembali ke Kolom Sebelumnya -->
                        <div class="flex items-center justify-between py-2 px-1.5 rounded-lg hover:bg-slate-50 transition-colors border-b border-slate-100">
                            <span class="text-xs sm:text-sm font-semibold text-slate-800 pr-3 truncate">Kembali ke kolom sebelumnya</span>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <kbd class="kb-key">Shift</kbd>
                                <kbd class="kb-key">Enter</kbd>
                            </div>
                        </div>

                        <!-- 4. Pindah Fokus Elemen Form -->
                        <div class="flex items-center justify-between py-2 px-1.5 rounded-lg hover:bg-slate-50 transition-colors border-b border-slate-100">
                            <span class="text-xs sm:text-sm font-semibold text-slate-800 pr-3 truncate">Pindah fokus elemen form</span>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <kbd class="kb-key">Tab</kbd>
                            </div>
                        </div>

                        <!-- 5. Fokus Elemen Sebelumnya -->
                        <div class="flex items-center justify-between py-2 px-1.5 rounded-lg hover:bg-slate-50 transition-colors border-b border-slate-100">
                            <span class="text-xs sm:text-sm font-semibold text-slate-800 pr-3 truncate">Fokus elemen sebelumnya</span>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <kbd class="kb-key">Shift</kbd>
                                <kbd class="kb-key">Tab</kbd>
                            </div>
                        </div>

                        <!-- 6. Tutup Modal / Batalkan -->
                        <div class="flex items-center justify-between py-2 px-1.5 rounded-lg hover:bg-slate-50 transition-colors border-b border-slate-100">
                            <span class="text-xs sm:text-sm font-semibold text-slate-800 pr-3 truncate">Tutup modal / batalkan aksi</span>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <kbd class="kb-key">Escape</kbd>
                            </div>
                        </div>

                        <!-- 7. Pilih Opsi Dropdown -->
                        <div class="flex items-center justify-between py-2 px-1.5 rounded-lg hover:bg-slate-50 transition-colors border-b border-slate-100">
                            <span class="text-xs sm:text-sm font-semibold text-slate-800 pr-3 truncate">Pilih opsi dropdown</span>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <kbd class="kb-key">&uarr;</kbd>
                                <kbd class="kb-key">&darr;</kbd>
                            </div>
                        </div>

                        <!-- 8. Lompat Nama Barang -->
                        <div class="flex items-center justify-between py-2 px-1.5 rounded-lg hover:bg-slate-50 transition-colors">
                            <span class="text-xs sm:text-sm font-semibold text-slate-800 pr-3 truncate">Lompat nama barang / rekanan</span>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <kbd class="kb-key">Ketik Awalan</kbd>
                            </div>
                        </div>
                    </div>

                    <!-- KOLOM 2: TABEL TRANSAKSI & NAVIGASI -->
                    <div class="space-y-6">
                        
                        <!-- Section: Tabel Transaksi & Baris -->
                        <div class="space-y-1">
                            <div class="flex items-center gap-2 pb-2 mb-2 border-b border-slate-200">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Tabel Transaksi &amp; Baris</span>
                            </div>

                            <!-- Tambah Baris Alt + A -->
                            <div class="flex items-center justify-between py-2 px-1.5 rounded-lg hover:bg-slate-50 transition-colors border-b border-slate-100">
                                <span class="text-xs sm:text-sm font-semibold text-slate-800 pr-3 truncate">Tambah baris item baru</span>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <kbd class="kb-key">Alt</kbd>
                                    <kbd class="kb-key">A</kbd>
                                </div>
                            </div>

                            <!-- Tambah Baris Insert -->
                            <div class="flex items-center justify-between py-2 px-1.5 rounded-lg hover:bg-slate-50 transition-colors border-b border-slate-100">
                                <span class="text-xs sm:text-sm font-semibold text-slate-800 pr-3 truncate">Tambah baris (alternatif)</span>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <kbd class="kb-key">Insert</kbd>
                                </div>
                            </div>

                            <!-- Hapus Baris Shift + Delete -->
                            <div class="flex items-center justify-between py-2 px-1.5 rounded-lg hover:bg-slate-50 transition-colors border-b border-slate-100">
                                <span class="text-xs sm:text-sm font-semibold text-slate-800 pr-3 truncate">Hapus baris item aktif</span>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <kbd class="kb-key">Shift</kbd>
                                    <kbd class="kb-key">Delete</kbd>
                                </div>
                            </div>

                            <!-- Hapus Baris Alt + Delete -->
                            <div class="flex items-center justify-between py-2 px-1.5 rounded-lg hover:bg-slate-50 transition-colors border-b border-slate-100">
                                <span class="text-xs sm:text-sm font-semibold text-slate-800 pr-3 truncate">Hapus baris (alternatif)</span>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <kbd class="kb-key">Alt</kbd>
                                    <kbd class="kb-key">Delete</kbd>
                                </div>
                            </div>

                            <!-- Barcode Scanner -->
                            <div class="flex items-center justify-between py-2 px-1.5 rounded-lg hover:bg-slate-50 transition-colors">
                                <span class="text-xs sm:text-sm font-semibold text-slate-800 pr-3 truncate">Pindai barcode fisik</span>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <kbd class="kb-key">Scan USB</kbd>
                                </div>
                            </div>
                        </div>

                        <!-- Section: Navigasi & Sistem -->
                        <div class="space-y-1">
                            <div class="flex items-center gap-2 pb-2 mb-2 border-b border-slate-200">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Navigasi &amp; Sistem</span>
                            </div>

                            <!-- Pintasan Keyboard ? -->
                            <div class="flex items-center justify-between py-2 px-1.5 rounded-lg hover:bg-slate-50 transition-colors border-b border-slate-100">
                                <span class="text-xs sm:text-sm font-semibold text-slate-800 pr-3 truncate">Buka panduan pintasan</span>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <kbd class="kb-key">?</kbd>
                                    <span class="text-slate-400 text-xs font-bold">/</span>
                                    <kbd class="kb-key">F1</kbd>
                                </div>
                            </div>

                            <!-- Cari Ctrl + F -->
                            <div class="flex items-center justify-between py-2 px-1.5 rounded-lg hover:bg-slate-50 transition-colors border-b border-slate-100">
                                <span class="text-xs sm:text-sm font-semibold text-slate-800 pr-3 truncate">Fokus pencarian / filter</span>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <kbd class="kb-key">Ctrl</kbd>
                                    <kbd class="kb-key">F</kbd>
                                </div>
                            </div>

                            <!-- Cetak Ctrl + P -->
                            <div class="flex items-center justify-between py-2 px-1.5 rounded-lg hover:bg-slate-50 transition-colors">
                                <span class="text-xs sm:text-sm font-semibold text-slate-800 pr-3 truncate">Cetak dokumen / PO (A4 &amp; PDF)</span>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <kbd class="kb-key">Ctrl</kbd>
                                    <kbd class="kb-key">P</kbd>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Footer: Posisi Alami di Bawah Card (TIDAK MELAYANG / TIDAK MENUTUPI KONTEN) -->
            <div class="px-4 sm:px-6 py-4 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3">
                <span class="text-xs text-slate-500 text-center sm:text-left">
                    Tekan <kbd class="px-1.5 py-0.5 text-[11px] font-mono font-bold bg-white text-slate-700 border border-slate-300 rounded shadow-2xs">Esc</kbd> kapan saja untuk kembali
                </span>
                <button type="button" 
                        onclick="handleOkeClick()" 
                        class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:scale-[0.98] text-white font-bold text-xs sm:text-sm rounded-xl shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>OKE (Selesai)</span>
                </button>
            </div>

        </div>
    </div>

    <!-- STYLESHEET KEYCAPS TEMA WEB -->
    <style>
        .kb-key {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-width: 24px !important;
            height: 24px !important;
            padding: 0 7px !important;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace !important;
            font-size: 11px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            background-color: #f1f5f9 !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 6px !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05) !important;
            user-select: none !important;
            white-space: nowrap !important;
        }

        @media print {
            .no-print, header, aside, #sidebar-nav, #sidebar-overlay {
                display: none !important;
            }
            body {
                background: white !important;
                color: black !important;
            }
        }
    </style>

    <script>
        function handleOkeClick() {
            if (window.history.length > 1) {
                window.history.back();
            } else {
                window.location.href = "{{ route('dashboard') }}";
            }
        }

        // Tekan Escape untuk langsung kembali
        window.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                handleOkeClick();
            }
        });
    </script>
</x-app-layout>
