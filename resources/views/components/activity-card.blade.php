@props(['activity'])

@php
    $coverImage = $activity->coverImage();
    $cover = $coverImage?->url ?? $activity->image;
@endphp

<article class="card card-hover group">
    <div class="card-media aspect-[16/10]">
        @if ($cover)
            <img src="{{ $cover }}" alt="{{ $coverImage?->alt ?? $activity->title }}" loading="lazy" decoding="async" class="card-img">
        @else
            <x-image-placeholder :title="$activity->title" class="h-full w-full transition-transform duration-700 ease-out group-hover:scale-105" />
        @endif
        <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-stone-950/45 via-transparent to-transparent"></div>
        <div class="absolute left-4 top-4 flex flex-wrap gap-2">
            <span class="chip chip-light">{{ $activity->category }}</span>
            @if ($activity->is_featured)
                <span class="chip chip-amber">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="m10 2 2.4 4.9 5.4.8-3.9 3.8.9 5.3L10 14.3 5.2 16.8l.9-5.3L2.2 7.7l5.4-.8L10 2Z"/></svg>
                    Recomendada
                </span>
            @endif
        </div>
    </div>

    <div class="flex flex-1 flex-col p-6">
        <h3 class="font-display text-2xl font-semibold leading-tight text-stone-950">{{ $activity->title }}</h3>
        <p class="mt-3 line-clamp-4 text-base leading-7 text-stone-600">{{ $activity->description }}</p>

        @if ($activity->location || $activity->distance)
            <div class="mt-auto flex flex-wrap gap-2 pt-6">
                @if ($activity->location)
                    <span class="chip chip-muted">
                        <svg class="h-4 w-4 text-emerald-700" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 21s7-5.6 7-11a7 7 0 1 0-14 0c0 5.4 7 11 7 11Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><circle cx="12" cy="10" r="2.2" stroke="currentColor" stroke-width="1.7"/></svg>
                        {{ $activity->location }}
                    </span>
                @endif
                @if ($activity->distance)
                    <span class="chip chip-muted">
                        <svg class="h-4 w-4 text-emerald-700" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3v3m0 12v3m9-9h-3M6 12H3m13.5-6.5-2 2m-7 7-2 2m11 0-2-2m-7-7-2-2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                        {{ $activity->distance }}
                    </span>
                @endif
            </div>
        @endif
    </div>
</article>
