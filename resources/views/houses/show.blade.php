@extends('layouts.app')

@section('content')
    @php
        $coverImage = $house->coverImage();
        $cover = $coverImage?->url;
        $unitCount = $house->rentalUnits->count();
        $maxCapacity = $house->rentalUnits->max('capacity');
    @endphp

    <section class="relative isolate flex min-h-[480px] items-center overflow-hidden bg-stone-950 text-white lg:min-h-[600px]">
        @if ($cover)
            <img src="{{ $cover }}" alt="{{ $coverImage?->alt ?? $house->name }}" fetchpriority="high" decoding="async" class="absolute inset-0 -z-10 h-full w-full object-cover">
        @else
            <x-image-placeholder :title="$house->name" class="absolute inset-0 -z-10 h-full w-full" />
        @endif
        <div class="absolute inset-0 -z-10 bg-gradient-to-br from-stone-950/95 via-stone-950/75 to-stone-950/35"></div>
        <div class="absolute inset-0 -z-10 bg-gradient-to-t from-stone-950/85 via-transparent to-transparent"></div>

        <div class="mx-auto w-full max-w-screen-2xl px-4 py-20 sm:px-6 lg:px-10 lg:py-24">
            <a href="{{ route('houses.index') }}" class="animate-rise d1 inline-flex items-center gap-2 rounded-full bg-white/10 px-3.5 py-1.5 text-sm font-semibold text-white ring-1 ring-inset ring-white/25 backdrop-blur transition hover:bg-white/20">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M16 10H4m0 0 4.5-4.5M4 10l4.5 4.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Casas
            </a>
            <p class="animate-rise d2 mt-6 inline-flex items-center gap-1.5 text-sm font-semibold uppercase tracking-[0.12em] text-emerald-200 lg:text-base">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 21s7-5.6 7-11a7 7 0 1 0-14 0c0 5.4 7 11 7 11Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><circle cx="12" cy="10" r="2.2" stroke="currentColor" stroke-width="1.7"/></svg>
                {{ $house->location }}
            </p>
            <h1 class="animate-rise d2 mt-3 max-w-4xl font-display text-4xl font-semibold leading-[1.05] tracking-tight text-balance sm:text-5xl lg:text-6xl">{{ $house->name }}</h1>
            <p class="animate-rise d3 mt-6 max-w-2xl text-base leading-8 text-stone-200 sm:text-lg lg:text-xl lg:leading-9">{{ $house->short_description }}</p>

            <div class="animate-rise d4 mt-10 grid max-w-2xl gap-4 sm:grid-cols-3">
                <div class="rounded-2xl bg-white/10 p-5 ring-1 ring-inset ring-white/20 backdrop-blur">
                    <p class="font-display text-3xl font-semibold">{{ $unitCount }}</p>
                    <p class="mt-1.5 text-base text-stone-200">{{ $unitCount === 1 ? 'unidade' : 'unidades' }}</p>
                </div>
                <div class="rounded-2xl bg-white/10 p-5 ring-1 ring-inset ring-white/20 backdrop-blur">
                    <p class="font-display text-3xl font-semibold">{{ $maxCapacity }}</p>
                    <p class="mt-1.5 text-base text-stone-200">hospedes max.</p>
                </div>
                <div class="rounded-2xl bg-white/10 p-5 ring-1 ring-inset ring-white/20 backdrop-blur">
                    <p class="font-display text-3xl font-semibold">Direto</p>
                    <p class="mt-1.5 text-base text-stone-200">reserva por contacto</p>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white">
        <div class="mx-auto max-w-screen-2xl px-4 py-16 sm:px-6 lg:px-10 lg:py-24">
            <div class="reveal">
                <x-image-gallery :photos="$house->photos" :fallback="$cover" :title="$house->name" />
            </div>

            <div class="mt-14 grid gap-10 lg:grid-cols-[1.1fr_0.9fr] lg:gap-14">
                <div class="reveal">
                    <x-section-heading
                        eyebrow="Sobre a casa"
                        :title="$house->name"
                        :description="$house->description"
                    />
                </div>

                <div class="reveal reveal-d1 grid gap-4 sm:grid-cols-2">
                    @php
                        $features = [
                            ['title' => 'Estadias flexiveis', 'text' => 'Reserve a unidade que corresponde ao tamanho do grupo.', 'icon' => 'M4 7h16M4 12h16M4 17h10'],
                            ['title' => 'Base para explorar', 'text' => 'Boa ligacao a trilhos, cascatas e miradouros.', 'icon' => 'm3 18 5-7 4 5 3-4 6 6M3 18h18'],
                            ['title' => 'Privacidade', 'text' => 'Unidades independentes para estadias tranquilas.', 'icon' => 'M6 11V8a6 6 0 0 1 12 0v3M5 11h14v9H5z'],
                            ['title' => 'Confirmacao direta', 'text' => 'Datas e condicoes validadas por contacto.', 'icon' => 'm4 12.5 4.5 4.5L20 6'],
                        ];
                    @endphp
                    @foreach ($features as $item)
                        <div class="rounded-2xl border border-stone-200/80 bg-stone-50/60 p-6 transition-colors hover:border-emerald-200 hover:bg-white">
                            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-700/10 text-emerald-700 ring-1 ring-inset ring-emerald-700/15">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="{{ $item['icon'] }}" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </span>
                            <p class="mt-4 text-lg font-semibold text-stone-950">{{ $item['title'] }}</p>
                            <p class="mt-2 text-base leading-7 text-stone-600">{{ $item['text'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="bg-stone-50">
        <div class="mx-auto max-w-screen-2xl px-4 py-16 sm:px-6 lg:px-10 lg:py-24">
            <div class="reveal flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
                <x-section-heading
                    eyebrow="Unidades"
                    title="Unidades alugaveis nesta casa"
                    description="Veja capacidade, comodidades e bloqueios futuros antes de pedir a reserva."
                />
                <a href="{{ route('activities.index') }}" class="btn btn-sm btn-outline shrink-0">
                    Ver atividades perto
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M4 10h12m0 0-4.5-4.5M16 10l-4.5 4.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
            </div>

            <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3 lg:gap-8">
                @forelse ($house->rentalUnits as $i => $unit)
                    <div class="reveal reveal-d{{ min($i + 1, 3) }} h-full">
                        <x-rental-unit-card :unit="$unit" :house="$house" />
                    </div>
                @empty
                    <p class="note">Esta casa ainda nao tem unidades ativas.</p>
                @endforelse
            </div>
        </div>
    </section>

    <x-contact-cta />
@endsection
