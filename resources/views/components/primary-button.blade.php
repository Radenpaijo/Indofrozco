<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-4 py-2 bg-[#0071e3] hover:bg-[#0077ed] active:scale-[0.97] border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-wider shadow-sm transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-blue-500/40 select-none cursor-pointer']) }}>
    {{ $slot }}
</button>
