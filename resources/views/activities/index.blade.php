@extends('layouts.app')

@section('content')
    @php
        $categories = $activitiesByCategory->keys();
    @endphp

    <section class="relative min-h-[460px] overflow-hidden bg-stone-950 text-white lg:min-h-[540px]">
        <img src="https://images.unsplash.com/photo-1551632811-561732d1e306?auto=format&fit=crop&w=1900&q=80" alt="Trilho de montanha no Geres" fetchpriority="high" decoding="async" class="absolute inset-0 h-full w-full object-cover opacity-55">
        <div class="absolute inset-0 bg-gradient-to-r from-stone-950 via-stone-950/70 to-stone-950/20"></div>

        <div class="relative mx-auto max-w-screen-2xl px-4 py-24 sm:px-6 lg:px-10 lg:py-32">
            <p class="text-sm font-semibold uppercase tracking-[0.08em] text-emerald-100 lg:text-base">Geres</p>
            <h1 class="mt-5 max-w-5xl text-4xl font-semibold leading-tight sm:text-5xl lg:text-6xl">Atividades, trilhos e locais a visitar</h1>
            <p class="mt-6 max-w-3xl text-base leading-8 text-stone-100 sm:text-lg lg:text-xl lg:leading-9">Sugestoes para planear a estadia: trilhos, cascatas, miradouros, praias fluviais, termas e pontos historicos.</p>

            @if ($categories->isNotEmpty())
                <div class="mt-10 flex flex-wrap gap-3">
                    @foreach ($categories as $category)
                        <a href="#{{ \Illuminate\Support\Str::slug($category) }}" class="rounded-md bg-white px-4 py-2.5 text-base font-semibold text-stone-950 hover:bg-stone-100">{{ $category }}</a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <section class="bg-stone-50">
        <div class="mx-auto max-w-screen-2xl px-4 py-16 sm:px-6 lg:px-10 lg:py-24">
            <div class="grid gap-6 lg:grid-cols-[0.9fr_1.1fr] lg:items-end lg:gap-10">
                <x-section-heading
                    eyebrow="Roteiro"
                    title="Ideias por categoria"
                    description="A selecao mostra apenas atividades ativas, ordenadas para destacar primeiro as recomendadas."
                />

                <div class="rounded-lg border border-amber-100 bg-amber-50 p-6 text-base leading-8 text-amber-950 lg:p-7">
                    Algumas atividades dependem da epoca, meteorologia e acessos. Confirme sempre as condicoes antes de sair.
                </div>
            </div>

            <div class="mt-12 grid gap-16">
                @forelse ($activitiesByCategory as $category => $activities)
                    <div id="{{ \Illuminate\Support\Str::slug($category) }}" class="scroll-mt-28">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                            <h2 class="text-3xl font-semibold text-stone-950 lg:text-4xl">{{ $category }}</h2>
                            <span class="text-base font-medium text-stone-600">{{ $activities->count() }} sugestao{{ $activities->count() === 1 ? '' : 's' }}</span>
                        </div>

                        <div class="mt-8 grid gap-6 md:grid-cols-2 lg:grid-cols-3 lg:gap-8">
                            @foreach ($activities as $activity)
                                <x-activity-card :activity="$activity" />
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p class="rounded-lg border border-stone-200 bg-white p-6 text-sm text-stone-600">Ainda nao existem atividades ativas para apresentar.</p>
                @endforelse
            </div>
        </div>
    </section>

    <x-contact-cta />
@endsection
