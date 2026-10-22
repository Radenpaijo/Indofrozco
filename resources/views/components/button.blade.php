@props([
    'variant' => 'default', // default | secondary | outline | ghost | destructive | link
    'size' => 'default', // default | sm | lg | icon
    'type' => 'button',
])

@php
    $baseClasses = 'inline-flex items-center justify-center whitespace-nowrap rounded-md text-xs font-semibold transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-slate-950 disabled:pointer-events-none disabled:opacity-50';

    $variants = [
        'default' => 'bg-slate-900 text-slate-50 shadow hover:bg-slate-900/90',
        'indigo' => 'bg-indigo-600 text-white shadow-sm hover:bg-indigo-700',
        'destructive' => 'bg-red-600 text-white shadow-sm hover:bg-red-700',
        'outline' => 'border border-slate-300 bg-white text-slate-700 shadow-sm hover:bg-slate-100 hover:text-slate-900',
        'secondary' => 'bg-slate-100 text-slate-900 shadow-sm hover:bg-slate-200/80',
        'ghost' => 'hover:bg-slate-100 hover:text-slate-900 text-slate-700',
        'link' => 'text-slate-900 underline-offset-4 hover:underline',
    ];

    $sizes = [
        'default' => 'h-9 px-4 py-2',
        'sm' => 'h-8 rounded-md px-3 text-xs',
        'lg' => 'h-10 rounded-md px-8 text-sm',
        'icon' => 'h-9 w-9 p-0',
    ];

    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['default']) . ' ' . ($sizes[$size] ?? $sizes['default']);
@endphp

<button {{ $attributes->merge(['type' => $type, 'class' => $classes]) }}>
    {{ $slot }}
</button>