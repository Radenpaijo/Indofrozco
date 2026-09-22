<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-4 min-w-0">
                <x-back-button />
                <div class="min-w-0">
                    <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                        {{ __('Stock Opname (Perhitungan Stok Fisik)') }}
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">Audit dan rekonsiliasi jumlah fisik persediaan barang.</p>
                </div>
            </div>
            <a href="{{ route('stock-opnames.create') }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-md transition-all shrink-0">
                <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Buat Sesi Opname
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="p-4 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-xl text-sm font-medium">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-200/80">
                <div class="p-6 text-slate-900">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50/50 text-xs font-bold uppercase text-slate-500 tracking-wider">
                                <th class="p-4">Nomor Opname</th>
                                <th class="p-4">Tanggal</th>
                                <th class="p-4">Gudang / Lokasi</th>
                                <th class="p-4">Petugas</th>
                                <th class="p-4">Status</th>
                                <th class="p-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($opnames as $op)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="p-4 font-mono font-bold text-indigo-600">{{ $op->nomor_opname }}</td>
                                    <td class="p-4 text-slate-700 font-medium">{{ $op->tanggal->format('d M Y') }}</td>
                                    <td class="p-4 text-slate-600">{{ $op->warehouse->name ?? 'Semua Gudang' }} {{ $op->location ? '('.$op->location->name.')' : '' }}</td>
                                    <td class="p-4 font-semibold text-slate-800">{{ $op->user->name }}</td>
                                    <td class="p-4">
                                        <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-emerald-100 text-emerald-700">
                                            {{ strtoupper($op->status) }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-right">
                                        <a href="{{ route('stock-opnames.show', $op) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold text-xs">Detail</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-slate-400">Belum ada riwayat stock opname.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $opnames->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
