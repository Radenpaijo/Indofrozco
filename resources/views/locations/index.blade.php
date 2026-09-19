<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-slate-800 leading-tight">
                {{ __('Lokasi / Rak Gudang') }}
            </h2>
            <a href="{{ route('locations.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition shadow-md shadow-indigo-600/20">
                + Tambah Lokasi/Rak
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
                            <tr class="border-b border-slate-200 bg-slate-50/50 text-xs font-bold uppercase text-slate-500 tracking-wider">
                                <th class="p-4">Kode Rak</th>
                                <th class="p-4">Nama Rak</th>
                                <th class="p-4">Gudang</th>
                                <th class="p-4">Kapasitas</th>
                                <th class="p-4">Status</th>
                                <th class="p-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($locations as $loc)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="p-4 font-mono font-bold text-indigo-600">{{ $loc->code }}</td>
                                    <td class="p-4 font-semibold text-slate-800">{{ $loc->name }}</td>
                                    <td class="p-4 text-slate-600 font-medium">{{ $loc->warehouse->name ?? '-' }}</td>
                                    <td class="p-4 font-semibold text-slate-700">{{ $loc->capacity ? number_format($loc->capacity, 2) . ' KG' : '-' }}</td>
                                    <td class="p-4">
                                        <span class="px-2.5 py-1 text-xs font-bold rounded-full {{ $loc->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                            {{ strtoupper($loc->status) }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-right space-x-2">
                                        <a href="{{ route('locations.edit', $loc) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold text-xs">Edit</a>
                                        <form action="{{ route('locations.destroy', $loc) }}" method="POST" class="inline" onsubmit="return confirm('Yakin menghapus lokasi rak ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-600 hover:text-rose-900 font-semibold text-xs">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-slate-400">Belum ada data lokasi rak.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $locations->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
