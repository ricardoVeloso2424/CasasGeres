@extends('layouts.app')

@section('content')
    <x-page-hero
        size="sm"
        image="https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1900&q=80"
        srcset="https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=800&q=80 800w, https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1280&q=80 1280w, https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1900&q=80 1900w"
        image-alt="Casa rodeada por natureza no Gerês"
        eyebrow="FAQ"
        title="Perguntas frequentes"
        lead="Informação prática sobre entrada, saída, comodidades, disponibilidade e reserva direta."
        divider-to="sand"
    />

    <section class="bg-sand-50">
        <div class="mx-auto max-w-3xl px-4 py-16 sm:px-6 lg:py-24">
            <div class="reveal divide-y divide-sand-200/80 overflow-hidden rounded-2xl border border-sand-200 bg-white shadow-sm shadow-stone-950/5">
                <x-faq-item question="Qual é o horário de check-in?" answer="O horário de check-in é combinado por contacto direto antes da chegada." />
                <x-faq-item question="Qual é o horário de check-out?" answer="O check-out é normalmente até às 11h, salvo combinação diferente." />
                <x-faq-item question="Aceitam animais?" answer="Depende da unidade e da época. Deve ser confirmado antes da reserva." />
                <x-faq-item question="Têm estacionamento?" answer="As unidades têm estacionamento associado ou próximo." />
                <x-faq-item question="Têm Wi-Fi?" answer="Sim, as unidades estão preparadas com Wi-Fi." />
                <x-faq-item question="Têm cozinha equipada?" answer="Sim, as unidades foram pensadas para estadias com autonomia." />
                <x-faq-item question="A reserva pelo site fica automaticamente confirmada?" answer="Não. O pedido não confirma automaticamente a reserva. A confirmação será feita posteriormente por contacto direto." />
                <x-faq-item question="Como funciona a disponibilidade?" answer="A disponibilidade apresentada é indicativa, porque calendários externos podem não sincronizar em tempo real." />
                <x-faq-item question="Como faço reserva direta?" answer="Pode contactar por formulário, telefone, WhatsApp ou email para confirmar datas e condições." />
            </div>
        </div>
    </section>
@endsection
