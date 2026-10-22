<div class="relative w-full overflow-auto rounded-lg border border-slate-300 shadow-sm">
    <table {{ $attributes->merge(['class' => 'w-full text-left border-collapse text-sm']) }}>
        {{ $slot }}
    </table>
</div>