@props(['photos', 'fallback', 'title'])

@php
    $items = $photos->isNotEmpty()
        ? $photos->values()
        : (filled($fallback) ? collect([(object) ['url' => $fallback, 'alt' => $title]]) : collect());
    $mainPhoto = $items->first();
@endphp

@if ($items->isEmpty())
    <x-image-placeholder :title="$title" class="aspect-[16/9] w-full rounded-lg lg:aspect-[16/8]" />
@elseif ($items->count() > 1)
    <div class="grid gap-4 lg:grid-cols-[2fr_1fr] lg:gap-5">
        <img src="{{ $mainPhoto->url }}" alt="{{ $mainPhoto->alt ?? $title }}" loading="lazy" decoding="async" class="aspect-[16/10] h-full w-full rounded-lg object-cover lg:min-h-[560px]">

        <div class="grid grid-cols-2 gap-4 lg:gap-5">
            @foreach ($items->skip(1)->take(4) as $photo)
                <img src="{{ $photo->url }}" alt="{{ $photo->alt ?? $title }}" loading="lazy" decoding="async" class="aspect-square w-full rounded-lg object-cover">
            @endforeach
        </div>
    </div>
@else
    <img src="{{ $mainPhoto->url }}" alt="{{ $mainPhoto->alt ?? $title }}" loading="lazy" decoding="async" class="aspect-[16/9] w-full rounded-lg object-cover lg:aspect-[16/8]">
@endif
