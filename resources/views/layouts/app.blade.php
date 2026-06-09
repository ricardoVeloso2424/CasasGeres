@php
    $siteName = config('site.name', config('app.name'));
    $defaultDescription = config('site.default_description', 'Alojamento local familiar no Geres para reserva direta.');
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

    $navLinks = [
        ['label' => 'Inicio', 'route' => 'home', 'active' => 'home'],
        ['label' => 'Casas', 'route' => 'houses.index', 'active' => 'houses.*'],
        ['label' => 'Atividades', 'route' => 'activities.index', 'active' => 'activities.*'],
        ['label' => 'Sobre', 'route' => 'pages.about', 'active' => 'pages.about'],
        ['label' => 'FAQ', 'route' => 'pages.faq', 'active' => 'pages.faq'],
        ['label' => 'Contactos', 'route' => 'contact.index', 'active' => 'contact.*'],
    ];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
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
    <body class="min-h-screen bg-stone-50 font-sans text-stone-900 antialiased">
        <div x-data="{ menuOpen: false }" class="min-h-screen">
            <header class="sticky top-0 z-40 border-b border-stone-200 bg-white/95 shadow-sm backdrop-blur">
                <nav class="mx-auto flex min-h-16 max-w-screen-2xl items-center justify-between px-4 py-3 sm:px-6 lg:min-h-20 lg:px-10">
                    <a href="{{ route('home') }}" class="flex items-center gap-3" aria-label="{{ $siteName }}">
                        <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-emerald-800 text-base font-bold text-white lg:h-12 lg:w-12">CG</span>
                        <span>
                            <span class="block text-lg font-semibold leading-tight text-stone-950 lg:text-xl">{{ $siteName }}</span>
                            <span class="block text-sm font-medium text-stone-500">{{ config('site.location') }}</span>
                        </span>
                    </a>

                    <div class="hidden items-center gap-1 text-base font-medium text-stone-700 lg:flex">
                        @foreach ($navLinks as $link)
                            <a
                                href="{{ route($link['route']) }}"
                                class="rounded-md px-4 py-3 transition hover:bg-stone-100 hover:text-emerald-800 {{ request()->routeIs($link['active']) ? 'bg-emerald-50 text-emerald-900' : '' }}"
                            >
                                {{ $link['label'] }}
                            </a>
                        @endforeach
                    </div>

                    <div class="hidden items-center gap-3 lg:flex">
                        <a href="tel:{{ config('site.phone_href') }}" class="rounded-md border border-stone-300 px-5 py-3 text-base font-semibold text-stone-800 hover:border-emerald-700 hover:text-emerald-800">{{ config('site.phone') }}</a>
                        <a href="{{ route('contact.index') }}" class="rounded-md bg-emerald-800 px-6 py-3 text-base font-semibold text-white hover:bg-emerald-900">Reservar / Contactar</a>
                    </div>

                    <button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-md border border-stone-300 text-stone-800 lg:hidden" x-on:click="menuOpen = ! menuOpen" aria-label="Abrir menu" x-bind:aria-expanded="menuOpen.toString()">
                        <span class="sr-only">Menu</span>
                        <span class="grid gap-1">
                            <span class="h-0.5 w-5 bg-current"></span>
                            <span class="h-0.5 w-5 bg-current"></span>
                            <span class="h-0.5 w-5 bg-current"></span>
                        </span>
                    </button>
                </nav>

                <div class="border-t border-stone-200 bg-white px-4 py-4 lg:hidden" x-show="menuOpen" x-cloak>
                    <div class="mx-auto grid max-w-screen-2xl gap-2 text-base font-medium text-stone-700">
                        @foreach ($navLinks as $link)
                            <a
                                href="{{ route($link['route']) }}"
                                class="rounded-md px-3 py-3 {{ request()->routeIs($link['active']) ? 'bg-emerald-50 text-emerald-900' : 'hover:bg-stone-50' }}"
                                x-on:click="menuOpen = false"
                            >
                                {{ $link['label'] }}
                            </a>
                        @endforeach
                        <a href="{{ route('contact.index') }}" class="mt-2 rounded-md bg-emerald-800 px-4 py-3 text-center font-semibold text-white" x-on:click="menuOpen = false">Reservar / Contactar</a>
                    </div>
                </div>
            </header>

            <main>
                @yield('content')
            </main>

            <footer class="border-t border-stone-200 bg-stone-950 text-stone-100">
                <div class="mx-auto grid max-w-screen-2xl gap-10 px-4 py-16 sm:px-6 md:grid-cols-2 lg:grid-cols-4 lg:px-10">
                    <div>
                        <div class="flex items-center gap-3">
                            <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-700 text-sm font-bold text-white">CG</span>
                            <p class="text-lg font-semibold">{{ $siteName }}</p>
                        </div>
                        <p class="mt-4 text-sm leading-6 text-stone-300">Alojamento local familiar com unidades independentes, contacto direto e apoio proximo antes da reserva.</p>
                    </div>

                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.08em] text-stone-400">Contactos</p>
                        <div class="mt-4 grid gap-2 text-sm text-stone-300">
                            <span>{{ config('site.responsible_name') }}</span>
                            <a href="tel:{{ config('site.phone_href') }}" class="hover:text-white">{{ config('site.phone') }}</a>
                            <a href="mailto:{{ config('site.email') }}" class="hover:text-white">{{ config('site.email') }}</a>
                            <a href="https://wa.me/{{ config('site.whatsapp') }}?text=Ola%2C%20gostava%20de%20pedir%20disponibilidade%20no%20Geres." class="hover:text-white">WhatsApp</a>
                        </div>
                    </div>

                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.08em] text-stone-400">Links rapidos</p>
                        <div class="mt-4 grid gap-2 text-sm text-stone-300">
                            @foreach ($navLinks as $link)
                                <a href="{{ route($link['route']) }}" class="hover:text-white">{{ $link['label'] }}</a>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.08em] text-stone-400">Disponibilidade</p>
                        <p class="mt-4 text-sm leading-6 text-stone-300">As datas apresentadas sao indicativas e podem depender de sincronizacao externa. A confirmacao final e feita por contacto direto.</p>
                        <p class="mt-4 text-sm font-semibold text-white">{{ config('site.location') }}</p>
                    </div>
                </div>

                <div class="border-t border-white/10">
                    <div class="mx-auto flex max-w-screen-2xl flex-col gap-2 px-4 py-5 text-sm text-stone-400 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-10">
                        <span>&copy; {{ now()->year }} {{ $siteName }}. Todos os direitos reservados.</span>
                        <span>Reserva direta e acompanhamento familiar.</span>
                    </div>
                </div>
            </footer>

            <div class="fixed bottom-4 right-4 z-50 flex flex-col gap-2 sm:flex-row">
                <a href="https://wa.me/{{ config('site.whatsapp') }}?text=Ola%2C%20gostava%20de%20pedir%20disponibilidade%20para%20uma%20casa%20no%20Geres." class="rounded-md bg-emerald-800 px-4 py-3 text-sm font-semibold text-white shadow-lg hover:bg-emerald-900">WhatsApp</a>
                <a href="tel:{{ config('site.phone_href') }}" class="rounded-md bg-white px-4 py-3 text-sm font-semibold text-stone-900 shadow-lg ring-1 ring-stone-200 hover:text-emerald-800">Ligar</a>
            </div>
        </div>
    </body>
</html>
