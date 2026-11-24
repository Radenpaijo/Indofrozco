@props(['value', 'required' => false])

@php
    $text = $value ?? (string) $slot;
    $hasStar = str_ends_with(trim($text), '*');
    $hasWajib = str_contains($text, '(Wajib)');
    
    if ($hasStar) {
        $cleanText = rtrim(trim($text), '* ');
        $isRequired = true;
    } elseif ($hasWajib) {
        $cleanText = trim(str_replace('(Wajib)', '', $text));
        $isRequired = true;
    } else {
        $cleanText = $text;
        $isRequired = (bool) $required;
    }
@endphp

<label {{ $attributes->merge(['class' => 'block font-bold text-xs uppercase tracking-wider text-slate-600 mb-1']) }}>
    {!! $cleanText !!}@if($isRequired)<span class="text-rose-500 font-bold ml-1">*</span>@endif
</label>
