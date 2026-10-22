@props([
    'orientation' => 'horizontal', // horizontal | vertical
])

@php
    $classes = $orientation === 'vertical' 
        ? 'shrink-0 bg-slate-200 w-[1px] h-full' 
        : 'shrink-0 bg-slate-200 h-[1px] w-full my-4';
@endphp

<div {{ $attributes->merge(['class' => $classes]) }} role="separator"></div>
