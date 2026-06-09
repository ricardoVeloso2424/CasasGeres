@extends('layouts.app')

@section('content')
    <section class="relative min-h-[560px] overflow-hidden bg-stone-950 text-white md:min-h-[680px] lg:min-h-[70vh]">
        <img src="https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?auto=format&fit=crop&w=1900&q=80" alt="Paisagem de montanha no Geres" fetchpriority="high" decoding="async" class="absolute inset-0 h-full w-full object-cover opacity-70">
        <div class="absolute inset-0 bg-gradient-to-r from-stone-950 via-stone-950/65 to-stone-950/15"></div>

        <div class="relative mx-auto flex min-h-[560px] max-w-screen-2xl items-center px-4 py-20 sm:px-6 md:min-h-[680px] lg:min-h-[70vh] lg:px-10">
            <div class="max-w-5xl">
                <div class="flex flex-wrap gap-3">
                    <span class="rounded-md bg-white px-4 py-2 text-sm font-semibold text-stone-950">Reserva direta</span>
                    <span class="rounded-md bg-emerald-100 px-4 py-2 text-sm font-semibold text-emerald-950">Alojamento familiar</span>
                    <span class="rounded-md bg-amber-100 px-4 py-2 text-sm font-semibold text-amber-950">Geres, Portugal</span>
                </div>

                <p class="mt-8 text-sm font-semibold uppercase tracking-[0.08em] text-emerald-100 lg:text-base">Casas e unidades independentes</p>
                <h1 class="mt-5 max-w-5xl text-4xl font-semibold leading-tight sm:text-5xl lg:text-6xl xl:text-7xl">Descanse no Gerês com a serra a porta</h1>
                <p class="mt-7 max-w-3xl text-base leading-8 text-stone-100 sm:text-lg lg:text-xl lg:leading-9">T1, T2 e casa inteira para estadias tranquilas em familia, com contacto direto para confirmar datas, condicoes e disponibilidade.</p>

                <div class="mt-10 flex flex-wrap gap-4">
                    <a href="{{ route('houses.index') }}" class="rounded-md bg-emerald-800 px-6 py-3.5 text-base font-semibold text-white hover:bg-emerald-900">Ver casas</a>
                    <a href="{{ route('contact.index') }}" class="rounded-md bg-white px-6 py-3.5 text-base font-semibold text-stone-950 hover:bg-stone-100">Reservar / Contactar</a>
                    <a href="{{ route('activities.index') }}" class="rounded-md border border-white/50 px-6 py-3.5 text-base font-semibold text-white hover:bg-white/10">Explorar atividades</a>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white">
        <div class="mx-auto max-w-screen-2xl px-4 py-14 sm:px-6 lg:px-10 lg:py-20">
            <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-4 lg:gap-6">
                @foreach ([
                    ['title' => 'Unidades equipadas', 'text' => 'Espacos preparados para estadias autonomas.'],
                    ['title' => 'Natureza perto', 'text' => 'Trilhos, cascatas e miradouros a curta distancia.'],
                    ['title' => 'Contacto familiar', 'text' => 'Apoio direto antes de confirmar a reserva.'],
                    ['title' => 'Disponibilidade clara', 'text' => 'Datas bloqueadas visiveis de forma indicativa.'],
                ] as $highlight)
                    <div class="rounded-lg border border-stone-200 bg-stone-50 p-6">
                        <p class="text-lg font-semibold text-stone-950">{{ $highlight['title'] }}</p>
                        <p class="mt-3 text-base leading-7 text-stone-600">{{ $highlight['text'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-stone-50">
        <div class="mx-auto max-w-screen-2xl px-4 py-16 sm:px-6 lg:px-10 lg:py-24">
            <div class="flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
                <x-section-heading
                    eyebrow="Casas"
                    title="Escolha a base certa para a estadia"
                    description="Casas familiares com unidades independentes, pensadas para casais, familias pequenas e grupos."
                />
                <a href="{{ route('houses.index') }}" class="inline-flex rounded-md border border-stone-300 px-4 py-3 text-sm font-semibold text-stone-800 hover:border-emerald-700 hover:text-emerald-800">Ver todas</a>
            </div>

            <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3 lg:gap-8">
                @forelse ($houses as $house)
                    <x-house-card :house="$house" />
                @empty
                    <p class="rounded-lg border border-stone-200 bg-white p-6 text-sm text-stone-600">Ainda nao existem casas em destaque.</p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="bg-white">
        <div class="mx-auto grid max-w-screen-2xl gap-10 px-4 py-16 sm:px-6 lg:grid-cols-[0.9fr_1.1fr] lg:gap-14 lg:px-10 lg:py-24">
            <div>
                <x-section-heading
                    eyebrow="Como funciona"
                    title="Reserva simples, confirmada por pessoas"
                    description="O site ajuda a escolher casa e datas, mas a confirmacao final e sempre feita por contacto direto."
                />

                <div class="mt-10 grid gap-5">
                    @foreach ([
                        ['step' => '01', 'title' => 'Escolha a casa', 'text' => 'Veja as unidades, capacidade, comodidades e datas indisponiveis.'],
                        ['step' => '02', 'title' => 'Envie o pedido', 'text' => 'Use o formulario, WhatsApp, telefone ou email.'],
                        ['step' => '03', 'title' => 'Confirme connosco', 'text' => 'Validamos disponibilidade e condicoes antes da reserva ficar fechada.'],
                    ] as $item)
                        <div class="rounded-lg border border-stone-200 bg-stone-50 p-6">
                            <p class="text-sm font-semibold uppercase tracking-[0.08em] text-sky-700">{{ $item['step'] }}</p>
                            <p class="mt-3 text-lg font-semibold text-stone-950">{{ $item['title'] }}</p>
                            <p class="mt-3 text-base leading-7 text-stone-600">{{ $item['text'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <img src="https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&w=1400&q=80" alt="Interior acolhedor de alojamento local" loading="lazy" decoding="async" class="min-h-96 w-full rounded-lg object-cover lg:min-h-[560px]">
        </div>
    </section>

    <section class="bg-stone-50">
        <div class="mx-auto max-w-screen-2xl px-4 py-16 sm:px-6 lg:px-10 lg:py-24">
            <div class="flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
                <x-section-heading
                    eyebrow="Geres"
                    title="Atividades perto dos alojamentos"
                    description="Trilhos, cascatas, miradouros, praias fluviais e locais para descobrir durante a estadia."
                />
                <a href="{{ route('activities.index') }}" class="inline-flex rounded-md border border-stone-300 px-4 py-3 text-sm font-semibold text-stone-800 hover:border-emerald-700 hover:text-emerald-800">Ver atividades</a>
            </div>

            <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3 lg:gap-8">
                @forelse ($activities as $activity)
                    <x-activity-card :activity="$activity" />
                @empty
                    <p class="rounded-lg border border-stone-200 bg-white p-6 text-sm text-stone-600">Ainda nao existem atividades em destaque.</p>
                @endforelse
            </div>
        </div>
    </section>

    <x-contact-cta />
@endsection
