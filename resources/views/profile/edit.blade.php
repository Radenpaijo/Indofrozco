<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 text-left">
            <div class="flex items-center space-x-4">
                <x-back-button />
                <div>
                    <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                        {{ __('Profil Pengguna & Keamanan') }}
                    </h2>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="w-full space-y-6">
        <div class="p-4 sm:p-8 bg-white shadow-sm rounded-2xl border border-slate-200/80">
            <div class="w-full">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="p-4 sm:p-8 bg-white shadow-sm rounded-2xl border border-slate-200/80">
            <div class="w-full">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="p-4 sm:p-8 bg-white shadow-sm rounded-2xl border border-slate-200/80">
            <div class="w-full">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
