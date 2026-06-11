@extends('layouts.app')

@section('content')
    @php
        $coverImage = $house->coverImage();
        $cover = $coverImage?->url;
        $unitCount = $house->rentalUnits->count();
        $maxCapacity = $house->rentalUnits->max('capacity');
    @endphp

    <x-page-hero
        size="md"
        :image="$cover"
        :image-alt="$coverImage?->alt ?? $house->name"
        :title="$house->name"
        :lead="$house->short_description"
        divider-to="white"
    >
        <x-slot:top>
            <a href="{{ route('houses.index') }}" class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3.5 py-1.5 text-sm font-semibold text-white ring-1 ring-inset ring-white/25 backdrop-blur transition hover:bg-white/20">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M16 10H4m0 0 4.5-4.5M4 10l4.5 4.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Casas
            </a>
        </x-slot:top>

        <x-slot:badges>
            <p class="inline-flex items-center gap-1.5 text-sm font-semibold uppercase tracking-[0.12em] text-amber-200 lg:text-base">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 21s7-5.6 7-11a7 7 0 1 0-14 0c0 5.4 7 11 7 11Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><circle cx="12" cy="10" r="2.2" stroke="currentColor" stroke-width="1.7"/></svg>
                {{ $house->location }}
            </p>
        </x-slot:badges>

        <div class="grid max-w-2xl gap-4 sm:grid-cols-3">
            <div class="rounded-2xl bg-white/10 p-5 ring-1 ring-inset ring-white/20 backdrop-blur">
                <p class="font-display text-3xl font-semibold">{{ $unitCount }}</p>
                <p class="mt-1.5 text-base text-sand-100/85">{{ $unitCount === 1 ? 'unidade' : 'unidades' }}</p>
            </div>
            <div class="rounded-2xl bg-white/10 p-5 ring-1 ring-inset ring-white/20 backdrop-blur">
                <p class="font-display text-3xl font-semibold">{{ $maxCapacity }}</p>
                <p class="mt-1.5 text-base text-sand-100/85">hóspedes máx.</p>
            </div>
            <div class="rounded-2xl bg-white/10 p-5 ring-1 ring-inset ring-white/20 backdrop-blur">
                <svg class="h-8 w-8 text-amber-200" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m4 12.5 4.5 4.5L20 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <p class="mt-1.5 text-base text-sand-100/85">reserva direta por contacto</p>
            </div>
        </div>
    </x-page-hero>

    <section class="bg-white">
        <div class="mx-auto max-w-screen-2xl px-4 py-16 sm:px-6 lg:px-10 lg:py-24">
            <div class="reveal">
                <x-image-gallery :photos="$house->photos" :fallback="$cover" :title="$house->name" />
            </div>

            <div class="mt-14 grid gap-10 lg:grid-cols-[1.1fr_0.9fr] lg:gap-14">
                <div class="reveal">
                    <x-section-heading
                        eyebrow="A casa"
                        :title="'Sobre a ' . $house->name"
                        :description="$house->description"
                    />
                </div>

                <div class="reveal reveal-d1 grid gap-4 sm:grid-cols-2">
                    @php
                        $features = [
                            ['title' => 'Estadias flexíveis', 'text' => 'Reserve a unidade que corresponde ao tamanho do grupo.', 'icon' => 'M4 7h16M4 12h16M4 17h10'],
                            ['title' => 'Base para explorar', 'text' => 'Boa ligação a trilhos, cascatas e miradouros.', 'icon' => 'm3 18 5-7 4 5 3-4 6 6M3 18h18'],
                            ['title' => 'Privacidade', 'text' => 'Unidades independentes para estadias tranquilas.', 'icon' => 'M6 11V8a6 6 0 0 1 12 0v3M5 11h14v9H5z'],
                            ['title' => 'Confirmação direta', 'text' => 'Datas e condições validadas por contacto.', 'icon' => 'm4 12.5 4.5 4.5L20 6'],
                        ];
                    @endphp
                    @foreach ($features as $item)
                        <div class="rounded-2xl border border-sand-200 bg-sand-50/70 p-5 transition-colors duration-200 hover:border-fir-200 hover:bg-white lg:p-6">
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-fir-600/10 text-fir-700 ring-1 ring-inset ring-fir-700/15">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="{{ $item['icon'] }}" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </span>
                            <p class="mt-4 text-base font-semibold text-stone-950 lg:text-lg">{{ $item['title'] }}</p>
                            <p class="mt-1.5 text-sm leading-6 text-stone-600 lg:text-base lg:leading-7">{{ $item['text'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="bg-sand-50">
        <div class="mx-auto max-w-screen-2xl px-4 py-16 sm:px-6 lg:px-10 lg:py-24">
            <div class="reveal flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
                <x-section-heading
                    eyebrow="Unidades"
                    title="Unidades alugáveis nesta casa"
                    description="Veja capacidade, comodidades e ocupações futuras antes de pedir a reserva."
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
                    <p class="note">Esta casa ainda não tem unidades ativas.</p>
                @endforelse
            </div>
        </div>
    </section>

    <x-contact-cta />
@endsection
