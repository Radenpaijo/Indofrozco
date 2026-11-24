<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                <x-back-button :href="route('dashboard')" />
                <div class="min-w-0">
                    <h2 class="font-bold text-xl sm:text-2xl text-slate-800 leading-tight truncate">
                        {{ __('Manajemen Pengguna') }}
                    </h2>
                </div>
            </div>
            <a href="{{ route('users.create') }}" class="w-full sm:w-auto shrink-0 self-start sm:self-center">
                <x-button variant="indigo" class="w-full sm:w-auto justify-center shadow-sm">
                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Pengguna
                </x-button>
            </a>
        </div>
    </x-slot>

    <div class="w-full space-y-4">

        <!-- Filter Card -->
        <x-card class="p-3.5 sm:p-5">
            <form method="GET" action="{{ route('users.index') }}" class="flex flex-col sm:flex-row gap-2.5">
                <div class="flex-grow">
                    <x-input id="search" type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..." class="w-full text-xs" />
                </div>
                <div class="w-full sm:w-48">
                    <x-select id="role" name="role" class="w-full text-xs">
                        <option value="">Semua Role</option>
                        @foreach($roles as $key => $label)
                            <option value="{{ $key }}" {{ request('role') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </x-select>
                </div>
                <div class="flex items-center gap-2">
                    <x-button type="submit" variant="default" class="text-xs px-4 flex-1 sm:flex-none justify-center">Filter</x-button>
                    @if(request()->hasAny(['search', 'role']))
                        <a href="{{ route('users.index') }}">
                            <x-button type="button" variant="outline" class="text-xs px-3">Reset</x-button>
                        </a>
                    @endif
                </div>
            </form>
        </x-card>

        <!-- 1. Mobile Card View (block sm:hidden) -->
        <div class="space-y-3 sm:hidden">
            @forelse($users as $user)
                @php
                    $words = preg_split('/\s+/', trim($user->name));
                    $initials = count($words) >= 2 
                        ? strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1))
                        : strtoupper(substr($user->name, 0, 2));
                @endphp
                <div class="bg-white rounded-xl border border-slate-200 shadow-2xs p-4 space-y-3">
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center space-x-2.5 min-w-0">
                            <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-800 border border-slate-200 flex items-center justify-center font-bold text-xs shrink-0">
                                {{ $initials }}
                            </div>
                            <div class="min-w-0">
                                <div class="font-bold text-slate-800 text-xs truncate flex items-center gap-1.5">
                                    <span>{{ $user->name }}</span>
                                    @if(auth()->id() === $user->id)
                                        <span class="text-[9px] font-semibold text-indigo-700 bg-indigo-50 border border-indigo-200 rounded px-1">Anda</span>
                                    @endif
                                </div>
                                <div class="text-[11px] text-slate-500 font-mono truncate">{{ $user->email }}</div>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-100 text-slate-800 border border-slate-200 shrink-0">
                            {{ $roles[$user->role] ?? strtoupper($user->role) }}
                        </span>
                    </div>

                    @php
                        $totalFeatures = count(\App\Models\User::getAllPermissionKeys());
                        if (is_array($user->permissions)) {
                            $count = count($user->permissions);
                            $accessText = "{$count} dari {$totalFeatures} Fitur";
                        } else {
                            $accessText = 'Akses Default Role';
                        }
                    @endphp
                    <div class="flex items-center justify-between text-xs py-2 border-y border-slate-100 text-slate-600">
                        <span class="text-[11px] text-slate-500">Akses Fitur:</span>
                        <span class="font-medium text-slate-800 text-[11px]">{{ $accessText }}</span>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-0.5">
                        <a href="{{ route('users.edit', $user) }}" class="flex-1">
                            <x-button variant="outline" size="sm" class="w-full text-xs justify-center py-1.5">
                                Edit Hak Akses
                            </x-button>
                        </a>
                        @if(auth()->id() !== $user->id)
                            <x-modal-hapus name="{{ $user->name }}" action="{{ route('users.destroy', $user) }}" />
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl border border-dashed border-slate-200 p-8 text-center text-slate-400 text-xs">
                    Belum ada data akun pengguna yang sesuai.
                </div>
            @endforelse
        </div>

        <!-- 2. Desktop Table View (hidden sm:block) -->
        <div class="hidden sm:block">
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
                            @php
                                $words = preg_split('/\s+/', trim($user->name));
                                $initials = count($words) >= 2 
                                    ? strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1))
                                    : strtoupper(substr($user->name, 0, 2));
                            @endphp
                            <x-table.cell>
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-full bg-slate-100 text-slate-800 border border-slate-300 flex items-center justify-center font-bold text-xs shrink-0">
                                        {{ $initials }}
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
                            <x-table.cell class="text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('users.edit', $user) }}">
                                        <x-button variant="outline" size="sm" class="text-xs">Edit Hak Akses</x-button>
                                    </a>
                                    @if(auth()->id() !== $user->id)
                                        <x-modal-hapus name="{{ $user->name }}" action="{{ route('users.destroy', $user) }}" />
                                    @else
                                        <button type="button" disabled class="p-1.5 text-slate-300 border border-slate-200 rounded-md cursor-not-allowed inline-flex items-center justify-center shrink-0 opacity-40 shadow-2xs" title="Tidak dapat menghapus akun sendiri">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>
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
        </div>

        @if($users->hasPages())
            <div class="px-6 py-4 bg-white rounded-lg border border-slate-300">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
