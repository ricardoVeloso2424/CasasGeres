@extends('layouts.app')

@section('content')
    <section class="relative min-h-[460px] overflow-hidden bg-stone-950 text-white lg:min-h-[540px]">
        <img src="https://images.unsplash.com/photo-1518780664697-55e3ad937233?auto=format&fit=crop&w=1900&q=80" alt="Casa de campo no Geres" fetchpriority="high" decoding="async" class="absolute inset-0 h-full w-full object-cover opacity-55">
        <div class="absolute inset-0 bg-gradient-to-r from-stone-950 via-stone-950/70 to-stone-950/20"></div>

        <div class="relative mx-auto max-w-screen-2xl px-4 py-24 sm:px-6 lg:px-10 lg:py-32">
            <div class="max-w-5xl">
                <p class="text-sm font-semibold uppercase tracking-[0.08em] text-emerald-100 lg:text-base">Casas</p>
                <h1 class="mt-5 text-4xl font-semibold leading-tight sm:text-5xl lg:text-6xl">Casas e unidades para alugar no Geres</h1>
                <p class="mt-6 max-w-3xl text-base leading-8 text-stone-100 sm:text-lg lg:text-xl lg:leading-9">Escolha entre T1, T2 ou casa inteira. A disponibilidade e indicativa e a confirmacao final e sempre feita por contacto direto.</p>
            </div>

            <div class="mt-10 flex flex-wrap gap-4">
                <a href="{{ route('contact.index') }}" class="rounded-md bg-white px-6 py-3.5 text-base font-semibold text-stone-950 hover:bg-stone-100">Reservar / Contactar</a>
                <a href="#casas" class="rounded-md border border-white/50 px-6 py-3.5 text-base font-semibold text-white hover:bg-white/10">Ver alojamentos</a>
            </div>
        </div>
    </section>

    <section id="casas" class="bg-stone-50">
        <div class="mx-auto max-w-screen-2xl px-4 py-16 sm:px-6 lg:px-10 lg:py-24">
            <div class="grid gap-6 lg:grid-cols-[0.9fr_1.1fr] lg:items-end lg:gap-10">
                <x-section-heading
                    eyebrow="Alojamentos ativos"
                    title="Encontre a opcao certa"
                    description="Cada casa pode ter uma ou mais unidades alugaveis, com capacidades diferentes para adaptar a estadia ao grupo."
                />

                <div class="rounded-lg border border-sky-100 bg-sky-50 p-6 text-base leading-8 text-sky-950 lg:p-7">
                    As datas indisponiveis aparecem em cada unidade como informacao de apoio. Para reservar, envie pedido pela unidade ou contacte diretamente.
                </div>
            </div>

            <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3 lg:gap-8">
                @forelse ($houses as $house)
                    <x-house-card :house="$house" />
                @empty
                    <p class="rounded-lg border border-stone-200 bg-white p-6 text-sm text-stone-600">Nao existem casas ativas para apresentar.</p>
                @endforelse
            </div>
        </div>
    </section>

    <x-contact-cta />
@endsection
