@props([
    'variant' => 'default', // default | secondary | outline | destructive | warning | success | danger | info
])

@php
    $baseClasses = 'inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none';

    $variants = [
        'default' => 'border-transparent bg-slate-900 text-slate-50 shadow-xs hover:bg-slate-900/80',
        'secondary' => 'border-slate-300 bg-slate-100 text-slate-700 hover:bg-slate-200/80',
        'destructive' => 'border-transparent bg-red-600 text-white shadow-xs hover:bg-red-700',
        'outline' => 'border-slate-300 text-slate-800 bg-white',
        'warning' => 'border-amber-200/90 bg-amber-50 text-amber-800',
        'success' => 'border-emerald-200/90 bg-emerald-50 text-emerald-800',
        'danger' => 'border-rose-200/90 bg-rose-50 text-rose-800',
        'info' => 'border-sky-200/90 bg-sky-50 text-sky-800',
    ];

    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['secondary']);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>