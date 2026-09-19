<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-slate-800 leading-tight">
                {{ __('Gudang') }}
            </h2>
            <a href="{{ route('warehouses.create') }}"
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition shadow-md shadow-indigo-600/20">
                + Tambah Gudang
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

            @if(session('error'))
                <div class="p-4 bg-rose-50 text-rose-700 border border-rose-200 rounded-xl text-sm font-medium">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-200/80">
                <div class="p-6 text-slate-900">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr
                                class="border-b border-slate-200 bg-slate-50/50 text-xs font-bold uppercase text-slate-500 tracking-wider">
                                <th class="p-4">Kode</th>
                                <th class="p-4">Nama Gudang</th>
                                <th class="p-4">Tipe Storage</th>
                                <th class="p-4">Total Lokasi/Rak</th>
                                <th class="p-4">Status</th>
                                <th class="p-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($warehouses as $wh)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="p-4 font-mono font-semibold text-indigo-600">{{ $wh->code }}</td>
                                    <td class="p-4 font-semibold text-slate-800">{{ $wh->name }}</td>
                                    <td class="p-4">
                                        <span
                                            class="px-2.5 py-1 text-xs font-bold rounded-md bg-blue-50 text-blue-700 border border-blue-200">
                                            {{ strtoupper(str_replace('_', ' ', $wh->type)) }}
                                        </span>
                                    </td>
                                    <td class="p-4 font-semibold text-slate-600">{{ $wh->locations_count }} Rak</td>
                                    <td class="p-4">
                                        <span
                                            class="px-2.5 py-1 text-xs font-bold rounded-full {{ $wh->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                            {{ strtoupper($wh->status) }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-right">
                                        <div class="flex items-center justify-end gap-3">
                                            <a href="{{ route('warehouses.edit', $wh) }}">
                                                <x-edit-button></x-edit-button>
                                            </a>

                                            <x-modal-hapus name="{{ $wh->name }}"
                                                action="{{ route('warehouses.destroy', $wh) }}" />
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-slate-400">Belum ada data gudang.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $warehouses->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>