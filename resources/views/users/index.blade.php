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

    <div class="w-full space-y-6">

        <!-- Filter Card -->
        <x-card>
            <x-card.content class="p-4 sm:p-6">
                <form method="GET" action="{{ route('users.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="space-y-1">
                        <x-label for="search">Cari Akun</x-label>
                        <x-input id="search" type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..." />
                    </div>
                    <div class="space-y-1">
                        <x-label for="role">Filter Role</x-label>
                        <x-select id="role" name="role">
                            <option value="">Semua Role</option>
                            @foreach($roles as $key => $label)
                                <option value="{{ $key }}" {{ request('role') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </x-select>
                    </div>
                    <div class="flex items-end space-x-2">
                        <x-button type="submit" variant="default">Filter</x-button>
                        @if(request()->hasAny(['search', 'role']))
                            <a href="{{ route('users.index') }}">
                                <x-button type="button" variant="outline">Reset</x-button>
                            </a>
                        @endif
                    </div>
                </form>
            </x-card.content>
        </x-card>

        <!-- Users Table Card -->
        <x-table>
            <x-table.header>
                <x-table.row>
                    <x-table.head>Pengguna</x-table.head>
                    <x-table.head>Role / Peranan</x-table.head>
                    <x-table.head>Akses Fitur & Menu</x-table.head>
                    <x-table.head>Tanggal Buat</x-table.head>
                    <x-table.head class="text-right">Aksi</x-table.head>
                </x-table.row>
            </x-table.header>
            <x-table.body>
                @forelse($users as $user)
                    <x-table.row>
                        <x-table.cell>
                            <div class="flex items-center space-x-3">
                                <div class="w-9 h-9 rounded-full bg-slate-200 text-slate-800 border border-slate-300 flex items-center justify-center font-bold text-xs">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="font-bold text-slate-800 flex items-center space-x-2">
                                        <span>{{ $user->name }}</span>
                                        @if(auth()->id() === $user->id)
                                            <x-badge variant="outline" class="text-[10px] text-indigo-600 border-indigo-200 bg-indigo-50">Anda</x-badge>
                                        @endif
                                    </div>
                                    <div class="text-xs text-slate-500 font-mono">{{ $user->email }}</div>
                                </div>
                            </div>
                        </x-table.cell>
                        <x-table.cell>
                            <x-badge variant="outline">
                                {{ $roles[$user->role] ?? strtoupper($user->role) }}
                            </x-badge>
                        </x-table.cell>
                        <x-table.cell>
                            @php
                                $totalFeatures = count(\App\Models\User::getAllPermissionKeys());
                                if (is_array($user->permissions)) {
                                    $count = count($user->permissions);
                                    $accessText = "{$count} dari {$totalFeatures} Fitur";
                                } else {
                                    $accessText = 'Akses Default Role';
                                }
                            @endphp
                            <x-badge variant="secondary">
                                {{ $accessText }}
                            </x-badge>
                        </x-table.cell>
                        <x-table.cell class="text-xs text-slate-500 font-mono">
                            {{ $user->created_at ? $user->created_at->format('d M Y H:i') : '-' }}
                        </x-table.cell>
                        <x-table.cell class="text-right space-x-2">
                            <a href="{{ route('users.edit', $user) }}">
                                <x-button variant="outline" size="sm">Edit Hak Akses</x-button>
                            </a>
                            @if(auth()->id() !== $user->id)
                                <x-modal-hapus name="{{ $user->name }}" action="{{ route('users.destroy', $user) }}" />
                            @else
                                <x-button type="button" disabled variant="outline" size="sm" class="opacity-50 cursor-not-allowed" title="Tidak dapat menghapus akun sendiri">
                                    Hapus
                                </x-button>
                            @endif
                        </x-table.cell>
                    </x-table.row>
                @empty
                    <x-table.row>
                        <x-table.cell colspan="5" class="p-8 text-center text-slate-400">
                            Belum ada data akun pengguna yang sesuai.
                        </x-table.cell>
                    </x-table.row>
                @endforelse
            </x-table.body>
        </x-table>

        @if($users->hasPages())
            <div class="px-6 py-4 bg-white rounded-lg border border-slate-300">
                {{ $users->links() }}
            </div>
        @endif
    </div>
        </div>
    </div>
</x-app-layout>
