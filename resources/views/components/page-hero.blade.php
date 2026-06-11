@props([
    'image' => null,
    'imageAlt' => '',
    'srcset' => null,
    'sizes' => null,
    'eyebrow' => null,
    'title' => null,
    'lead' => null,
    'size' => 'md',
    'dividerTo' => 'white',
])

@php
    $minHeight = [
        'lg' => 'min-h-[600px] md:min-h-[680px] lg:min-h-[780px]',
        'md' => 'min-h-[480px] lg:min-h-[580px]',
        'sm' => 'min-h-[400px] lg:min-h-[470px]',
    ][$size] ?? 'min-h-[480px] lg:min-h-[580px]';

    $titleSize = [
        'lg' => 'text-4xl sm:text-5xl lg:text-6xl xl:text-7xl',
        'md' => 'text-4xl sm:text-5xl lg:text-6xl',
        'sm' => 'text-3xl sm:text-4xl lg:text-5xl',
    ][$size] ?? 'text-4xl sm:text-5xl lg:text-6xl';

    $dividerColor = $dividerTo === 'sand' ? 'text-sand-50' : 'text-white';
    $hasAbove = $eyebrow || isset($badges) || isset($top);
@endphp

<section class="relative isolate flex {{ $minHeight }} items-center overflow-hidden bg-fir-950 text-white">
    @if ($image)
        <img
            src="{{ $image }}"
            @if ($srcset) srcset="{{ $srcset }}" sizes="{{ $sizes ?? '100vw' }}" @endif
            alt="{{ $imageAlt }}"
            fetchpriority="high"
            decoding="async"
            class="hero-img absolute inset-0 -z-10 h-full w-full object-cover"
        >
    @else
        <div class="absolute inset-0 -z-10 bg-gradient-to-br from-fir-700 via-fir-900 to-fir-950" aria-hidden="true"></div>
    @endif
    <div class="absolute inset-0 -z-10 bg-gradient-to-br from-fir-950/90 via-fir-950/55 to-fir-950/20"></div>
    <div class="absolute inset-0 -z-10 bg-gradient-to-t from-fir-950/85 via-fir-950/15 to-transparent"></div>
    <div class="hero-vignette -z-10" aria-hidden="true"></div>
    <div class="texture-grain -z-10" aria-hidden="true"></div>

    <div class="mx-auto w-full max-w-screen-2xl px-4 pb-24 pt-16 sm:px-6 sm:pb-28 sm:pt-20 lg:px-10">
        @isset($top)
            <div class="animate-rise d1">{{ $top }}</div>
        @endisset

        @if ($eyebrow)
            <p class="animate-rise d1 eyebrow {{ isset($top) ? 'mt-6' : '' }} text-amber-200"><span class="eyebrow-line bg-amber-300/60"></span>{{ $eyebrow }}</p>
        @endif

        @isset($badges)
            <div class="animate-rise d1 {{ $eyebrow || isset($top) ? 'mt-6' : '' }}">{{ $badges }}</div>
        @endisset

        <h1 class="animate-rise d2 {{ $hasAbove ? 'mt-4' : '' }} max-w-4xl font-display {{ $titleSize }} font-semibold leading-[1.04] tracking-tight text-balance drop-shadow-[0_2px_18px_rgba(20,31,23,0.35)]">@isset($heading){{ $heading }}@else{{ $title }}@endisset</h1>

        @if ($lead)
            <p class="animate-rise d3 mt-6 max-w-2xl text-base leading-8 text-sand-100/90 sm:text-lg lg:text-xl lg:leading-9">{{ $lead }}</p>
        @endif

        @if (trim($slot) !== '')
            <div class="animate-rise d4 mt-9">{{ $slot }}</div>
        @endif
    </div>

    <div class="pointer-events-none absolute inset-x-0 bottom-0 -mb-px" aria-hidden="true">
        <svg class="h-10 w-full sm:h-14 lg:h-16 {{ $dividerColor }}" viewBox="0 0 1440 110" preserveAspectRatio="none" fill="currentColor">
            <path opacity="0.35" d="M0 110V58l90-20 80 18 90-32 80 28 90-22 90 28 90-36 90 28 90-16 90 24 90-12 90 20 90-14 90 18 90-10v50H0Z"/>
            <path d="M0 110V82l110-24 110 18 110-28 110 24 110-20 110 26 110-28 110 24 110-18 110 22 110-16 110 20 120-14v34H0Z"/>
        </svg>
    </div>
</section>
