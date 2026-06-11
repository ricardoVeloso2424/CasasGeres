@extends('layouts.app')

@section('content')
    <x-page-hero
        size="lg"
        image="https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?auto=format&fit=crop&w=1900&q=80"
        srcset="https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?auto=format&fit=crop&w=800&q=80 800w, https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?auto=format&fit=crop&w=1280&q=80 1280w, https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?auto=format&fit=crop&w=1900&q=80 1900w"
        image-alt="Paisagem de montanha no Gerês"
        eyebrow="Alojamento local no Gerês"
        lead="Casas e unidades independentes para estadias tranquilas em família, com reserva confirmada por contacto direto."
        divider-to="white"
    >
        <x-slot:heading>
            Descanse no <em class="text-amber-200">Gerês</em> com a serra à porta
        </x-slot:heading>

        <div class="flex flex-col gap-3 sm:flex-row">
            <a href="{{ route('houses.index') }}" class="btn btn-primary">
                Ver casas
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M4 10h12m0 0-4.5-4.5M16 10l-4.5 4.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
            <a href="{{ route('contact.index') }}" class="btn btn-glass">Contactar</a>
        </div>
        <p class="mt-7 inline-flex items-center gap-2 text-sm font-medium text-sand-100/85">
            <svg class="h-4 w-4 text-amber-200" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 21s7-5.6 7-11a7 7 0 1 0-14 0c0 5.4 7 11 7 11Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><circle cx="12" cy="10" r="2.4" stroke="currentColor" stroke-width="1.8"/></svg>
            {{ config('site.location') }}
        </p>
    </x-page-hero>

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

            <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3 lg:gap-8">
                @forelse ($houses as $i => $house)
                    <div class="reveal reveal-d{{ min($i + 1, 3) }} h-full">
                        <x-house-card :house="$house" />
                    </div>
                @empty
                    <p class="note">Ainda não existem casas em destaque.</p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="bg-sand-50">
        <div class="mx-auto max-w-screen-2xl px-4 py-16 sm:px-6 lg:px-10 lg:py-24">
            <div class="reveal flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
                <x-section-heading
                    eyebrow="Gerês"
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
                    <p class="note">Ainda não existem atividades em destaque.</p>
                @endforelse
            </div>
        </div>
    </section>

    <x-contact-cta />
@endsection
