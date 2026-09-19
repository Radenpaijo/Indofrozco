<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-slate-800 leading-tight">
                {{ __('Mutasi Lokasi / Rak Gudang') }}
            </h2>
            <a href="{{ route('stock-mutations.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition shadow-md shadow-indigo-600/20">
                + Mutasi Stok Baru
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
                                <th class="p-4">Nomor Mutasi</th>
                                <th class="p-4">Tanggal</th>
                                <th class="p-4">Item Mutasi</th>
                                <th class="p-4">Dibuat Oleh</th>
                                <th class="p-4">Status</th>
                                <th class="p-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($mutations as $mut)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="p-4 font-mono font-bold text-indigo-600">{{ $mut->nomor_mutasi }}</td>
                                    <td class="p-4 text-slate-700 font-medium">{{ $mut->tanggal->format('d M Y') }}</td>
                                    <td class="p-4 font-semibold text-slate-800">{{ $mut->details->count() }} Barang</td>
                                    <td class="p-4 font-semibold text-slate-800">{{ $mut->user->name }}</td>
                                    <td class="p-4">
                                        <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-emerald-100 text-emerald-700">
                                            {{ strtoupper($mut->status) }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-right">
                                        <a href="{{ route('stock-mutations.show', $mut) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold text-xs">Detail</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-slate-400">Belum ada riwayat mutasi barang antar rak.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $mutations->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
