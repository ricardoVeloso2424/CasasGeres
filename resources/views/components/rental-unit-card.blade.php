@props(['unit', 'house'])

@php
    $coverImage = $unit->coverImage() ?? $house->coverImage();
    $cover = $coverImage?->url;
    $nextBlocked = $unit->blockedDates->sortBy('starts_at')->first();
@endphp

<article class="group flex h-full flex-col overflow-hidden rounded-lg border border-stone-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
    <a href="{{ route('houses.units.show', [$house, $unit]) }}" class="block">
        <div class="relative aspect-[16/10] overflow-hidden bg-stone-200">
            @if ($cover)
                <img src="{{ $cover }}" alt="{{ $coverImage?->alt ?? $unit->name }}" loading="lazy" decoding="async" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
            @else
                <x-image-placeholder :title="$unit->name" class="h-full w-full transition duration-500 group-hover:scale-105" />
            @endif
            <div class="absolute left-4 top-4 flex flex-wrap gap-2">
                <span class="rounded-md bg-white px-3 py-1.5 text-sm font-semibold text-stone-900">{{ $unit->type }}</span>
                @if ($unit->featured)
                    <span class="rounded-md bg-amber-100 px-3 py-1.5 text-sm font-semibold text-amber-950">Destaque</span>
                @endif
            </div>
        </div>
    </a>

    <div class="flex flex-1 flex-col p-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h3 class="text-2xl font-semibold leading-tight text-stone-950">{{ $unit->name }}</h3>
                <p class="mt-2 text-base text-stone-600">{{ $house->name }}</p>
            </div>
            @if ($unit->base_price)
                <span class="shrink-0 rounded-md bg-emerald-50 px-3 py-2 text-sm font-semibold text-emerald-950">desde {{ number_format($unit->base_price, 0) }} EUR</span>
            @endif
        </div>

        <p class="mt-4 line-clamp-3 text-base leading-7 text-stone-600">{{ $unit->short_description }}</p>

        <div class="mt-6 grid grid-cols-3 gap-3 text-center text-sm text-stone-700 lg:text-base">
            <span class="rounded-md bg-stone-50 px-2 py-3.5 ring-1 ring-stone-200">{{ $unit->capacity }} hosp.</span>
            <span class="rounded-md bg-stone-50 px-2 py-3.5 ring-1 ring-stone-200">{{ $unit->bedrooms }} quarto(s)</span>
            <span class="rounded-md bg-stone-50 px-2 py-3.5 ring-1 ring-stone-200">{{ $unit->bathrooms }} banho(s)</span>
        </div>

        <p class="mt-5 rounded-md bg-sky-50 px-4 py-3 text-base leading-7 text-sky-950">
            {{ $nextBlocked ? 'Proximo bloqueio: ' . $nextBlocked->starts_at->format('d/m/Y') : 'Sem bloqueios futuros registados.' }}
        </p>

        <a href="{{ route('houses.units.show', [$house, $unit]) }}" class="mt-auto inline-flex w-full items-center justify-center rounded-md bg-emerald-800 px-5 py-3.5 text-base font-semibold text-white hover:bg-emerald-900">
            Ver unidade
        </a>
    </div>
</article>
