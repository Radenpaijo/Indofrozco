@props(['href' => null])

@php
    $previousUrl = url()->previous();
    $currentUrl = url()->current();
    $targetUrl = $href ?: (($previousUrl && $previousUrl !== $currentUrl) ? $previousUrl : route('dashboard'));
@endphp

<a href="{{ $targetUrl }}" {{ $attributes->merge(['class' => 'inline-flex items-center px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 hover:text-slate-900 text-xs font-semibold rounded-lg transition-all shadow-xs shrink-0 w-fit']) }}>
    <svg class="h-3.5 w-3.5 mr-1.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
    </svg>
    Kembali
</a>

