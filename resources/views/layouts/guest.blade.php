<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-100 antialiased bg-slate-950">
        <div class="min-h-screen flex items-center justify-center p-4 relative overflow-hidden bg-gradient-to-tr from-slate-950 via-slate-900 to-indigo-950">
            <!-- Background Decorative Blobs -->
            <div class="absolute top-[-10%] left-[-10%] w-[500px] h-[500px] rounded-full bg-indigo-500/10 blur-[120px] pointer-events-none"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-[500px] h-[500px] rounded-full bg-sky-500/10 blur-[120px] pointer-events-none"></div>

            <div class="w-full max-w-md z-10 animate-fade-in">
                <!-- Main Guest Card -->
                <div class="bg-slate-900/60 backdrop-blur-xl border border-slate-800/80 shadow-2xl rounded-3xl p-8">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
