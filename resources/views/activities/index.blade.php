@extends('layouts.app')

@section('content')
    @php
        $categories = $activitiesByCategory->keys();
    @endphp

    <x-page-hero
        size="md"
        image="https://images.unsplash.com/photo-1551632811-561732d1e306?auto=format&fit=crop&w=1900&q=80"
        srcset="https://images.unsplash.com/photo-1551632811-561732d1e306?auto=format&fit=crop&w=800&q=80 800w, https://images.unsplash.com/photo-1551632811-561732d1e306?auto=format&fit=crop&w=1280&q=80 1280w, https://images.unsplash.com/photo-1551632811-561732d1e306?auto=format&fit=crop&w=1900&q=80 1900w"
        image-alt="Trilho de montanha no Gerês"
        eyebrow="Gerês"
        title="Atividades, trilhos e locais a visitar"
        lead="Sugestões para planear a estadia: trilhos, cascatas, miradouros, praias fluviais, termas e pontos históricos."
        divider-to="sand"
    >
        @if ($categories->isNotEmpty())
            <div class="flex flex-wrap gap-2.5">
                @foreach ($categories as $category)
                    <a href="#{{ \Illuminate\Support\Str::slug($category) }}" class="chip chip-glass transition hover:bg-white/25">{{ $category }}</a>
                @endforeach
            </div>
        @endif
    </x-page-hero>

    <section class="bg-sand-50 bg-topo">
        <div class="mx-auto max-w-screen-2xl px-4 py-16 sm:px-6 lg:px-10 lg:py-24">
            <div class="reveal grid gap-6 lg:grid-cols-[0.9fr_1.1fr] lg:items-end lg:gap-10">
                <x-section-heading
                    eyebrow="Roteiro"
                    title="Ideias por categoria"
                    description="A seleção mostra apenas atividades ativas, ordenadas para destacar primeiro as recomendadas."
                />

                <div class="note-amber flex items-start gap-3 lg:p-7">
                    <svg class="mt-0.5 h-5 w-5 shrink-0 text-amber-600" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 4 3 19h18L12 4Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M12 10v4M12 17h.01" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                    <span>Algumas atividades dependem da época, meteorologia e acessos. Confirme sempre as condições antes de sair.</span>
                </div>
            </div>

            <div class="mt-12 grid gap-14 lg:gap-16">
                @forelse ($activitiesByCategory as $category => $activities)
                    <div id="{{ \Illuminate\Support\Str::slug($category) }}" class="scroll-mt-24">
                        <div class="reveal flex flex-col gap-2 border-b border-sand-200 pb-5 sm:flex-row sm:items-end sm:justify-between">
                            <h2 class="font-display text-3xl font-semibold tracking-tight text-stone-950 lg:text-4xl">{{ $category }}</h2>
                            <span class="chip chip-fir w-fit">{{ $activities->count() }} sugest{{ $activities->count() === 1 ? 'ão' : 'ões' }}</span>
                        </div>

                        @if ($activities->count() === 1)
                            <div class="reveal mt-8">
                                <x-activity-card :activity="$activities->first()" :wide="true" />
                            </div>
                        @else
                            <div class="mt-8 grid gap-6 md:grid-cols-2 lg:grid-cols-3 lg:gap-8">
                                @foreach ($activities as $i => $activity)
                                    <div class="reveal reveal-d{{ min($i + 1, 3) }} h-full">
                                        <x-activity-card :activity="$activity" />
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @empty
                    <p class="note">Ainda não existem atividades ativas para apresentar.</p>
                @endforelse
            </div>
        </div>
    </section>

    <x-contact-cta />
@endsection
