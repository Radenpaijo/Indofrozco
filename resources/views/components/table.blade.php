<div class="relative w-full overflow-x-auto rounded-lg border border-slate-300 shadow-sm">
    <table {{ $attributes->merge(['class' => 'w-full min-w-[650px] text-left border-collapse text-sm']) }}>
        {{ $slot }}
    </table>
</div>