@extends('layouts.app')

@section('content')
    <x-page-hero
        size="md"
        image="https://images.unsplash.com/photo-1518780664697-55e3ad937233?auto=format&fit=crop&w=1900&q=80"
        srcset="https://images.unsplash.com/photo-1518780664697-55e3ad937233?auto=format&fit=crop&w=800&q=80 800w, https://images.unsplash.com/photo-1518780664697-55e3ad937233?auto=format&fit=crop&w=1280&q=80 1280w, https://images.unsplash.com/photo-1518780664697-55e3ad937233?auto=format&fit=crop&w=1900&q=80 1900w"
        image-alt="Casa de campo no Gerês"
        eyebrow="Casas"
        title="Casas e unidades para alugar no Gerês"
        lead="Escolha entre T1, T2 ou casa inteira. A disponibilidade é indicativa e a confirmação final é sempre feita por contacto direto."
        divider-to="sand"
    >
        <a href="#casas" class="btn btn-light">Ver alojamentos</a>
    </x-page-hero>

    <section id="casas" class="scroll-mt-24 bg-sand-50">
        <div class="mx-auto max-w-screen-2xl px-4 py-16 sm:px-6 lg:px-10 lg:py-24">
            <div class="reveal grid gap-6 lg:grid-cols-[0.9fr_1.1fr] lg:items-end lg:gap-10">
                <x-section-heading
                    eyebrow="Alojamentos ativos"
                    title="Encontre a opção certa"
                    description="Cada casa pode ter uma ou mais unidades alugáveis, com capacidades diferentes para adaptar a estadia ao grupo."
                />

                <div class="note-info flex items-start gap-3 lg:p-7">
                    <svg class="mt-0.5 h-5 w-5 shrink-0 text-river-600" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.7"/><path d="M12 11v5M12 7.5h.01" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                    <span>As datas indisponíveis aparecem em cada unidade como informação de apoio. Para reservar, envie pedido pela unidade ou contacte diretamente.</span>
                </div>
            </div>

            <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3 lg:gap-8">
                @forelse ($houses as $i => $house)
                    <div class="reveal reveal-d{{ min($i + 1, 3) }} h-full">
                        <x-house-card :house="$house" />
                    </div>
                @empty
                    <p class="note">Não existem casas ativas para apresentar.</p>
                @endforelse
            </div>
        </div>
    </section>

    <x-contact-cta />
@endsection
