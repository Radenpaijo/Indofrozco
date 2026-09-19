<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-slate-800 leading-tight">
                {{ __('Detail Audit Log #') }}{{ $auditLog->id }}
            </h2>
            <a href="{{ route('audit-logs.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-xl transition">
                &larr; Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-200/80 p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm mb-6 pb-6 border-b border-slate-100">
                    <div>
                        <span class="text-xs font-bold uppercase text-slate-400 block">Waktu (Timestamp)</span>
                        <span class="font-mono font-semibold text-slate-800 text-sm">{{ $auditLog->created_at->format('d/m/Y H:i:s') }}</span>
                    </div>
                    <div>
                        <span class="text-xs font-bold uppercase text-slate-400 block">User Pelaksana</span>
                        <span class="font-semibold text-slate-800">{{ $auditLog->user->name ?? 'System' }}</span>
                    </div>
                    <div>
                        <span class="text-xs font-bold uppercase text-slate-400 block">Modul & Aksi</span>
                        <span class="px-2.5 py-1 text-xs font-bold rounded-md bg-indigo-50 text-indigo-700 border border-indigo-200">
                            {{ $auditLog->module }} &bull; {{ $auditLog->action }}
                        </span>
                    </div>
                    <div>
                        <span class="text-xs font-bold uppercase text-slate-400 block">IP Address</span>
                        <span class="font-mono text-xs text-slate-600">{{ $auditLog->ip_address ?? '127.0.0.1' }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nilai Lama (Old Values) -->
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                        <h4 class="font-bold text-xs uppercase tracking-wider text-slate-500 mb-3 flex items-center">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500 mr-2"></span> Nilai Sebelum (Old Values)
                        </h4>
                        @if($auditLog->old_values)
                            <pre class="bg-white p-3 rounded-lg border border-slate-200 text-xs font-mono text-slate-800 overflow-x-auto">{{ json_encode($auditLog->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        @else
                            <p class="text-xs text-slate-400 italic">Tidak ada nilai lama (Record Baru)</p>
                        @endif
                    </div>

                    <!-- Nilai Baru (New Values) -->
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                        <h4 class="font-bold text-xs uppercase tracking-wider text-slate-500 mb-3 flex items-center">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 mr-2"></span> Nilai Sesudah (New Values)
                        </h4>
                        @if($auditLog->new_values)
                            <pre class="bg-white p-3 rounded-lg border border-slate-200 text-xs font-mono text-slate-800 overflow-x-auto">{{ json_encode($auditLog->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        @else
                            <p class="text-xs text-slate-400 italic">Tidak ada perubahan nilai sesudah</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
