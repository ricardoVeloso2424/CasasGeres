@extends('layouts.app')

@section('content')
    <section class="relative min-h-[460px] overflow-hidden bg-stone-950 text-white lg:min-h-[540px]">
        <img src="https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&w=1900&q=80" alt="Interior acolhedor de alojamento familiar" fetchpriority="high" decoding="async" class="absolute inset-0 h-full w-full object-cover opacity-50">
        <div class="absolute inset-0 bg-gradient-to-r from-stone-950 via-stone-950/75 to-stone-950/20"></div>

        <div class="relative mx-auto max-w-screen-2xl px-4 py-24 sm:px-6 lg:px-10 lg:py-32">
            <p class="text-sm font-semibold uppercase tracking-[0.08em] text-emerald-100 lg:text-base">Sobre nos</p>
            <h1 class="mt-5 max-w-5xl text-4xl font-semibold leading-tight sm:text-5xl lg:text-6xl">Um projeto familiar no coracao do Geres</h1>
            <p class="mt-6 max-w-3xl text-base leading-8 text-stone-100 sm:text-lg lg:text-xl lg:leading-9">Casas pensadas para receber com simplicidade, conforto e proximidade a natureza.</p>
        </div>
    </section>

    <section class="bg-white">
        <div class="mx-auto grid max-w-screen-2xl gap-10 px-4 py-16 sm:px-6 lg:grid-cols-[1fr_0.9fr] lg:items-start lg:gap-14 lg:px-10 lg:py-24">
            <div>
                <x-section-heading
                    eyebrow="A nossa forma de receber"
                    title="Estadias acompanhadas desde o primeiro contacto"
                    description="Cada pedido e tratado diretamente, para que datas, unidade e condicoes sejam confirmadas com clareza antes da reserva."
                />

                <div class="mt-10 grid max-w-4xl gap-6 text-lg leading-9 text-stone-700">
                    <p>Estas casas nasceram da vontade de receber bem e de partilhar uma zona que faz parte da vida da familia. O objetivo e criar estadias simples, confortaveis e proximas da natureza.</p>
                    <p>Algumas casas estao divididas em unidades independentes, o que permite acolher casais, familias pequenas ou grupos que querem ficar perto mantendo privacidade.</p>
                    <p>O site ajuda a conhecer as casas, consultar datas indisponiveis de forma indicativa e entrar em contacto sem intermediarios.</p>
                </div>
            </div>

            <img src="https://images.unsplash.com/photo-1560185127-6ed189bf02f4?auto=format&fit=crop&w=1400&q=80" alt="Sala luminosa de alojamento familiar" loading="lazy" decoding="async" class="min-h-96 w-full rounded-lg object-cover lg:min-h-[520px]">
        </div>
    </section>

    <section class="bg-stone-50">
        <div class="mx-auto max-w-screen-2xl px-4 py-16 sm:px-6 lg:px-10 lg:py-24">
            <x-section-heading
                eyebrow="Confianca"
                title="O que pode esperar"
                description="Uma experiencia clara, sem automatismos escondidos, com informacao util para decidir."
            />

            <div class="mt-10 grid gap-6 md:grid-cols-3 lg:gap-8">
                @foreach ([
                    ['title' => 'Reserva direta', 'text' => 'Fala connosco antes de fechar datas e condicoes.'],
                    ['title' => 'Informacao honesta', 'text' => 'Disponibilidade indicativa e bloqueios visiveis nas unidades.'],
                    ['title' => 'Base no Geres', 'text' => 'Casas perto de trilhos, cascatas, miradouros e praias fluviais.'],
                ] as $item)
                    <div class="rounded-lg border border-stone-200 bg-white p-7 shadow-sm">
                        <p class="text-2xl font-semibold text-stone-950">{{ $item['title'] }}</p>
                        <p class="mt-4 text-base leading-7 text-stone-600">{{ $item['text'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <x-contact-cta />
@endsection
