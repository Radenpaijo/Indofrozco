<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 text-left">
            <div class="flex items-center space-x-4">
                <x-back-button />
                <div>
                    <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                        {{ __('Audit Log & Histori Sistem') }}
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">Rekam jejak seluruh aktivitas user dan perubahan data master.</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
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

        <!-- Table Card -->
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

        @if($logs->hasPages())
            <div class="px-6 py-4 bg-white rounded-lg border border-slate-300">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
    </div>
</x-app-layout>
