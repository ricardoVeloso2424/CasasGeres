@extends('layouts.app')

@section('content')
    @php
        $categories = $activitiesByCategory->keys();
    @endphp

    <section class="relative isolate flex min-h-[440px] items-center overflow-hidden bg-stone-950 text-white lg:min-h-[520px]">
        <img src="https://images.unsplash.com/photo-1551632811-561732d1e306?auto=format&fit=crop&w=1900&q=80" alt="Trilho de montanha no Geres" fetchpriority="high" decoding="async" class="absolute inset-0 -z-10 h-full w-full object-cover">
        <div class="absolute inset-0 -z-10 bg-gradient-to-br from-stone-950/95 via-stone-950/75 to-stone-950/30"></div>
        <div class="absolute inset-0 -z-10 bg-gradient-to-t from-stone-950/80 via-transparent to-transparent"></div>

        <div class="mx-auto w-full max-w-screen-2xl px-4 py-20 sm:px-6 lg:px-10 lg:py-24">
            <p class="animate-rise d1 eyebrow text-emerald-200"><span class="eyebrow-line bg-emerald-400/60"></span>Geres</p>
            <h1 class="animate-rise d2 mt-4 max-w-4xl font-display text-4xl font-semibold leading-[1.05] tracking-tight text-balance sm:text-5xl lg:text-6xl">Atividades, trilhos e locais a visitar</h1>
            <p class="animate-rise d3 mt-6 max-w-2xl text-base leading-8 text-stone-200 sm:text-lg lg:text-xl lg:leading-9">Sugestoes para planear a estadia: trilhos, cascatas, miradouros, praias fluviais, termas e pontos historicos.</p>

            @if ($categories->isNotEmpty())
                <div class="animate-rise d4 mt-9 flex flex-wrap gap-2.5">
                    @foreach ($categories as $category)
                        <a href="#{{ \Illuminate\Support\Str::slug($category) }}" class="chip chip-glass transition hover:bg-white/25">{{ $category }}</a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <section class="bg-stone-50">
        <div class="mx-auto max-w-screen-2xl px-4 py-16 sm:px-6 lg:px-10 lg:py-24">
            <div class="reveal grid gap-6 lg:grid-cols-[0.9fr_1.1fr] lg:items-end lg:gap-10">
                <x-section-heading
                    eyebrow="Roteiro"
                    title="Ideias por categoria"
                    description="A selecao mostra apenas atividades ativas, ordenadas para destacar primeiro as recomendadas."
                />

                <div class="note-amber flex items-start gap-3 lg:p-7">
                    <svg class="mt-0.5 h-5 w-5 shrink-0 text-amber-600" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 4 3 19h18L12 4Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M12 10v4M12 17h.01" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                    <span>Algumas atividades dependem da epoca, meteorologia e acessos. Confirme sempre as condicoes antes de sair.</span>
                </div>
            </div>

            <div class="mt-12 grid gap-16">
                @forelse ($activitiesByCategory as $category => $activities)
                    <div id="{{ \Illuminate\Support\Str::slug($category) }}" class="scroll-mt-24">
                        <div class="reveal flex flex-col gap-2 border-b border-stone-200 pb-5 sm:flex-row sm:items-end sm:justify-between">
                            <h2 class="font-display text-3xl font-semibold tracking-tight text-stone-950 lg:text-4xl">{{ $category }}</h2>
                            <span class="chip chip-emerald w-fit">{{ $activities->count() }} sugestao{{ $activities->count() === 1 ? '' : 's' }}</span>
                        </div>

                        <div class="mt-8 grid gap-6 md:grid-cols-2 lg:grid-cols-3 lg:gap-8">
                            @foreach ($activities as $i => $activity)
                                <div class="reveal reveal-d{{ min($i + 1, 3) }} h-full">
                                    <x-activity-card :activity="$activity" />
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p class="note">Ainda nao existem atividades ativas para apresentar.</p>
                @endforelse
            </div>
        </div>
    </section>

    <x-contact-cta />
@endsection
