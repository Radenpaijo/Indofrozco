<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-left">
            <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                <x-back-button :href="route('dashboard')" />
                <div class="min-w-0">
                    <h2 class="font-bold text-xl sm:text-2xl text-slate-800 leading-tight truncate">
                        {{ __('Audit Log & Histori') }}
                    </h2>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Filter Card -->
        <x-card>
            <x-card.content class="p-4 sm:p-6">
                <form method="GET" action="{{ route('audit-logs.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <x-input type="text" name="search" value="{{ request('search') }}" placeholder="Cari aksi atau modul..." />
                    </div>
                    <div>
                        <x-select name="module">
                            <option value="">Semua Modul</option>
                            <option value="MASTER_BARANG" {{ request('module') === 'MASTER_BARANG' ? 'selected' : '' }}>Master Barang</option>
                            <option value="MASTER_GUDANG" {{ request('module') === 'MASTER_GUDANG' ? 'selected' : '' }}>Master Gudang</option>
                            <option value="INBOUND" {{ request('module') === 'INBOUND' ? 'selected' : '' }}>Barang Masuk</option>
                            <option value="OUTBOUND" {{ request('module') === 'OUTBOUND' ? 'selected' : '' }}>Barang Keluar</option>
                            <option value="STOCK_OPNAME" {{ request('module') === 'STOCK_OPNAME' ? 'selected' : '' }}>Stock Opname</option>
                            <option value="STOCK_ADJUSTMENT" {{ request('module') === 'STOCK_ADJUSTMENT' ? 'selected' : '' }}>Stock Adjustment</option>
                        </x-select>
                    </div>
                    <div class="flex items-center space-x-2">
                        <x-button type="submit" variant="default" class="flex-1">
                            Filter Log
                        </x-button>
                        @if(request()->anyFilled(['search', 'module']))
                            <a href="{{ route('audit-logs.index') }}">
                                <x-button type="button" variant="outline">
                                    Reset
                                </x-button>
                            </a>
                        @endif
                    </div>
                </form>
            </x-card.content>
        </x-card>

        <!-- Desktop Table View -->
        <div class="hidden sm:block">
            <x-table>
                <x-table.header>
                    <x-table.row>
                        <x-table.head>Waktu (Timestamp)</x-table.head>
                        <x-table.head>User Pelaksana</x-table.head>
                        <x-table.head>Aksi</x-table.head>
                        <x-table.head>Modul</x-table.head>
                        <x-table.head>Record ID</x-table.head>
                        <x-table.head>IP Address</x-table.head>
                        <x-table.head class="text-right">Aksi</x-table.head>
                    </x-table.row>
                </x-table.header>
                <x-table.body>
                    @forelse($logs as $log)
                        <x-table.row>
                            <x-table.cell class="font-mono text-xs text-slate-500">{{ $log->created_at->format('d/m/Y H:i:s') }}</x-table.cell>
                            <x-table.cell class="font-semibold text-slate-900">{{ $log->user->name ?? 'System' }}</x-table.cell>
                            <x-table.cell class="font-bold text-indigo-600 text-xs">{{ $log->action }}</x-table.cell>
                            <x-table.cell>
                                <x-badge variant="outline">
                                    {{ $log->module }}
                                </x-badge>
                            </x-table.cell>
                            <x-table.cell class="font-mono text-xs text-slate-600">#{{ $log->record_id ?? '-' }}</x-table.cell>
                            <x-table.cell class="text-xs font-mono text-slate-400">{{ $log->ip_address ?? '127.0.0.1' }}</x-table.cell>
                            <x-table.cell class="text-right">
                                <a href="{{ route('audit-logs.show', $log) }}">
                                    <x-button variant="outline" size="sm">Detail</x-button>
                                </a>
                            </x-table.cell>
                        </x-table.row>
                    @empty
                        <x-table.row>
                            <x-table.cell colspan="7" class="py-8 text-center text-slate-400">Belum ada catatan audit log.</x-table.cell>
                        </x-table.row>
                    @endforelse
                </x-table.body>
            </x-table>
        </div>

        <!-- Mobile Card View -->
        <div class="block sm:hidden space-y-3">
            @forelse($logs as $log)
                @php
                    $actionUpper = strtoupper($log->action);
                    $actionColor = match(true) {
                        str_contains($actionUpper, 'DELETE') || str_contains($actionUpper, 'REJECT') || str_contains($actionUpper, 'BATAL') => 'bg-rose-50 text-rose-700 border-rose-200',
                        str_contains($actionUpper, 'CREATE') || str_contains($actionUpper, 'STORE') || str_contains($actionUpper, 'APPROVE') => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                        str_contains($actionUpper, 'UPDATE') || str_contains($actionUpper, 'EDIT') => 'bg-sky-50 text-sky-700 border-sky-200',
                        default => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                    };
                @endphp
                <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-2xs space-y-3">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-mono text-slate-500">
                            {{ $log->created_at->format('d/m/Y H:i:s') }}
                        </span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $actionColor }}">
                            {{ $log->action }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100 text-xs">
                        <div>
                            <span class="text-slate-400 block text-[11px]">User:</span>
                            <span class="font-semibold text-slate-800">{{ $log->user->name ?? 'System' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[11px]">Modul & ID:</span>
                            <span class="font-medium text-slate-700">{{ $log->module }} #{{ $log->record_id ?? '-' }}</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-2 border-t border-slate-100 text-xs">
                        <span class="font-mono text-[11px] text-slate-400">IP: {{ $log->ip_address ?? '127.0.0.1' }}</span>
                        <a href="{{ route('audit-logs.show', $log) }}" class="inline-flex items-center gap-1 px-3 py-1 text-xs font-semibold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 rounded-lg transition-colors">
                            <span>Detail</span>
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl border border-slate-200 p-8 text-center text-slate-400 text-xs">
                    Belum ada catatan audit log.
                </div>
            @endforelse
        </div>

        @if($logs->hasPages())
            <div class="px-4 sm:px-6 py-3 sm:py-4 bg-white rounded-xl border border-slate-300">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
