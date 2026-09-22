@if (session('success'))
    <div class="mb-4 flex items-center justify-between p-4 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 rounded-xl shadow-sm animate-fade-in" role="alert">
        <div class="flex items-center space-x-3">
            <div class="p-2 bg-emerald-500/20 rounded-lg flex-shrink-0">
                <svg class="h-5 w-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-emerald-300">Berhasil</h4>
                <p class="text-sm font-medium text-emerald-200 mt-0.5">{{ session('success') }}</p>
            </div>
        </div>
    </div>
@endif

@if (session('error'))
    <div class="mb-4 flex items-center justify-between p-4 bg-rose-500/10 border border-rose-500/30 text-rose-400 rounded-xl shadow-sm animate-fade-in" role="alert">
        <div class="flex items-center space-x-3">
            <div class="p-2 bg-rose-500/20 rounded-lg flex-shrink-0">
                <svg class="h-5 w-5 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-rose-300">Gagal / Error</h4>
                <p class="text-sm font-medium text-rose-200 mt-0.5">{{ session('error') }}</p>
            </div>
        </div>
    </div>
@endif

@if (session('warning'))
    <div class="mb-4 flex items-center justify-between p-4 bg-amber-500/10 border border-amber-500/30 text-amber-400 rounded-xl shadow-sm animate-fade-in" role="alert">
        <div class="flex items-center space-x-3">
            <div class="p-2 bg-amber-500/20 rounded-lg flex-shrink-0">
                <svg class="h-5 w-5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-amber-300">Peringatan</h4>
                <p class="text-sm font-medium text-amber-200 mt-0.5">{{ session('warning') }}</p>
            </div>
        </div>
    </div>
@endif

@if (session('info'))
    <div class="mb-4 flex items-center justify-between p-4 bg-blue-500/10 border border-blue-500/30 text-blue-400 rounded-xl shadow-sm animate-fade-in" role="alert">
        <div class="flex items-center space-x-3">
            <div class="p-2 bg-blue-500/20 rounded-lg flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-blue-300">Informasi</h4>
                <p class="text-sm font-medium text-blue-200 mt-0.5">{{ session('info') }}</p>
            </div>
        </div>
    </div>
@endif

@if ($errors->any())
    <div class="mb-4 p-4 bg-rose-500/10 border border-rose-500/30 text-rose-400 rounded-xl shadow-sm animate-fade-in" role="alert">
        <div class="flex items-start space-x-3">
            <div class="p-2 bg-rose-500/20 rounded-lg flex-shrink-0 mt-0.5">
                <svg class="h-5 w-5 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-rose-300">Terdapat Kesalahan Validasi:</h4>
                <ul class="mt-1.5 list-disc list-inside text-sm text-rose-200 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif
