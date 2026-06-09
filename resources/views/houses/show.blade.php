@extends('layouts.app')

@section('content')
    @php
        $coverImage = $house->coverImage();
        $cover = $coverImage?->url;
        $unitCount = $house->rentalUnits->count();
        $maxCapacity = $house->rentalUnits->max('capacity');
    @endphp

    <section class="relative min-h-[500px] overflow-hidden bg-stone-950 text-white lg:min-h-[620px]">
        @if ($cover)
            <img src="{{ $cover }}" alt="{{ $coverImage?->alt ?? $house->name }}" fetchpriority="high" decoding="async" class="absolute inset-0 h-full w-full object-cover opacity-60">
        @else
            <x-image-placeholder :title="$house->name" class="absolute inset-0 h-full w-full opacity-60" />
        @endif
        <div class="absolute inset-0 bg-gradient-to-r from-stone-950 via-stone-950/70 to-stone-950/20"></div>

        <div class="relative mx-auto max-w-screen-2xl px-4 py-24 sm:px-6 lg:px-10 lg:py-32">
            <a href="{{ route('houses.index') }}" class="text-base font-semibold text-emerald-100 hover:text-white">Casas</a>
            <p class="mt-6 text-sm font-semibold uppercase tracking-[0.08em] text-emerald-100 lg:text-base">{{ $house->location }}</p>
            <h1 class="mt-4 max-w-5xl text-4xl font-semibold leading-tight sm:text-5xl lg:text-6xl">{{ $house->name }}</h1>
            <p class="mt-6 max-w-3xl text-base leading-8 text-stone-100 sm:text-lg lg:text-xl lg:leading-9">{{ $house->short_description }}</p>

            <div class="mt-10 grid max-w-3xl gap-4 sm:grid-cols-3">
                <div class="rounded-lg bg-white/10 p-5 ring-1 ring-white/20 backdrop-blur">
                    <p class="text-3xl font-semibold">{{ $unitCount }}</p>
                    <p class="mt-2 text-base text-stone-100">{{ $unitCount === 1 ? 'unidade' : 'unidades' }}</p>
                </div>
                <div class="rounded-lg bg-white/10 p-5 ring-1 ring-white/20 backdrop-blur">
                    <p class="text-3xl font-semibold">{{ $maxCapacity }}</p>
                    <p class="mt-2 text-base text-stone-100">hospedes max.</p>
                </div>
                <div class="rounded-lg bg-white/10 p-5 ring-1 ring-white/20 backdrop-blur">
                    <p class="text-3xl font-semibold">Direto</p>
                    <p class="mt-2 text-base text-stone-100">reserva por contacto</p>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white">
        <div class="mx-auto max-w-screen-2xl px-4 py-16 sm:px-6 lg:px-10 lg:py-24">
            <x-image-gallery :photos="$house->photos" :fallback="$cover" :title="$house->name" />

            <div class="mt-14 grid gap-10 lg:grid-cols-[1.1fr_0.9fr] lg:gap-14">
                <div>
                    <x-section-heading
                        eyebrow="Sobre a casa"
                        :title="$house->name"
                        :description="$house->description"
                    />
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    @foreach ([
                        ['title' => 'Estadias flexiveis', 'text' => 'Reserve a unidade que corresponde ao tamanho do grupo.'],
                        ['title' => 'Base para explorar', 'text' => 'Boa ligacao a trilhos, cascatas e miradouros.'],
                        ['title' => 'Privacidade', 'text' => 'Unidades independentes para estadias tranquilas.'],
                        ['title' => 'Confirmacao direta', 'text' => 'Datas e condicoes validadas por contacto.'],
                    ] as $item)
                        <div class="rounded-lg border border-stone-200 bg-stone-50 p-6">
                            <p class="text-lg font-semibold text-stone-950">{{ $item['title'] }}</p>
                            <p class="mt-3 text-base leading-7 text-stone-600">{{ $item['text'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="bg-stone-50">
        <div class="mx-auto max-w-screen-2xl px-4 py-16 sm:px-6 lg:px-10 lg:py-24">
            <div class="flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
                <x-section-heading
                    eyebrow="Unidades"
                    title="Unidades alugaveis nesta casa"
                    description="Veja capacidade, comodidades e bloqueios futuros antes de pedir a reserva."
                />
                <a href="{{ route('activities.index') }}" class="inline-flex rounded-md border border-stone-300 px-5 py-3.5 text-base font-semibold text-stone-800 hover:border-emerald-700 hover:text-emerald-800">Ver atividades perto</a>
            </div>

            <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3 lg:gap-8">
                @forelse ($house->rentalUnits as $unit)
                    <x-rental-unit-card :unit="$unit" :house="$house" />
                @empty
                    <p class="rounded-lg border border-stone-200 bg-white p-6 text-sm text-stone-600">Esta casa ainda nao tem unidades ativas.</p>
                @endforelse
            </div>
        </div>
    </section>

    <x-contact-cta />
@endsection
