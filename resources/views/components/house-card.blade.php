@props(['house'])

@php
    $coverImage = $house->coverImage();
    $cover = $coverImage?->url;
    $unitCount = $house->rental_units_count ?? $house->rentalUnits->count();
    $maxCapacity = $house->rentalUnits->max('capacity');
@endphp

<article class="group flex h-full flex-col overflow-hidden rounded-lg border border-stone-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
    <a href="{{ route('houses.show', $house) }}" class="block">
        <div class="relative aspect-[16/10] overflow-hidden bg-stone-200">
            @if ($cover)
                <img src="{{ $cover }}" alt="{{ $coverImage?->alt ?? $house->name }}" loading="lazy" decoding="async" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
            @else
                <x-image-placeholder :title="$house->name" class="h-full w-full transition duration-500 group-hover:scale-105" />
            @endif
            <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-stone-950/70 to-transparent p-4">
                <span class="rounded-md bg-white px-3 py-1.5 text-sm font-semibold text-stone-900">{{ $unitCount }} {{ $unitCount === 1 ? 'unidade' : 'unidades' }}</span>
            </div>
            @if ($house->featured)
                <span class="absolute left-4 top-4 rounded-md bg-amber-100 px-3 py-1.5 text-sm font-semibold text-amber-950">Destaque</span>
            @endif
        </div>
    </a>

    <div class="flex flex-1 flex-col p-6">
        <p class="text-sm font-semibold uppercase tracking-[0.08em] text-emerald-800">{{ $house->location }}</p>
        <h3 class="mt-3 text-2xl font-semibold leading-tight text-stone-950">{{ $house->name }}</h3>
        <p class="mt-4 line-clamp-3 text-base leading-7 text-stone-600">{{ $house->short_description }}</p>

        <div class="mt-6 grid grid-cols-2 gap-3 text-base text-stone-700">
            <span class="rounded-md bg-stone-50 px-3 py-3 ring-1 ring-stone-200">Ate {{ $maxCapacity }} hospedes</span>
            <span class="rounded-md bg-stone-50 px-3 py-3 ring-1 ring-stone-200">Reserva direta</span>
        </div>

        <a href="{{ route('houses.show', $house) }}" class="mt-auto inline-flex w-full items-center justify-center rounded-md bg-emerald-800 px-5 py-3.5 text-base font-semibold text-white hover:bg-emerald-900">
            Ver detalhes
        </a>
    </div>
</article>
