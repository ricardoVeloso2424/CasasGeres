@props(['house'])

@php
    $coverImage = $house->coverImage();
    $cover = $coverImage?->url;
    $unitCount = $house->rental_units_count ?? $house->rentalUnits->count();
    $maxCapacity = $house->rentalUnits->max('capacity');
    $unitTypes = $house->rentalUnits->pluck('type')->filter()->unique()->sort()->values();
@endphp

<article class="card card-hover group">
    <div class="card-media aspect-[4/3]">
        @if ($cover)
            <img src="{{ $cover }}" alt="{{ $coverImage?->alt ?? $house->name }}" loading="lazy" decoding="async" class="card-img img-reveal">
        @else
            <x-image-placeholder :title="$house->name" class="h-full w-full transition-transform duration-700 ease-out group-hover:scale-[1.04]" />
        @endif
        <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-fir-950/55 via-transparent to-transparent"></div>
        <span class="chip chip-light absolute bottom-4 left-4">{{ $unitCount }} {{ $unitCount === 1 ? 'unidade' : 'unidades' }}</span>
        @if ($house->featured)
            <span class="chip chip-amber absolute left-4 top-4">
                <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="m10 2 2.4 4.9 5.4.8-3.9 3.8.9 5.3L10 14.3 5.2 16.8l.9-5.3L2.2 7.7l5.4-.8L10 2Z"/></svg>
                Destaque
            </span>
        @endif
    </div>

    <div class="flex flex-1 flex-col p-6 pb-5">
        <p class="inline-flex items-center gap-1.5 text-sm font-semibold uppercase tracking-[0.1em] text-fir-700">
            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 21s7-5.6 7-11a7 7 0 1 0-14 0c0 5.4 7 11 7 11Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><circle cx="12" cy="10" r="2.2" stroke="currentColor" stroke-width="1.8"/></svg>
            {{ $house->location }}
        </p>

        <h3 class="mt-3 font-display text-2xl font-semibold leading-tight text-stone-950 transition-colors group-hover:text-fir-800">
            <a href="{{ route('houses.show', $house) }}" class="card-link focus:outline-none">{{ $house->name }}</a>
        </h3>

        <p class="mt-3 line-clamp-2 text-base leading-7 text-stone-600">{{ $house->short_description }}</p>

        <div class="mb-5 mt-5 flex flex-wrap gap-2">
            <span class="chip chip-muted">
                <svg class="h-4 w-4 text-fir-700" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="9" cy="8" r="3" stroke="currentColor" stroke-width="1.7"/><path d="M15 11a2.5 2.5 0 1 0 0-5M4 19a5 5 0 0 1 10 0M14 19a5 5 0 0 1 6-4.6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                Até {{ $maxCapacity }} hóspedes
            </span>
            @foreach ($unitTypes as $type)
                <span class="chip chip-muted">{{ $type }}</span>
            @endforeach
        </div>

        <p class="card-cta">
            Ver casa
            <svg class="h-4 w-4 transition-transform duration-200 group-hover:translate-x-1" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M4 10h12m0 0-4.5-4.5M16 10l-4.5 4.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </p>
    </div>
</article>
