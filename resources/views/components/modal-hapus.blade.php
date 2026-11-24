@props([
    'name' => 'data ini',
    'action' => null,
])

<div x-data="{ open: false }" class="inline-flex items-center">
    {{-- Tombol Hapus --}}
    <x-delete-button @click="open = true"></x-delete-button>

    {{-- Modal --}}
    <div x-show="open" x-cloak x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        @keydown.escape.window="open = false">
        <div x-show="open" x-transition.scale @click.outside="open = false"
            class="w-full max-w-md rounded-xl bg-white shadow-2xl">
            {{-- Header --}}
            <div class="flex items-center justify-between border-b px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    Konfirmasi Hapus
                </h3>

                <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-600 text-xl">
                    &times;
                </button>
            </div>

            {{-- Content --}}
            <div class="px-6 py-5">
                <p class="text-sm text-gray-600">
                    Apakah Anda yakin ingin menghapus
                    <span class="font-semibold text-gray-900">
                        {{ $name }}
                    </span>
                    ?
                </p>

                <p class="mt-2 text-xs text-gray-500">
                    Data yang sudah dihapus tidak dapat dikembalikan.
                </p>
            </div>

            {{-- Footer --}}
            <div class="flex justify-end gap-3 border-t bg-gray-50 px-6 py-4 rounded-b-xl">
                <button type="button" @click="open = false"
                    class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100">
                    Batal
                </button>

                <form action="{{ $action }}" method="POST">
                    @csrf
                    @method('DELETE')

                    <button type="submit"
                        class="rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700">
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>