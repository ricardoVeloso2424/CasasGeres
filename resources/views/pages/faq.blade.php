@extends('layouts.app')

@section('content')
    <section class="relative min-h-[460px] overflow-hidden bg-stone-950 text-white lg:min-h-[540px]">
        <img src="https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1900&q=80" alt="Casa rodeada por natureza no Geres" fetchpriority="high" decoding="async" class="absolute inset-0 h-full w-full object-cover opacity-50">
        <div class="absolute inset-0 bg-gradient-to-r from-stone-950 via-stone-950/75 to-stone-950/20"></div>

        <div class="relative mx-auto max-w-screen-2xl px-4 py-24 sm:px-6 lg:px-10 lg:py-32">
            <p class="text-sm font-semibold uppercase tracking-[0.08em] text-emerald-100 lg:text-base">FAQ</p>
            <h1 class="mt-5 max-w-5xl text-4xl font-semibold leading-tight sm:text-5xl lg:text-6xl">Perguntas frequentes</h1>
            <p class="mt-6 max-w-3xl text-base leading-8 text-stone-100 sm:text-lg lg:text-xl lg:leading-9">Informacao pratica sobre entrada, saida, comodidades, disponibilidade e reserva direta.</p>
        </div>
    </section>

    <section class="bg-stone-50">
        <div class="mx-auto grid max-w-screen-2xl gap-10 px-4 py-16 sm:px-6 lg:grid-cols-[0.75fr_1.25fr] lg:gap-14 lg:px-10 lg:py-24">
            <div>
                <x-section-heading
                    eyebrow="Antes de reservar"
                    title="Respostas rapidas"
                    description="Para detalhes especificos de uma unidade ou data, o melhor caminho continua a ser contacto direto."
                />
                <a href="{{ route('contact.index') }}" class="mt-8 inline-flex rounded-md bg-emerald-800 px-6 py-3.5 text-base font-semibold text-white hover:bg-emerald-900">Reservar / Contactar</a>
            </div>

            <div class="grid gap-5">
                <x-faq-item question="Qual e o horario de check-in?" answer="O horario de check-in e combinado por contacto direto antes da chegada." />
                <x-faq-item question="Qual e o horario de check-out?" answer="O check-out e normalmente ate as 11h, salvo combinacao diferente." />
                <x-faq-item question="Aceitam animais?" answer="Depende da unidade e da epoca. Deve ser confirmado antes da reserva." />
                <x-faq-item question="Tem estacionamento?" answer="As unidades de exemplo tem estacionamento associado ou proximo." />
                <x-faq-item question="Tem Wi-Fi?" answer="Sim, as unidades estao preparadas com Wi-Fi nos dados de exemplo." />
                <x-faq-item question="Tem cozinha equipada?" answer="Sim, as unidades foram pensadas para estadias com autonomia." />
                <x-faq-item question="A reserva pelo site fica automaticamente confirmada?" answer="Nao. O pedido nao confirma automaticamente a reserva. A confirmacao sera feita posteriormente por contacto direto." />
                <x-faq-item question="Como funciona a disponibilidade?" answer="A disponibilidade apresentada e indicativa, porque calendarios externos podem nao sincronizar em tempo real." />
                <x-faq-item question="Como faco reserva direta?" answer="Pode contactar por formulario, telefone, WhatsApp ou email para confirmar datas e condicoes." />
            </div>
        </div>
    </section>

    <x-contact-cta />
@endsection
