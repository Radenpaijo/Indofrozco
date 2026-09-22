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
        <!-- Filter Card -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <form method="GET" action="{{ route('audit-logs.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari aksi atau modul..." class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                </div>
                <div>
                    <select name="module" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 py-2.5">
                        <option value="">Semua Modul</option>
                        <option value="MASTER_BARANG" {{ request('module') === 'MASTER_BARANG' ? 'selected' : '' }}>Master Barang</option>
                        <option value="MASTER_GUDANG" {{ request('module') === 'MASTER_GUDANG' ? 'selected' : '' }}>Master Gudang</option>
                        <option value="INBOUND" {{ request('module') === 'INBOUND' ? 'selected' : '' }}>Barang Masuk</option>
                        <option value="OUTBOUND" {{ request('module') === 'OUTBOUND' ? 'selected' : '' }}>Barang Keluar</option>
                        <option value="STOCK_OPNAME" {{ request('module') === 'STOCK_OPNAME' ? 'selected' : '' }}>Stock Opname</option>
                        <option value="STOCK_ADJUSTMENT" {{ request('module') === 'STOCK_ADJUSTMENT' ? 'selected' : '' }}>Stock Adjustment</option>
                    </select>
                </div>
                <div class="flex items-center space-x-2">
                    <button type="submit" class="flex-1 inline-flex justify-center items-center px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-xl transition-all shadow-sm">
                        Filter Log
                    </button>
                    @if(request()->anyFilled(['search', 'module']))
                        <a href="{{ route('audit-logs.index') }}" class="inline-flex items-center px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition-all">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/70 border-b border-slate-200 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            <th class="py-3.5 px-6">Waktu (Timestamp)</th>
                            <th class="py-3.5 px-6">User Pelaksana</th>
                            <th class="py-3.5 px-6">Aksi</th>
                            <th class="py-3.5 px-6">Modul</th>
                            <th class="py-3.5 px-6">Record ID</th>
                            <th class="py-3.5 px-6">IP Address</th>
                            <th class="py-3.5 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse($logs as $log)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="py-4 px-6 font-mono text-xs text-slate-500">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                                <td class="py-4 px-6 font-semibold text-slate-900">{{ $log->user->name ?? 'System' }}</td>
                                <td class="py-4 px-6 font-bold text-indigo-600 text-xs">{{ $log->action }}</td>
                                <td class="py-4 px-6">
                                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold rounded-lg bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $log->module }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 font-mono text-xs text-slate-600">#{{ $log->record_id ?? '-' }}</td>
                                <td class="py-4 px-6 text-xs font-mono text-slate-400">{{ $log->ip_address ?? '127.0.0.1' }}</td>
                                <td class="py-4 px-6 text-right">
                                    <a href="{{ route('audit-logs.show', $log) }}" class="inline-flex items-center px-3 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold rounded-lg border border-indigo-200 transition">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400">Belum ada catatan audit log.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($logs->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
