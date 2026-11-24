@props(['href' => null, 'label' => 'Kembali', 'withLabel' => false, 'variant' => 'button'])

@php
    if ($href) {
        $targetUrl = $href;
    } else {
        $previousUrl = url()->previous();
        $currentUrl = url()->current();

        $prevPath = trim(parse_url($previousUrl, PHP_URL_PATH) ?? '', '/');
        $currPath = trim(parse_url($currentUrl, PHP_URL_PATH) ?? '', '/');

        // Deteksi parent resource path (misal 'barangs/create' -> 'barangs')
        $segments = array_values(array_filter(explode('/', $currPath)));
        $parentPath = count($segments) > 1 ? $segments[0] : null;
        $fallbackUrl = $parentPath ? url($parentPath) : route('dashboard');

        // Kondisi looping yang harus dicegah:
        // 1. Previous URL kosong
        // 2. Previous path sama dengan current path (misal refresh, filter pencarian, query params)
        // 3. Previous path adalah sub-halaman/child dari current path (misal di /barangs tapi previous /barangs/create atau /barangs/1)
        // 4. Previous path adalah halaman auth (login, register, forgot-password)
        $isAuthPage = $prevPath && (str_contains($prevPath, 'login') || str_contains($prevPath, 'register') || str_contains($prevPath, 'password'));
        $isSamePath = ($prevPath === $currPath);
        $isChildPage = $prevPath && str_starts_with($prevPath, $currPath . '/');

        if (empty($previousUrl) || $isSamePath || $isChildPage || $isAuthPage) {
            $targetUrl = $fallbackUrl;
        } else {
            $targetUrl = $previousUrl;
        }
    }

    $tooltipText = str_contains($label, 'Esc') ? $label : "{$label} (Esc)";
@endphp

<a href="{{ $targetUrl }}" 
   title="{{ $tooltipText }}"
   aria-label="{{ $tooltipText }}"
   data-back-button="true"
   {{ $attributes->merge(['class' => 'group inline-flex items-center justify-center p-1 sm:p-1.5 -ml-1 text-slate-800 hover:text-indigo-600 active:text-indigo-800 transition-colors shrink-0 cursor-pointer focus:outline-none select-none']) }}>
    <!-- Tanda Panah Bersih Tanpa Kotak (Proporsional & Seimbang dengan Huruf Judul) -->
    <svg class="w-6 h-6 sm:w-7 sm:h-7 transition-transform duration-150 group-hover:-translate-x-1 pointer-events-none" 
         fill="none" 
         viewBox="0 0 24 24" 
         stroke="currentColor" 
         stroke-width="2.6" 
         stroke-linecap="round" 
         stroke-linejoin="round">
        <path d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
    </svg>
    @if($withLabel || !$slot->isEmpty())
        <span class="text-sm font-semibold ml-2">{{ $slot->isEmpty() ? $label : $slot }}</span>
    @endif
</a>
