@props([
    'eyebrow' => null,
    'title',
    'description' => null,
    'align' => 'left',
])

@php
    $centered = $align === 'center';
    $alignment = $centered ? 'mx-auto max-w-3xl text-center' : '';
@endphp

<div {{ $attributes->merge(['class' => $alignment]) }}>
    @if ($eyebrow)
        <p @class(['eyebrow', 'justify-center' => $centered])>
            <span class="eyebrow-line"></span>
            {{ $eyebrow }}
            @if ($centered)
                <span class="eyebrow-line"></span>
            @endif
        </p>
    @endif

    <h2 class="{{ $eyebrow ? 'mt-4' : '' }} font-display text-3xl font-semibold leading-[1.1] tracking-tight text-balance text-stone-950 sm:text-4xl lg:text-[2.75rem]">{{ $title }}</h2>

    @if ($description)
        <p class="mt-5 max-w-3xl text-base leading-8 text-pretty text-stone-600 lg:text-lg {{ $centered ? 'mx-auto' : '' }}">{{ $description }}</p>
    @endif
</div>
