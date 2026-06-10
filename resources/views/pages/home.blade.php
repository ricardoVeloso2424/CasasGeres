@extends('layouts.app')

@section('content')
    <section class="relative isolate flex min-h-[540px] items-center overflow-hidden bg-stone-950 text-white md:min-h-[620px] lg:min-h-[72vh]">
        <img src="https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?auto=format&fit=crop&w=1900&q=80" alt="Paisagem de montanha no Geres" fetchpriority="high" decoding="async" class="absolute inset-0 -z-10 h-full w-full object-cover">
        <div class="absolute inset-0 -z-10 bg-gradient-to-br from-stone-950/95 via-stone-950/70 to-stone-950/25"></div>
        <div class="absolute inset-0 -z-10 bg-gradient-to-t from-stone-950/85 via-transparent to-transparent"></div>

        <div class="mx-auto w-full max-w-screen-2xl px-4 py-20 sm:px-6 lg:px-10">
            <div class="max-w-2xl">
                <p class="animate-rise d1 eyebrow text-emerald-200"><span class="eyebrow-line bg-emerald-400/60"></span>Alojamento local no Geres</p>
                <h1 class="animate-rise d1 mt-4 font-display text-4xl font-semibold leading-[1.05] tracking-tight text-balance sm:text-5xl lg:text-6xl xl:text-7xl">Descanse no Gerês com a serra a porta</h1>
                <p class="animate-rise d2 mt-6 max-w-xl text-base leading-8 text-stone-200 sm:text-lg lg:text-xl lg:leading-9">Casas e unidades independentes para estadias tranquilas em familia, com reserva confirmada por contacto direto.</p>

                <div class="animate-rise d3 mt-9 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('houses.index') }}" class="btn btn-primary">
                        Ver casas
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M4 10h12m0 0-4.5-4.5M16 10l-4.5 4.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </a>
                    <a href="{{ route('contact.index') }}" class="btn btn-glass">Contactar</a>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white">
        <div class="mx-auto max-w-screen-2xl px-4 py-16 sm:px-6 lg:px-10 lg:py-24">
            <div class="reveal flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
                <x-section-heading
                    eyebrow="Casas"
                    title="Escolha a base certa para a estadia"
                    description="Casas familiares com unidades independentes, pensadas para casais, familias pequenas e grupos."
                />
                <a href="{{ route('houses.index') }}" class="btn btn-sm btn-outline shrink-0">
                    Ver todas
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M4 10h12m0 0-4.5-4.5M16 10l-4.5 4.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
            </div>

            <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3 lg:gap-8">
                @forelse ($houses as $i => $house)
                    <div class="reveal reveal-d{{ min($i + 1, 3) }} h-full">
                        <x-house-card :house="$house" />
                    </div>
                @empty
                    <p class="note">Ainda nao existem casas em destaque.</p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="bg-stone-50">
        <div class="mx-auto max-w-screen-2xl px-4 py-16 sm:px-6 lg:px-10 lg:py-24">
            <div class="reveal flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
                <x-section-heading
                    eyebrow="Geres"
                    title="Atividades perto dos alojamentos"
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
                    <p class="note">Ainda nao existem atividades em destaque.</p>
                @endforelse
            </div>
        </div>
    </section>
@endsection
