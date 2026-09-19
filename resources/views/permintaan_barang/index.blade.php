<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Permintaan Barang (Produksi ➔ Gudang)') }}
            </h2>
            @if(auth()->user()->hasRole(['admin', 'produksi']))
                <a href="{{ route('permintaan-barang.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow transition-colors">
                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Ajukan Permintaan
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Alert Messages -->
            @if(session('success'))
                <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded shadow-sm">
                    <div class="flex items-center space-x-2">
                        <svg class="h-5 w-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-emerald-800 font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-rose-50 border-l-4 border-rose-500 p-4 rounded shadow-sm">
                    <div class="flex items-center space-x-2">
                        <svg class="h-5 w-5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-rose-800 font-medium">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- Table Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead>
                            <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <th class="px-6 py-4">No. Permintaan</th>
                                <th class="px-6 py-4">Tanggal Diajukan</th>
                                <th class="px-6 py-4">Pemohon (Produksi)</th>
                                <th class="px-6 py-4">Jumlah Item</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse($permintaans as $permintaan)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4 font-mono font-semibold text-gray-700">
                                        {{ $permintaan->no_permintaan }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        {{ \Carbon\Carbon::parse($permintaan->tanggal)->format('d M Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-900 font-medium">
                                        {{ $permintaan->user->name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-500">
                                        {{ $permintaan->details->count() }} Item
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($permintaan->status === 'pending')
                                            <span class="px-2.5 py-1 text-xs font-bold bg-amber-50 text-amber-700 rounded-full">PENDING</span>
                                        @elseif($permintaan->status === 'selesai')
                                            <span class="px-2.5 py-1 text-xs font-bold bg-emerald-50 text-emerald-700 rounded-full">DISETUJUI (SELESAI)</span>
                                        @elseif($permintaan->status === 'ditolak')
                                            <span class="px-2.5 py-1 text-xs font-bold bg-rose-50 text-rose-700 rounded-full">DITOLAK</span>
                                        @else
                                            <span class="px-2.5 py-1 text-xs font-bold bg-gray-50 text-gray-600 rounded-full">{{ strtoupper($permintaan->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('permintaan-barang.show', $permintaan->id) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold text-xs transition-colors">Detail Permintaan</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                        Belum ada pengajuan permintaan barang.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($permintaans->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                        {{ $permintaans->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
