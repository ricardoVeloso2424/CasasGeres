@extends('layouts.app')

@section('content')
    <section class="relative isolate flex min-h-[420px] items-center overflow-hidden bg-stone-950 text-white lg:min-h-[500px]">
        <img src="https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1900&q=80" alt="Casa rodeada por natureza no Geres" fetchpriority="high" decoding="async" class="absolute inset-0 -z-10 h-full w-full object-cover">
        <div class="absolute inset-0 -z-10 bg-gradient-to-br from-stone-950/95 via-stone-950/78 to-stone-950/35"></div>
        <div class="absolute inset-0 -z-10 bg-gradient-to-t from-stone-950/85 via-transparent to-transparent"></div>

        <div class="mx-auto w-full max-w-screen-2xl px-4 py-20 sm:px-6 lg:px-10 lg:py-24">
            <p class="animate-rise d1 eyebrow text-emerald-200"><span class="eyebrow-line bg-emerald-400/60"></span>FAQ</p>
            <h1 class="animate-rise d2 mt-4 max-w-4xl font-display text-4xl font-semibold leading-[1.05] tracking-tight text-balance sm:text-5xl lg:text-6xl">Perguntas frequentes</h1>
            <p class="animate-rise d3 mt-6 max-w-2xl text-base leading-8 text-stone-200 sm:text-lg lg:text-xl lg:leading-9">Informacao pratica sobre entrada, saida, comodidades, disponibilidade e reserva direta.</p>
        </div>
    </section>

    <section class="bg-stone-50">
        <div class="mx-auto max-w-3xl px-4 py-16 sm:px-6 lg:py-24">
            <div class="reveal grid gap-4">
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
@endsection
