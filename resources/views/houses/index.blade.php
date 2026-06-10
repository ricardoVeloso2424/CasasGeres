@extends('layouts.app')

@section('content')
    <section class="relative isolate flex min-h-[440px] items-center overflow-hidden bg-stone-950 text-white lg:min-h-[520px]">
        <img src="https://images.unsplash.com/photo-1518780664697-55e3ad937233?auto=format&fit=crop&w=1900&q=80" alt="Casa de campo no Geres" fetchpriority="high" decoding="async" class="absolute inset-0 -z-10 h-full w-full object-cover">
        <div class="absolute inset-0 -z-10 bg-gradient-to-br from-stone-950/95 via-stone-950/75 to-stone-950/30"></div>
        <div class="absolute inset-0 -z-10 bg-gradient-to-t from-stone-950/80 via-transparent to-transparent"></div>

        <div class="mx-auto w-full max-w-screen-2xl px-4 py-20 sm:px-6 lg:px-10 lg:py-24">
            <div class="max-w-3xl">
                <p class="animate-rise d1 eyebrow text-emerald-200"><span class="eyebrow-line bg-emerald-400/60"></span>Casas</p>
                <h1 class="animate-rise d2 mt-4 font-display text-4xl font-semibold leading-[1.05] tracking-tight text-balance sm:text-5xl lg:text-6xl">Casas e unidades para alugar no Geres</h1>
                <p class="animate-rise d3 mt-6 max-w-2xl text-base leading-8 text-stone-200 sm:text-lg lg:text-xl lg:leading-9">Escolha entre T1, T2 ou casa inteira. A disponibilidade e indicativa e a confirmacao final e sempre feita por contacto direto.</p>

                <div class="animate-rise d4 mt-9">
                    <a href="#casas" class="btn btn-light">Ver alojamentos</a>
                </div>
            </div>
        </div>
    </section>

    <section id="casas" class="scroll-mt-24 bg-stone-50">
        <div class="mx-auto max-w-screen-2xl px-4 py-16 sm:px-6 lg:px-10 lg:py-24">
            <div class="reveal grid gap-6 lg:grid-cols-[0.9fr_1.1fr] lg:items-end lg:gap-10">
                <x-section-heading
                    eyebrow="Alojamentos ativos"
                    title="Encontre a opcao certa"
                    description="Cada casa pode ter uma ou mais unidades alugaveis, com capacidades diferentes para adaptar a estadia ao grupo."
                />

                <div class="note-info flex items-start gap-3 lg:p-7">
                    <svg class="mt-0.5 h-5 w-5 shrink-0 text-sky-600" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.7"/><path d="M12 11v5M12 7.5h.01" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                    <span>As datas indisponiveis aparecem em cada unidade como informacao de apoio. Para reservar, envie pedido pela unidade ou contacte diretamente.</span>
                </div>
            </div>

            <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3 lg:gap-8">
                @forelse ($houses as $i => $house)
                    <div class="reveal reveal-d{{ min($i + 1, 3) }} h-full">
                        <x-house-card :house="$house" />
                    </div>
                @empty
                    <p class="note">Nao existem casas ativas para apresentar.</p>
                @endforelse
            </div>
        </div>
    </section>

    <x-contact-cta />
@endsection
