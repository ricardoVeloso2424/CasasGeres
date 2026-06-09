@props(['activity'])

@php
    $coverImage = $activity->coverImage();
    $cover = $coverImage?->url ?? $activity->image;
@endphp

<article class="group flex h-full flex-col overflow-hidden rounded-lg border border-stone-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
    <div class="relative aspect-[16/10] overflow-hidden bg-stone-200">
        @if ($cover)
            <img src="{{ $cover }}" alt="{{ $coverImage?->alt ?? $activity->title }}" loading="lazy" decoding="async" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
        @else
            <x-image-placeholder :title="$activity->title" class="h-full w-full transition duration-500 group-hover:scale-105" />
        @endif
        <div class="absolute left-4 top-4 flex flex-wrap gap-2">
            <span class="rounded-md bg-white px-3 py-1.5 text-sm font-semibold text-stone-900">{{ $activity->category }}</span>
            @if ($activity->is_featured)
                <span class="rounded-md bg-amber-100 px-3 py-1.5 text-sm font-semibold text-amber-950">Recomendada</span>
            @endif
        </div>
    </div>

    <div class="flex flex-1 flex-col p-6">
        <h3 class="text-2xl font-semibold leading-tight text-stone-950">{{ $activity->title }}</h3>
        <p class="mt-4 line-clamp-4 text-base leading-7 text-stone-600">{{ $activity->description }}</p>

        <div class="mt-auto flex flex-wrap gap-2 pt-6 text-base text-stone-700">
            @if ($activity->location)
                <span class="rounded-md bg-stone-50 px-3 py-2.5 ring-1 ring-stone-200">{{ $activity->location }}</span>
            @endif
            @if ($activity->distance)
                <span class="rounded-md bg-stone-50 px-3 py-2.5 ring-1 ring-stone-200">{{ $activity->distance }}</span>
            @endif
        </div>
    </div>
</article>
