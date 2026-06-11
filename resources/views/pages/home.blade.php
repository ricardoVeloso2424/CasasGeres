@extends('layouts.app')

@section('content')
    @php
        $singleHouse = $houses->count() === 1 ? $houses->first() : null;
        $singleUnits = $singleHouse?->rentalUnits ?? collect();
        $singleUnitTypes = $singleUnits->pluck('type')->filter()->unique();
        $unitsTitle = $singleUnitTypes->count() === 1 && $singleUnitTypes->first() === 'T1'
            ? 'Escolha o T1 ideal para a estadia'
            : 'Escolha a unidade ideal para a estadia';
        $heroCtaHref = $singleHouse ? route('houses.show', $singleHouse) : route('houses.index');
        $heroCtaLabel = $singleHouse ? 'Ver a casa' : 'Ver casas';
    @endphp

    <x-page-hero
        size="lg"
        image="https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?auto=format&fit=crop&w=1900&q=80"
        srcset="https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?auto=format&fit=crop&w=800&q=80 800w, https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?auto=format&fit=crop&w=1280&q=80 1280w, https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?auto=format&fit=crop&w=1900&q=80 1900w"
        image-alt="Paisagem de montanha no Gerês"
        eyebrow="Alojamento local no Gerês"
        :lead="$singleHouse
            ? 'Casa familiar com unidades independentes para estadias tranquilas, com reserva confirmada por contacto direto.'
            : 'Casas e unidades independentes para estadias tranquilas em família, com reserva confirmada por contacto direto.'"
        divider-to="white"
    >
        <x-slot:heading>
            Descanse no <em class="text-amber-200">Gerês</em> com a serra à porta
        </x-slot:heading>

        <div class="flex flex-col gap-3 sm:flex-row">
            <a href="{{ $heroCtaHref }}" class="btn btn-lg btn-primary">
                {{ $heroCtaLabel }}
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M4 10h12m0 0-4.5-4.5M16 10l-4.5 4.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
            <a href="{{ route('contact.index') }}" class="btn btn-lg btn-glass">Contactar</a>
        </div>
        <p class="mt-8 inline-flex items-center gap-2 text-sm font-medium text-sand-100/85">
            <svg class="h-4 w-4 text-amber-200" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 21s7-5.6 7-11a7 7 0 1 0-14 0c0 5.4 7 11 7 11Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><circle cx="12" cy="10" r="2.4" stroke="currentColor" stroke-width="1.8"/></svg>
            {{ config('site.location') }}
        </p>
    </x-page-hero>

    @if ($singleHouse)
        <section class="bg-white">
            <div class="mx-auto max-w-screen-2xl px-4 py-16 sm:px-6 lg:px-10 lg:py-24">
                <div class="reveal">
                    <x-section-heading
                        eyebrow="O alojamento"
                        title="Uma casa com unidades independentes"
                        description="Unidades independentes na mesma casa, reservadas individualmente e confirmadas por contacto direto."
                    />
                </div>

                <div class="reveal mt-10">
                    <x-house-card :house="$singleHouse" :wide="true" />
                </div>
            </div>
        </section>

        @if ($singleUnits->isNotEmpty())
            <section class="bg-sand-50 bg-topo">
                <div class="mx-auto max-w-screen-2xl px-4 py-16 sm:px-6 lg:px-10 lg:py-24">
                    <div class="reveal flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
                        <x-section-heading
                            eyebrow="As unidades"
                            :title="$unitsTitle"
                            description="Veja capacidade, comodidades e ocupações futuras antes de pedir a reserva."
                        />
                        <a href="{{ route('houses.show', $singleHouse) }}" class="btn btn-sm btn-outline shrink-0">
                            Ver a casa
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M4 10h12m0 0-4.5-4.5M16 10l-4.5 4.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </a>
                    </div>

                    <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3 lg:gap-8">
                        @foreach ($singleUnits as $i => $unit)
                            <div class="reveal reveal-d{{ min($i + 1, 3) }} h-full">
                                <x-rental-unit-card :unit="$unit" :house="$singleHouse" />
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    @else
        <section class="bg-white">
            <div class="mx-auto max-w-screen-2xl px-4 py-16 sm:px-6 lg:px-10 lg:py-24">
                <div class="reveal flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
                    <x-section-heading
                        eyebrow="Casas"
                        title="Escolha a base certa para a estadia"
                        description="Casas familiares com unidades independentes, pensadas para casais, famílias pequenas e grupos."
                    />
                    <a href="{{ route('houses.index') }}" class="btn btn-sm btn-outline shrink-0">
                        Ver todas
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M4 10h12m0 0-4.5-4.5M16 10l-4.5 4.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </a>
                </div>

                @if ($houses->isEmpty())
                    <p class="note mt-10">Ainda não existem casas em destaque.</p>
                @else
                    <div class="mt-10 grid gap-6 lg:gap-8">
                        <div class="reveal">
                            <x-house-card :house="$houses->first()" :wide="true" />
                        </div>

                        @if ($houses->count() > 1)
                            <div class="grid gap-6 md:grid-cols-2 lg:gap-8">
                                @foreach ($houses->skip(1)->values() as $i => $house)
                                    <div class="reveal reveal-d{{ min($i + 1, 2) }} h-full">
                                        <x-house-card :house="$house" />
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </section>
    @endif

    <section @class(['bg-white' => $singleHouse && $singleUnits->isNotEmpty(), 'bg-sand-50 bg-topo' => ! ($singleHouse && $singleUnits->isNotEmpty())])>
        <div class="mx-auto max-w-screen-2xl px-4 py-16 sm:px-6 lg:px-10 lg:py-24">
            <div class="reveal flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
                <x-section-heading
                    eyebrow="Gerês"
                    title="Atividades perto do alojamento"
                    description="Trilhos, cascatas, miradouros e praias fluviais para descobrir durante a estadia."
                />
                <a href="{{ route('activities.index') }}" class="btn btn-sm btn-outline shrink-0">
                    Ver atividades
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M4 10h12m0 0-4.5-4.5M16 10l-4.5 4.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
            </div>

            <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3 lg:gap-8">
                @forelse ($activities as $i => $activity)
                    <div class="reveal reveal-d{{ min($i + 1, 3) }} h-full">
                        <x-activity-card :activity="$activity" />
                    </div>
                @empty
                    <p class="note">Ainda não existem atividades em destaque.</p>
                @endforelse
            </div>
        </div>
    </section>

    <x-contact-cta />
@endsection
