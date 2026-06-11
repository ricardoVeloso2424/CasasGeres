@props(['unit', 'house'])

@php
    $coverImage = $unit->coverImage() ?? $house->coverImage();
    $cover = $coverImage?->url;
    $nextBlocked = $unit->blockedDates->sortBy('starts_at')->first();
@endphp

<article class="card card-hover group">
    <div class="card-media aspect-[16/10]">
        @if ($cover)
            <img src="{{ $cover }}" alt="{{ $coverImage?->alt ?? $unit->name }}" loading="lazy" decoding="async" class="card-img img-reveal">
        @else
            <x-image-placeholder :title="$unit->name" class="h-full w-full transition-transform duration-700 ease-out group-hover:scale-[1.04]" />
        @endif
        <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-fir-950/45 via-transparent to-transparent"></div>
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

    <div class="flex flex-1 flex-col p-6 pb-5">
        <div class="flex items-start justify-between gap-4">
            <div class="min-w-0">
                <h3 class="font-display text-2xl font-semibold leading-tight text-stone-950 transition-colors group-hover:text-fir-800">
                    <a href="{{ route('houses.units.show', [$house, $unit]) }}" class="card-link focus:outline-none">{{ $unit->name }}</a>
                </h3>
                <p class="mt-1.5 text-base text-stone-500">{{ $house->name }}</p>
            </div>
            @if ($unit->base_price)
                <p class="shrink-0 text-right">
                    <span class="block text-xs font-medium uppercase tracking-wide text-stone-500">desde</span>
                    <span class="block font-display text-2xl font-semibold leading-tight text-fir-800">{{ number_format($unit->base_price, 0, ',', ' ') }} €</span>
                    <span class="block text-xs font-medium text-stone-500">por noite</span>
                </p>
            @endif
        </div>

        <p class="mt-4 line-clamp-2 text-base leading-7 text-stone-600">{{ $unit->short_description }}</p>

        <div class="mt-5 flex flex-wrap items-center gap-x-5 gap-y-2 text-base text-stone-700">
            <span class="inline-flex items-center gap-2">
                <svg class="h-4.5 w-4.5 text-fir-700" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="9" cy="8" r="3" stroke="currentColor" stroke-width="1.7"/><path d="M15 11a2.5 2.5 0 1 0 0-5M4 19a5 5 0 0 1 10 0M14 19a5 5 0 0 1 6-4.6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                {{ $unit->capacity }} {{ $unit->capacity === 1 ? 'hóspede' : 'hóspedes' }}
            </span>
            <span class="inline-flex items-center gap-2">
                <svg class="h-4.5 w-4.5 text-fir-700" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3 18v-5h18v5M3 13V8a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v5M3 18v2M21 18v2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                {{ $unit->bedrooms }} {{ $unit->bedrooms === 1 ? 'quarto' : 'quartos' }}
            </span>
            <span class="inline-flex items-center gap-2">
                <svg class="h-4.5 w-4.5 text-fir-700" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 12h16v3a4 4 0 0 1-4 4H8a4 4 0 0 1-4-4v-3ZM6 12V6a2 2 0 0 1 2-2c1 0 1.6.6 2 1.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                {{ $unit->bathrooms }} WC
            </span>
        </div>

        <p class="mb-5 mt-4 inline-flex items-center gap-2 text-sm leading-6 text-stone-500">
            <svg class="h-4 w-4 shrink-0 text-river-600" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="4" y="5" width="16" height="15" rx="2" stroke="currentColor" stroke-width="1.7"/><path d="M4 9h16M8 3v3M16 3v3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
            {{ $nextBlocked ? 'Próxima data ocupada: ' . $nextBlocked->starts_at->format('d/m/Y') : 'Sem datas ocupadas registadas.' }}
        </p>

        <p class="card-cta">
            Ver unidade
            <svg class="h-4 w-4 transition-transform duration-200 group-hover:translate-x-1" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M4 10h12m0 0-4.5-4.5M16 10l-4.5 4.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </p>
    </div>
</article>
