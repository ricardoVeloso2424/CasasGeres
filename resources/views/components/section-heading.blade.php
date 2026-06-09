@props([
    'eyebrow' => null,
    'title',
    'description' => null,
    'align' => 'left',
])

@php
    $alignment = $align === 'center' ? 'mx-auto text-center' : '';
@endphp

<div {{ $attributes->merge(['class' => $alignment]) }}>
    @if ($eyebrow)
        <p class="text-sm font-semibold uppercase tracking-[0.08em] text-emerald-800">{{ $eyebrow }}</p>
    @endif

    <h2 class="{{ $eyebrow ? 'mt-3' : '' }} text-3xl font-semibold leading-tight text-stone-950 sm:text-4xl lg:text-5xl">{{ $title }}</h2>

    @if ($description)
        <p class="mt-5 max-w-3xl text-base leading-8 text-stone-600 lg:text-lg {{ $align === 'center' ? 'mx-auto' : '' }}">{{ $description }}</p>
    @endif
</div>
