<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-800 leading-tight">
            {{ __('Audit Log & Histori Sistem') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-200/80">
                <div class="p-6 text-slate-900">
                    <form method="GET" action="{{ route('audit-logs.index') }}" class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-3">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari aksi atau modul..." class="rounded-xl border-slate-300 text-sm">
                        <select name="module" class="rounded-xl border-slate-300 text-sm">
                            <option value="">Semua Modul</option>
                            <option value="INBOUND" {{ request('module') === 'INBOUND' ? 'selected' : '' }}>Inbound</option>
                            <option value="OUTBOUND" {{ request('module') === 'OUTBOUND' ? 'selected' : '' }}>Outbound</option>
                            <option value="STOCK_OPNAME" {{ request('module') === 'STOCK_OPNAME' ? 'selected' : '' }}>Stock Opname</option>
                            <option value="STOCK_ADJUSTMENT" {{ request('module') === 'STOCK_ADJUSTMENT' ? 'selected' : '' }}>Stock Adjustment</option>
                            <option value="STOCK_MUTATION" {{ request('module') === 'STOCK_MUTATION' ? 'selected' : '' }}>Stock Mutation</option>
                        </select>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition shadow-md shadow-indigo-600/20">
                            Filter Log
                        </button>
                    </form>

                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50/50 text-xs font-bold uppercase text-slate-500 tracking-wider">
                                <th class="p-4">Waktu (Timestamp)</th>
                                <th class="p-4">User</th>
                                <th class="p-4">Aksi</th>
                                <th class="p-4">Modul</th>
                                <th class="p-4">Record ID</th>
                                <th class="p-4">IP Address</th>
                                <th class="p-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($logs as $log)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="p-4 font-mono text-xs text-slate-500">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                                    <td class="p-4 font-semibold text-slate-800">{{ $log->user->name ?? 'System' }}</td>
                                    <td class="p-4 font-bold text-indigo-600">{{ $log->action }}</td>
                                    <td class="p-4">
                                        <span class="px-2.5 py-1 text-xs font-bold rounded-md bg-slate-100 text-slate-700">
                                            {{ $log->module }}
                                        </span>
                                    </td>
                                    <td class="p-4 font-mono text-xs text-slate-600">#{{ $log->record_id ?? '-' }}</td>
                                    <td class="p-4 text-xs font-mono text-slate-400">{{ $log->ip_address ?? '127.0.0.1' }}</td>
                                    <td class="p-4 text-right">
                                        <a href="{{ route('audit-logs.show', $log) }}" class="px-3 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold rounded-lg border border-indigo-200 transition">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-slate-400">Belum ada catatan audit log.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $logs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
