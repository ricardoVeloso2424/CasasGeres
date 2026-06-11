@php
    $siteName = config('site.name', config('app.name'));
    $defaultDescription = config('site.default_description', 'Alojamento local familiar no Gerês para reserva direta.');
    $pageTitle = $seo['title'] ?? $siteName;
    $title = str_contains($pageTitle, $siteName) ? $pageTitle : $pageTitle . ' | ' . $siteName;
    $description = $seo['description'] ?? $defaultDescription;
    $canonical = $seo['canonical'] ?? url()->current();
    $ogTitle = $seo['og_title'] ?? $pageTitle;
    $ogDescription = $seo['og_description'] ?? $description;
    $ogType = $seo['type'] ?? 'website';
    $ogImage = $seo['image'] ?? config('site.default_og_image');
    $twitterCard = $ogImage ? 'summary_large_image' : 'summary';
    $jsonLd = $schema ?? null;

    $footerPhone = config('site.phone');
    $footerPhoneHref = config('site.phone_href');
    $footerWhatsapp = config('site.whatsapp');
    $footerEmail = config('site.email');
    $footerResponsible = config('site.responsible_name');

    /*
     * Active houses for the public navigation, shared by the View Composer
     * registered in AppServiceProvider as $navigationHouses.
     */
    $navHouses = $navigationHouses ?? collect();

    $routeHouse = request()->route('house');
    $currentHouseSlug = $routeHouse instanceof \App\Models\House ? $routeHouse->slug : (is_string($routeHouse) ? $routeHouse : null);

    $houseLinks = $navHouses
        ->map(fn ($navHouse) => [
            'label' => $navHouse->name,
            'href' => route('houses.show', $navHouse),
            'active' => $currentHouseSlug === $navHouse->slug,
        ])
        ->all();

    $navBefore = [
        ['label' => 'Início', 'href' => route('home'), 'active' => request()->routeIs('home')],
    ];

    $navAfter = [
        ['label' => 'Atividades', 'href' => route('activities.index'), 'active' => request()->routeIs('activities.*')],
        ['label' => 'FAQ', 'href' => route('pages.faq'), 'active' => request()->routeIs('pages.faq')],
        ['label' => 'Contactos', 'href' => route('contact.index'), 'active' => request()->routeIs('contact.*')],
    ];

    $flatNavLinks = array_merge($navBefore, $houseLinks, $navAfter);

    // With many houses the bar would get crowded: collapse them into a light dropdown.
    $useHousesDropdown = count($houseLinks) > 3;
    $anyHouseActive = collect($houseLinks)->contains('active', true);

    $primaryHouse = $navHouses->first();
    $housesCtaHref = $navHouses->count() === 1 ? route('houses.show', $primaryHouse) : route('houses.index');
    $housesCtaLabel = $navHouses->count() === 1 ? 'Ver a casa' : 'Ver casas';
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script>document.documentElement.classList.add('js');</script>
        <meta name="description" content="{{ $description }}">
        <link rel="canonical" href="{{ $canonical }}">

        <meta property="og:title" content="{{ $ogTitle }}">
        <meta property="og:description" content="{{ $ogDescription }}">
        <meta property="og:type" content="{{ $ogType }}">
        <meta property="og:url" content="{{ $canonical }}">
        @if ($ogImage)
            <meta property="og:image" content="{{ $ogImage }}">
        @endif

        <meta name="twitter:card" content="{{ $twitterCard }}">
        <meta name="twitter:title" content="{{ $ogTitle }}">
        <meta name="twitter:description" content="{{ $ogDescription }}">
        @if ($ogImage)
            <meta name="twitter:image" content="{{ $ogImage }}">
        @endif

        <title>{{ $title }}</title>

        @if ($jsonLd)
            <script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
        @endif

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-sand-50 font-sans text-stone-900 antialiased">
        <a href="#conteudo" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded-xl focus:bg-fir-700 focus:px-5 focus:py-3 focus:text-sm focus:font-semibold focus:text-white">Saltar para o conteúdo</a>

        <div x-data="{ menuOpen: false }" x-on:keydown.escape.window="menuOpen = false" class="min-h-screen">
            <header class="site-header sticky top-0 z-40 border-b border-sand-200/80 bg-sand-50/90 backdrop-blur-md" data-elevate>
                <nav class="mx-auto flex min-h-16 max-w-screen-2xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:min-h-[4.75rem] lg:px-10">
                    <a href="{{ route('home') }}" class="group flex items-center gap-3" aria-label="{{ $siteName }}">
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-fir-600 to-fir-900 text-white shadow-sm ring-1 ring-fir-950/10 transition-transform duration-300 group-hover:scale-105 lg:h-12 lg:w-12">
                            <svg class="h-7 w-7" viewBox="0 0 32 32" fill="none" aria-hidden="true">
                                <path d="M3 24 11 11l5 7 4-5.5L29 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <circle cx="23" cy="9" r="2.4" stroke="#fcd34d" stroke-width="1.8"/>
                            </svg>
                        </span>
                        <span>
                            <span class="block font-display text-lg font-semibold leading-tight text-stone-950 lg:text-xl">{{ $siteName }}</span>
                            <span class="block text-sm font-medium text-stone-500">{{ config('site.location') }}</span>
                        </span>
                    </a>

                    <div class="hidden items-center gap-0.5 text-base lg:flex">
                        @foreach ($navBefore as $link)
                            <a href="{{ $link['href'] }}" @class(['nav-link', 'nav-link-active' => $link['active']]) @if ($link['active']) aria-current="page" @endif>{{ $link['label'] }}</a>
                        @endforeach

                        @if ($useHousesDropdown)
                            <div class="relative" x-data="{ housesOpen: false }" x-on:keydown.escape.window="housesOpen = false">
                                <button
                                    type="button"
                                    @class(['nav-link inline-flex items-center gap-1.5', 'nav-link-active' => $anyHouseActive])
                                    x-on:click="housesOpen = ! housesOpen"
                                    x-bind:aria-expanded="housesOpen.toString()"
                                >
                                    Alojamentos
                                    <svg class="h-4 w-4 transition-transform duration-200" x-bind:class="housesOpen && 'rotate-180'" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="m5 8 5 5 5-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                                <div
                                    x-show="housesOpen"
                                    x-cloak
                                    x-on:click.outside="housesOpen = false"
                                    x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="-translate-y-1 opacity-0"
                                    x-transition:enter-end="translate-y-0 opacity-100"
                                    x-transition:leave="transition ease-in duration-100"
                                    x-transition:leave-start="translate-y-0 opacity-100"
                                    x-transition:leave-end="-translate-y-1 opacity-0"
                                    class="absolute left-0 top-full z-50 mt-2 w-64 overflow-hidden rounded-2xl border border-sand-200 bg-white p-2 shadow-card-hover"
                                >
                                    @foreach ($houseLinks as $link)
                                        <a
                                            href="{{ $link['href'] }}"
                                            class="block rounded-xl px-4 py-2.5 font-medium transition-colors {{ $link['active'] ? 'bg-fir-600/10 text-fir-900' : 'text-stone-700 hover:bg-sand-50 hover:text-fir-800' }}"
                                            @if ($link['active']) aria-current="page" @endif
                                        >
                                            {{ $link['label'] }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            @foreach ($houseLinks as $link)
                                <a href="{{ $link['href'] }}" @class(['nav-link', 'nav-link-active' => $link['active']]) @if ($link['active']) aria-current="page" @endif>{{ $link['label'] }}</a>
                            @endforeach
                        @endif

                        @foreach ($navAfter as $link)
                            <a href="{{ $link['href'] }}" @class(['nav-link', 'nav-link-active' => $link['active']]) @if ($link['active']) aria-current="page" @endif>{{ $link['label'] }}</a>
                        @endforeach
                    </div>

                    <div class="hidden items-center lg:flex">
                        <a href="{{ $housesCtaHref }}" class="btn btn-sm btn-primary">
                            {{ $housesCtaLabel }}
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M4 10h12m0 0-4.5-4.5M16 10l-4.5 4.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </a>
                    </div>

                    <button type="button" class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-stone-300 text-stone-800 transition hover:border-fir-600 hover:text-fir-800 lg:hidden" x-on:click="menuOpen = ! menuOpen" aria-label="Abrir menu" x-bind:aria-expanded="menuOpen.toString()">
                        <span class="sr-only">Menu</span>
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path x-show="!menuOpen" d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path x-show="menuOpen" x-cloak d="M6 6l12 12M18 6 6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </button>
                </nav>

                <div
                    class="overflow-hidden border-t border-sand-200 bg-sand-50 lg:hidden"
                    x-show="menuOpen"
                    x-cloak
                    x-transition:enter="transition ease-out duration-250"
                    x-transition:enter-start="-translate-y-3 opacity-0"
                    x-transition:enter-end="translate-y-0 opacity-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="translate-y-0 opacity-100"
                    x-transition:leave-end="-translate-y-3 opacity-0"
                >
                    <div class="mx-auto grid max-w-screen-2xl gap-1.5 px-4 py-4 text-base font-medium text-stone-700 sm:px-6">
                        @foreach ($flatNavLinks as $link)
                            <a
                                href="{{ $link['href'] }}"
                                class="rounded-xl px-4 py-3 transition-colors {{ $link['active'] ? 'bg-fir-600/10 text-fir-900' : 'hover:bg-sand-100 hover:text-fir-800' }}"
                                x-on:click="menuOpen = false"
                                @if ($link['active']) aria-current="page" @endif
                            >
                                {{ $link['label'] }}
                            </a>
                        @endforeach
                        <div class="mt-2 border-t border-sand-200 pt-3">
                            <a href="{{ $housesCtaHref }}" class="btn btn-primary btn-block" x-on:click="menuOpen = false">{{ $housesCtaLabel }}</a>
                        </div>
                    </div>
                </div>
            </header>

            <main id="conteudo">
                @yield('content')
            </main>

            <footer class="relative overflow-hidden bg-fir-950 bg-topo-dark text-sand-100">
                <svg class="pointer-events-none absolute -right-16 bottom-8 h-64 w-auto text-sand-100/[0.05] lg:h-80" viewBox="0 0 600 300" fill="currentColor" aria-hidden="true">
                    <path d="M0 300 150 80l90 110 70-150 120 190 80-90 90 160v0z"/>
                </svg>
                <div class="texture-grain" aria-hidden="true"></div>

                <div class="relative mx-auto grid max-w-screen-2xl gap-10 px-4 py-14 sm:px-6 md:grid-cols-2 lg:grid-cols-3 lg:gap-12 lg:px-10 lg:py-16">
                    <div class="md:col-span-2 lg:col-span-1">
                        <div class="flex items-center gap-3">
                            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-white/10 text-sand-100 ring-1 ring-white/15">
                                <svg class="h-7 w-7" viewBox="0 0 32 32" fill="none" aria-hidden="true">
                                    <path d="M3 24 11 11l5 7 4-5.5L29 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <circle cx="23" cy="9" r="2.4" stroke="#fcd34d" stroke-width="1.8"/>
                                </svg>
                            </span>
                            <p class="font-display text-xl font-semibold">{{ $siteName }}</p>
                        </div>
                        <p class="mt-5 max-w-sm text-base leading-7 text-sand-200/80">Alojamento local familiar com unidades independentes, contacto direto e apoio próximo antes da reserva.</p>
                        <p class="mt-6 inline-flex items-center gap-2 text-sm font-medium text-sand-100">
                            <svg class="h-4 w-4 text-fir-300" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 21s7-5.6 7-11a7 7 0 1 0-14 0c0 5.4 7 11 7 11Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><circle cx="12" cy="10" r="2.4" stroke="currentColor" stroke-width="1.8"/></svg>
                            {{ config('site.location') }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.12em] text-fir-300">Contactos</p>
                        <div class="mt-5 grid gap-3 text-base text-sand-200/90">
                            @if ($footerResponsible)
                                <span class="text-sand-200/70">{{ $footerResponsible }}</span>
                            @endif
                            @if ($footerPhone)
                                <a href="tel:{{ $footerPhoneHref }}" class="inline-flex w-fit items-center gap-2.5 transition-colors hover:text-white">
                                    <svg class="h-4 w-4 shrink-0 text-fir-300" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 4h3l1.5 4-2 1.5a11 11 0 0 0 5 5l1.5-2 4 1.5v3a2 2 0 0 1-2 2A15 15 0 0 1 3 6a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>
                                    {{ $footerPhone }}
                                </a>
                            @endif
                            @if ($footerEmail)
                                <a href="mailto:{{ $footerEmail }}" class="inline-flex w-fit items-center gap-2.5 break-all transition-colors hover:text-white">
                                    <svg class="h-4 w-4 shrink-0 text-fir-300" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="m4 7 8 6 8-6" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>
                                    {{ $footerEmail }}
                                </a>
                            @endif
                            @if ($footerWhatsapp)
                                <a href="https://wa.me/{{ $footerWhatsapp }}?text=Ol%C3%A1%2C%20gostava%20de%20pedir%20disponibilidade%20no%20Ger%C3%AAs." class="inline-flex w-fit items-center gap-2.5 transition-colors hover:text-white">
                                    <svg class="h-4 w-4 shrink-0 text-fir-300" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 0 0-8.5 15.2L2 22l4.9-1.4A10 10 0 1 0 12 2Zm5.1 14.1c-.2.6-1.2 1.1-1.7 1.2-.5.1-1 .1-1.7-.1-.4-.1-.9-.3-1.5-.6a9 9 0 0 1-3.6-3.7c-.3-.6-.5-1.1-.5-1.6 0-.7.4-1.4.8-1.7.2-.2.4-.2.5-.2h.4c.2 0 .3 0 .5.4l.6 1.5c.1.2 0 .4-.1.5l-.3.4c-.1.1-.2.3-.1.5.3.6.7 1.1 1.2 1.5.5.4.9.6 1.3.8.2.1.4.1.5-.1l.5-.6c.1-.2.3-.2.5-.1l1.4.7c.2.1.3.2.3.3.1.2.1.5 0 .9Z"/></svg>
                                    WhatsApp
                                </a>
                            @endif
                        </div>
                    </div>

                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.12em] text-fir-300">Links rápidos</p>
                        <div class="mt-5 grid gap-3 text-base text-sand-200/90">
                            @foreach ($flatNavLinks as $link)
                                <a href="{{ $link['href'] }}" class="w-fit transition-colors hover:text-white">{{ $link['label'] }}</a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="relative border-t border-white/10">
                    <div class="mx-auto flex max-w-screen-2xl flex-col gap-2 px-4 py-6 text-sm text-sand-200/60 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-10">
                        <span>&copy; {{ now()->year }} {{ $siteName }}. Todos os direitos reservados.</span>
                        <span>{{ config('site.location') }}</span>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
