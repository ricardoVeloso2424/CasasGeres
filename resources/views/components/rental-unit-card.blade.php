@props(['unit', 'house'])

@php
    $coverImage = $unit->coverImage() ?? $house->coverImage();
    $cover = $coverImage?->url;
    $nextBlocked = $unit->blockedDates->sortBy('starts_at')->first();
@endphp

<article class="card card-hover group">
    <a href="{{ route('houses.units.show', [$house, $unit]) }}" class="block focus:outline-none" aria-label="{{ $unit->name }}">
        <div class="card-media aspect-[16/10]">
            @if ($cover)
                <img src="{{ $cover }}" alt="{{ $coverImage?->alt ?? $unit->name }}" loading="lazy" decoding="async" class="card-img">
            @else
                <x-image-placeholder :title="$unit->name" class="h-full w-full transition-transform duration-700 ease-out group-hover:scale-105" />
            @endif
            <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-stone-950/45 via-transparent to-transparent"></div>
            <div class="absolute left-4 top-4 flex flex-wrap gap-2">
                <span class="chip chip-light">{{ $unit->type }}</span>
                @if ($unit->featured)
                    <span class="chip chip-amber">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="m10 2 2.4 4.9 5.4.8-3.9 3.8.9 5.3L10 14.3 5.2 16.8l.9-5.3L2.2 7.7l5.4-.8L10 2Z"/></svg>
                        Destaque
                    </span>
                @endif
            </div>
        </div>
    </a>

    <div class="flex flex-1 flex-col p-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h3 class="font-display text-2xl font-semibold leading-tight text-stone-950 transition-colors group-hover:text-emerald-800">{{ $unit->name }}</h3>
                <p class="mt-1.5 text-base text-stone-500">{{ $house->name }}</p>
            </div>
            @if ($unit->base_price)
                <span class="shrink-0 chip chip-emerald">desde {{ number_format($unit->base_price, 0) }} EUR</span>
            @endif
        </div>

        <p class="mt-4 line-clamp-3 text-base leading-7 text-stone-600">{{ $unit->short_description }}</p>

        <div class="mt-6 grid grid-cols-3 gap-2.5 text-center">
            <div class="rounded-xl bg-stone-50 px-2 py-3 ring-1 ring-inset ring-stone-200">
                <p class="text-lg font-semibold text-stone-950">{{ $unit->capacity }}</p>
                <p class="text-xs font-medium uppercase tracking-wide text-stone-500">hospedes</p>
            </div>
            <div class="rounded-xl bg-stone-50 px-2 py-3 ring-1 ring-inset ring-stone-200">
                <p class="text-lg font-semibold text-stone-950">{{ $unit->bedrooms }}</p>
                <p class="text-xs font-medium uppercase tracking-wide text-stone-500">quarto(s)</p>
            </div>
            <div class="rounded-xl bg-stone-50 px-2 py-3 ring-1 ring-inset ring-stone-200">
                <p class="text-lg font-semibold text-stone-950">{{ $unit->bathrooms }}</p>
                <p class="text-xs font-medium uppercase tracking-wide text-stone-500">banho(s)</p>
            </div>
        </div>

        <p class="mt-5 inline-flex items-center gap-2 rounded-xl bg-sky-50 px-4 py-3 text-sm leading-6 text-sky-900 ring-1 ring-inset ring-sky-100">
            <svg class="h-4 w-4 shrink-0 text-sky-600" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="4" y="5" width="16" height="15" rx="2" stroke="currentColor" stroke-width="1.7"/><path d="M4 9h16M8 3v3M16 3v3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
            {{ $nextBlocked ? 'Proximo bloqueio: ' . $nextBlocked->starts_at->format('d/m/Y') : 'Sem bloqueios futuros registados.' }}
        </p>

        <a href="{{ route('houses.units.show', [$house, $unit]) }}" class="btn btn-primary btn-block mt-6">
            Ver unidade
            <svg class="h-4 w-4 transition-transform duration-200 group-hover:translate-x-1" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M4 10h12m0 0-4.5-4.5M16 10l-4.5 4.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </a>
    </div>
</article>
