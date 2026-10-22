<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'INDOFROZ') }}</title>
        <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo.svg') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-100 antialiased bg-slate-950">
        <div class="min-h-screen flex items-center justify-center p-4 relative overflow-hidden bg-slate-950">
            <div class="w-full max-w-md z-10 animate-fade-in">
                <!-- Main Guest Card (100% Solid Dark, Non-transparent) -->
                <div class="bg-slate-900 border border-slate-800 shadow-2xl rounded-3xl p-8">
                    {{ $slot }}
                </div>
            </div>
        </div>

        <x-toast />
    </body>
</html>
