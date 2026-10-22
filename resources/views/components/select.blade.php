@props([
    'disabled' => false,
])

<select {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['class' => 'flex h-9 w-full rounded-md border border-slate-300 bg-white px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-slate-950 disabled:cursor-not-allowed disabled:opacity-50']) }}>
    {{ $slot }}
</select>