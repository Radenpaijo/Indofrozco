<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Permintaan Pembelian (Purchase Requisition - PR)') }}
            </h2>
            @if(auth()->user()->hasRole(['admin', 'gudang']))
                <a href="{{ route('permintaan-pembelian.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow transition-colors">
                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Buat Requisition (PR)
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

            <!-- Table Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead>
                            <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <th class="px-6 py-4">No. PR</th>
                                <th class="px-6 py-4">Tanggal Diajukan</th>
                                <th class="px-6 py-4">Pembuat Requisition (Gudang)</th>
                                <th class="px-6 py-4">Jumlah Item</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse($prs as $pr)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4 font-mono font-semibold text-gray-700">
                                        {{ $pr->no_pr }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        {{ \Carbon\Carbon::parse($pr->tanggal)->format('d M Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-900 font-medium">
                                        {{ $pr->user->name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-500">
                                        {{ $pr->details->count() }} Item
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($pr->status === 'pending')
                                            <span class="px-2.5 py-1 text-xs font-bold bg-amber-50 text-amber-700 rounded-full">PENDING</span>
                                        @elseif($pr->status === 'di_po')
                                            <span class="px-2.5 py-1 text-xs font-bold bg-emerald-50 text-emerald-700 rounded-full">TELAH DI-PO-KAN</span>
                                        @elseif($pr->status === 'dibatalkan')
                                            <span class="px-2.5 py-1 text-xs font-bold bg-gray-100 text-gray-500 rounded-full">DIBATALKAN</span>
                                        @else
                                            <span class="px-2.5 py-1 text-xs font-bold bg-gray-50 text-gray-600 rounded-full">{{ strtoupper($pr->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <a href="{{ route('permintaan-pembelian.show', $pr->id) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold text-xs transition-colors">Detail PR</a>
                                        
                                        @if($pr->status === 'pending' && auth()->user()->hasRole(['admin', 'purchasing']))
                                            <a href="{{ route('purchase-orders.create', ['pr_id' => $pr->id]) }}" class="text-emerald-600 hover:text-emerald-900 font-bold text-xs transition-colors">Proses Jadi PO</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                        Belum ada pengajuan Purchase Requisition (PR).
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($prs->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                        {{ $prs->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
