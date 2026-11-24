@props(['required' => false])

@php
    $content = (string) $slot;
    $hasStar = str_ends_with(trim($content), '*');
    if ($hasStar) {
        $cleanContent = rtrim(trim($content), '* ');
        $isRequired = true;
    } else {
        $cleanContent = $content;
        $isRequired = (bool) $required;
    }
@endphp

<label {{ $attributes->merge(['class' => 'text-xs font-bold uppercase tracking-wider text-slate-600 block mb-1']) }}>
    {!! $cleanContent !!}@if($isRequired)<span class="text-rose-500 font-bold ml-1">*</span>@endif
</label>
