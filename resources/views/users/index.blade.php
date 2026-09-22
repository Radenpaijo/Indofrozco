<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-4 min-w-0">
                <x-back-button />
                <div class="min-w-0">
                    <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                        {{ __('Manajemen Akun User & Hak Akses Fitur') }}
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">
                        Kelola pengguna sistem, buat akun baru, dan tentukan secara spesifik fitur mana saja yang dapat diakses oleh akun tersebut.
                    </p>
                </div>
            </div>
            <a href="{{ route('users.create') }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-md transition-all shrink-0">
                <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Akun Baru
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="p-4 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-xl text-sm font-medium flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-rose-50 text-rose-700 border border-rose-200 rounded-xl text-sm font-medium flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- Filter Card -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
                <form method="GET" action="{{ route('users.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Cari Akun</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..." class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Filter Role</label>
                        <select name="role" class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">Semua Role</option>
                            @foreach($roles as $key => $label)
                                <option value="{{ $key }}" {{ request('role') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end space-x-2">
                        <button type="submit" class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-sm font-semibold rounded-xl transition shadow">
                            Filter
                        </button>
                        @if(request()->hasAny(['search', 'role']))
                            <a href="{{ route('users.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-xl transition">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Users Table Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-200/80">
                <div class="p-6 text-slate-900">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-200 bg-slate-50/50 text-xs font-bold uppercase text-slate-500 tracking-wider">
                                    <th class="p-4">Pengguna</th>
                                    <th class="p-4">Role / Peranan</th>
                                    <th class="p-4">Akses Fitur & Menu</th>
                                    <th class="p-4">Tanggal Buat</th>
                                    <th class="p-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm">
                                @forelse($users as $user)
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="p-4">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-sm">
                                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                                </div>
                                                <div>
                                                    <div class="font-bold text-slate-800 flex items-center space-x-2">
                                                        <span>{{ $user->name }}</span>
                                                        @if(auth()->id() === $user->id)
                                                            <span class="px-2 py-0.5 text-[10px] font-bold bg-indigo-50 text-indigo-600 rounded border border-indigo-200">Anda</span>
                                                        @endif
                                                    </div>
                                                    <div class="text-xs text-slate-500 font-mono">{{ $user->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="p-4">
                                            @php
                                                $badgeClasses = match($user->role) {
                                                    'admin' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                                                    'owner' => 'bg-purple-100 text-purple-800 border-purple-200',
                                                    'gudang' => 'bg-amber-100 text-amber-800 border-amber-200',
                                                    'purchasing' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                                    'produksi' => 'bg-sky-100 text-sky-800 border-sky-200',
                                                    default => 'bg-slate-100 text-slate-800 border-slate-200',
                                                };
                                            @endphp
                                            <span class="px-3 py-1 text-xs font-bold rounded-full border {{ $badgeClasses }}">
                                                {{ $roles[$user->role] ?? strtoupper($user->role) }}
                                            </span>
                                        </td>
                                        <td class="p-4">
                                            @php
                                                $totalFeatures = count(\App\Models\User::getAllPermissionKeys());
                                                if (is_array($user->permissions)) {
                                                    $count = count($user->permissions);
                                                    $accessText = "{$count} dari {$totalFeatures} Fitur";
                                                    $badgeColor = $count > 0 ? 'bg-indigo-50 text-indigo-700 border-indigo-200' : 'bg-rose-50 text-rose-600 border-rose-200';
                                                } else {
                                                    $accessText = 'Akses Default Role';
                                                    $badgeColor = 'bg-slate-100 text-slate-700 border-slate-200';
                                                }
                                            @endphp
                                            <span class="px-2.5 py-1 text-xs font-semibold rounded-lg border {{ $badgeColor }}">
                                                {{ $accessText }}
                                            </span>
                                        </td>
                                        <td class="p-4 text-xs text-slate-500 font-mono">
                                            {{ $user->created_at ? $user->created_at->format('d M Y H:i') : '-' }}
                                        </td>
                                        <td class="p-4 text-right space-x-2">
                                            <a href="{{ route('users.edit', $user) }}" class="inline-flex items-center px-3 py-1.5 bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 text-slate-700 font-semibold text-xs rounded-lg transition">
                                                Edit Hak Akses
                                            </a>
                                            @if(auth()->id() !== $user->id)
                                                <x-modal-hapus name="{{ $user->name }}" action="{{ route('users.destroy', $user) }}" />
                                            @else
                                                <button type="button" disabled title="Tidak dapat menghapus akun sendiri" class="inline-flex items-center px-3 py-1.5 bg-slate-100 text-slate-400 font-semibold text-xs rounded-lg cursor-not-allowed">
                                                    Hapus
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="p-8 text-center text-slate-400">
                                            Belum ada data akun pengguna yang sesuai.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
