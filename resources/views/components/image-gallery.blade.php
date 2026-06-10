@props(['photos', 'fallback', 'title'])

@php
    $items = $photos->isNotEmpty()
        ? $photos->values()
        : (filled($fallback) ? collect([(object) ['url' => $fallback, 'alt' => $title]]) : collect());
    $mainPhoto = $items->first();
@endphp

@if ($items->isEmpty())
    <x-image-placeholder :title="$title" class="aspect-[16/9] w-full rounded-2xl lg:aspect-[16/8]" />
@elseif ($items->count() > 1)
    <div class="grid gap-3 lg:grid-cols-[2fr_1fr] lg:gap-4">
        <div class="group overflow-hidden rounded-2xl">
            <img src="{{ $mainPhoto->url }}" alt="{{ $mainPhoto->alt ?? $title }}" loading="lazy" decoding="async" class="aspect-[16/10] h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-105 lg:min-h-[560px]">
        </div>

        <div class="grid grid-cols-2 gap-3 lg:gap-4">
            @foreach ($items->skip(1)->take(4) as $photo)
                <div class="group overflow-hidden rounded-2xl">
                    <img src="{{ $photo->url }}" alt="{{ $photo->alt ?? $title }}" loading="lazy" decoding="async" class="aspect-square w-full object-cover transition-transform duration-700 ease-out group-hover:scale-105">
                </div>
            @endforeach
        </div>
    </div>
@else
    <div class="group overflow-hidden rounded-2xl">
        <img src="{{ $mainPhoto->url }}" alt="{{ $mainPhoto->alt ?? $title }}" loading="lazy" decoding="async" class="aspect-[16/9] w-full object-cover transition-transform duration-700 ease-out group-hover:scale-105 lg:aspect-[16/8]">
    </div>
@endif
