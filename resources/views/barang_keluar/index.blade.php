<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Pengeluaran Gudang & Alokasi Batch FIFO') }}
            </h2>
            @if(auth()->user()->hasRole(['admin', 'gudang']))
                <a href="{{ route('barang-keluar.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow transition-colors">
                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Catat Rilis Manual
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Alert Messages -->
            @if(session('success'))
                <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded shadow-sm">
                    <span class="text-emerald-800 font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-rose-50 border-l-4 border-rose-500 p-4 rounded shadow-sm">
                    <span class="text-rose-800 font-medium">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Table Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead>
                            <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <th class="px-6 py-4">Nomor Pengeluaran</th>
                                <th class="px-6 py-4">Nama Barang</th>
                                <th class="px-6 py-4">Jumlah Keluar</th>
                                <th class="px-6 py-4">Batch Terpotong (FIFO)</th>
                                <th class="px-6 py-4">Petugas Gudang</th>
                                <th class="px-6 py-4">Ref. Permintaan</th>
                                <th class="px-6 py-4">Tanggal Pengeluaran</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse($barangKeluars as $keluar)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4 font-mono font-semibold text-gray-700">
                                        @if($keluar->permintaanBarang)
                                            REQ-{{ $keluar->permintaanBarang->no_permintaan }}
                                        @else
                                            MANUAL-OUT-{{ $keluar->id }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 font-medium text-gray-800">
                                        {{ $keluar->snapshot_nama_barang ?? ($keluar->barang->nama_barang ?? '-') }}
                                    </td>
                                    <td class="px-6 py-4 font-bold text-rose-600">
                                        -{{ $keluar->jumlah }} {{ $keluar->barang->satuan ?? '' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($keluar->inventoryTransactions && $keluar->inventoryTransactions->count() > 0)
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($keluar->inventoryTransactions as $tx)
                                                    @if($tx->batch)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-mono font-medium bg-indigo-50 text-indigo-700 border border-indigo-200">
                                                            {{ $tx->batch->batch_number }} (-{{ (float)$tx->jumlah }})
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-mono font-medium bg-gray-100 text-gray-600">
                                                            Direct Stock (-{{ (float)$tx->jumlah }})
                                                        </span>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400 italic">No batch ledger</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        {{ $keluar->user->name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-500 font-mono">
                                        @if($keluar->permintaanBarang)
                                            <a href="{{ route('permintaan-barang.show', $keluar->permintaanBarang->id) }}" class="text-indigo-600 hover:underline">
                                                {{ $keluar->permintaanBarang->no_permintaan }}
                                            </a>
                                        @else
                                            <span class="text-gray-400 italic">Rilis Manual</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-gray-500 text-xs font-mono">
                                        {{ \Carbon\Carbon::parse($keluar->tanggal)->format('d M Y H:i') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                        Belum ada pengeluaran gudang.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($barangKeluars->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                        {{ $barangKeluars->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
