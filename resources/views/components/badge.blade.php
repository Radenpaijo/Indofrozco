@props([
    'variant' => 'default', // default | secondary | outline | destructive
])

@php
    $baseClasses = 'inline-flex items-center rounded-md border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-slate-950 focus:ring-offset-2';

    $variants = [
        'default' => 'border-transparent bg-slate-900 text-slate-50 shadow hover:bg-slate-900/80',
        'secondary' => 'border-slate-300 bg-slate-100 text-slate-700 hover:bg-slate-200/80',
        'destructive' => 'border-transparent bg-red-600 text-white shadow hover:bg-red-700',
        'outline' => 'border-slate-300 text-slate-950',
    ];

    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['secondary']);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>