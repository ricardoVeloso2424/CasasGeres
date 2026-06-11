@props(['photos', 'fallback', 'title'])

@php
    $items = $photos->isNotEmpty()
        ? $photos->values()
        : (filled($fallback) ? collect([(object) ['url' => $fallback, 'alt' => $title]]) : collect());
    $count = $items->count();
    $mainPhoto = $items->first();
    $sidePhotos = $items->slice(1)->take(4)->values();
@endphp

@if ($count === 0)
    <x-image-placeholder :title="$title" class="aspect-[16/9] w-full rounded-2xl lg:aspect-[21/9]" />
@elseif ($count === 1)
    <div class="group overflow-hidden rounded-2xl bg-sand-100">
        <img src="{{ $mainPhoto->url }}" alt="{{ $mainPhoto->alt ?? $title }}" loading="lazy" decoding="async" class="img-reveal aspect-[16/9] w-full object-cover transition-transform duration-700 ease-out group-hover:scale-[1.03] lg:aspect-[21/9]">
    </div>
@elseif ($count === 2)
    <div class="grid gap-3 sm:grid-cols-2 lg:gap-4">
        @foreach ($items as $photo)
            <div class="group overflow-hidden rounded-2xl bg-sand-100">
                <img src="{{ $photo->url }}" alt="{{ $photo->alt ?? $title }}" loading="lazy" decoding="async" class="img-reveal aspect-[4/3] w-full object-cover transition-transform duration-700 ease-out group-hover:scale-[1.03]">
            </div>
        @endforeach
    </div>
@elseif ($count === 3)
    <div class="grid gap-3 lg:grid-cols-[2fr_1fr] lg:gap-4">
        <div class="group overflow-hidden rounded-2xl bg-sand-100">
            <img src="{{ $mainPhoto->url }}" alt="{{ $mainPhoto->alt ?? $title }}" loading="lazy" decoding="async" class="img-reveal aspect-[16/10] h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-[1.03]">
        </div>
        <div class="grid grid-cols-2 gap-3 lg:grid-cols-1 lg:grid-rows-2 lg:gap-4">
            @foreach ($sidePhotos as $photo)
                <div class="group overflow-hidden rounded-2xl bg-sand-100">
                    <img src="{{ $photo->url }}" alt="{{ $photo->alt ?? $title }}" loading="lazy" decoding="async" class="img-reveal aspect-[16/10] w-full object-cover transition-transform duration-700 ease-out group-hover:scale-[1.03] lg:aspect-auto lg:h-full">
                </div>
            @endforeach
        </div>
    </div>
@else
    <div class="grid gap-3 lg:grid-cols-[2fr_1fr] lg:gap-4">
        <div class="group overflow-hidden rounded-2xl bg-sand-100">
            <img src="{{ $mainPhoto->url }}" alt="{{ $mainPhoto->alt ?? $title }}" loading="lazy" decoding="async" class="img-reveal aspect-[16/10] h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-[1.03] lg:min-h-[520px]">
        </div>
        <div class="grid grid-cols-2 gap-3 lg:gap-4">
            @foreach ($sidePhotos as $photo)
                <div @class(['group overflow-hidden rounded-2xl bg-sand-100', 'col-span-2' => $sidePhotos->count() === 3 && $loop->first])>
                    <img src="{{ $photo->url }}" alt="{{ $photo->alt ?? $title }}" loading="lazy" decoding="async" @class(['img-reveal w-full object-cover transition-transform duration-700 ease-out group-hover:scale-[1.03]', 'aspect-[2/1]' => $sidePhotos->count() === 3 && $loop->first, 'aspect-square' => ! ($sidePhotos->count() === 3 && $loop->first)])>
                </div>
            @endforeach
        </div>
    </div>
@endif
