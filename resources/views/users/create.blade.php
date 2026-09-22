<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4 min-w-0">
            <x-back-button />
            <div class="min-w-0">
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                    {{ __('Tambah User') }}
                </h2>
                <p class="text-xs text-slate-500 mt-1">Buat pengguna baru dan tentukan hak akses fiturnya.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('users.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Account Information Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-200/80 p-6 space-y-5">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-800 border-b border-slate-100 pb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Informasi Utama Akun
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Nama Lengkap Pengguna <span class="text-rose-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Contoh: Ahmad Subagyo">
                            @error('name') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Alamat Email <span class="text-rose-500">*</span></label>
                            <input type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Contoh: ahmad@indofroz.com">
                            @error('email') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Peranan Utama / Role <span class="text-rose-500">*</span></label>
                        <select name="role" required class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">-- Pilih Peranan Utama --</option>
                            @foreach($roles as $key => $label)
                                <option value="{{ $key }}" {{ old('role') === $key ? 'selected' : '' }}>
                                    {{ $label }} ({{ strtoupper($key) }})
                                </option>
                            @endforeach
                        </select>
                        @error('role') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Password <span class="text-rose-500">*</span></label>
                            <input type="password" name="password" required class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Minimal 8 karakter">
                            @error('password') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Konfirmasi Password <span class="text-rose-500">*</span></label>
                            <input type="password" name="password_confirmation" required class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Ulangi password">
                        </div>
                    </div>
                </div>

                <!-- Feature Permissions Matrix Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-200/80 p-6 space-y-5">
                    <div class="flex flex-col md:flex-row md:items-center justify-between border-b border-slate-100 pb-4 gap-3">
                        <div>
                            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-800 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                Hak Akses Fitur & Menu Sistem (Centang Fitur yang Diizinkan)
                            </h3>
                            <p class="text-xs text-slate-500 mt-1">Centang menu apa saja yang diperbolehkan untuk diakses oleh akun ini.</p>
                        </div>

                        <!-- Quick Toggle Action Buttons -->
                        <div class="flex items-center space-x-2">
                            <button type="button" id="btn-select-all" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold rounded-lg transition">
                                ✓ Pilih Semua
                            </button>
                            <button type="button" id="btn-deselect-all" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-lg transition">
                                ✕ Hapus Semua
                            </button>
                        </div>
                    </div>

                    <!-- Permission Groups Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($moduleGroups as $groupTitle => $permissions)
                            <div class="bg-slate-50/70 p-4 rounded-xl border border-slate-200/60 space-y-3">
                                <div class="flex items-center justify-between border-b border-slate-200/60 pb-2">
                                    <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-700">{{ $groupTitle }}</h4>
                                    <button type="button" onclick="toggleGroup('{{ Str::slug($groupTitle) }}')" class="text-[11px] font-semibold text-indigo-600 hover:underline">
                                        Toggle Group
                                    </button>
                                </div>
                                <div class="space-y-2 group-checkboxes-{{ Str::slug($groupTitle) }}">
                                    @foreach($permissions as $key => $label)
                                        <label class="flex items-center p-2 rounded-lg bg-white border border-slate-200 hover:border-indigo-300 transition cursor-pointer group">
                                            <input type="checkbox" name="permissions[]" value="{{ $key }}" class="perm-checkbox rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 h-4 w-4"
                                                {{ is_array(old('permissions')) && in_array($key, old('permissions')) ? 'checked' : '' }}>
                                            <div class="ml-3">
                                                <span class="text-xs font-bold text-slate-800 group-hover:text-indigo-600 transition">{{ $label }}</span>
                                                <span class="text-[10px] text-slate-400 block font-mono">key: {{ $key }}</span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Submit Bar -->
                <div class="flex items-center justify-end space-x-3 bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
                    <a href="{{ route('users.index') }}" class="px-5 py-2.5 text-sm font-semibold text-slate-600 hover:text-slate-900">Batal</a>
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition shadow-md shadow-indigo-600/20">
                        Simpan Akun & Hak Akses
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Toggle All JS Script -->
    <script>
        document.getElementById('btn-select-all').addEventListener('click', function() {
            document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = true);
        });

        document.getElementById('btn-deselect-all').addEventListener('click', function() {
            document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = false);
        });

        function toggleGroup(groupSlug) {
            const container = document.querySelector('.group-checkboxes-' + groupSlug);
            if (!container) return;
            const checkboxes = container.querySelectorAll('.perm-checkbox');
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            checkboxes.forEach(cb => cb.checked = !allChecked);
        }
    </script>
</x-app-layout>
