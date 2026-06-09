<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ ($title ?? 'Admin') . ' | ' . config('app.name') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-stone-100 font-sans text-stone-900 antialiased">
        <div class="min-h-screen lg:grid lg:grid-cols-[260px_1fr]">
            <aside class="border-b border-stone-200 bg-stone-950 text-stone-100 lg:min-h-screen lg:border-b-0 lg:border-r">
                <div class="flex items-center justify-between px-4 py-4 lg:block lg:px-6">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-700 text-sm font-bold text-white">CG</span>
                        <span class="font-semibold">Admin</span>
                    </a>
                    <a href="{{ route('home') }}" class="text-sm font-medium text-stone-300 hover:text-white lg:mt-6 lg:block">Voltar ao site</a>
                </div>

                <nav class="grid gap-1 px-4 pb-4 text-sm font-medium text-stone-300 lg:px-6">
                    <a href="{{ route('admin.dashboard') }}" class="rounded-md px-3 py-2 hover:bg-white/10 hover:text-white">Dashboard</a>
                    <a href="{{ route('admin.houses.index') }}" class="rounded-md px-3 py-2 hover:bg-white/10 hover:text-white">Casas</a>
                    <a href="{{ route('admin.rental-units.index') }}" class="rounded-md px-3 py-2 hover:bg-white/10 hover:text-white">Unidades</a>
                    <a href="{{ route('admin.booking-requests.index') }}" class="rounded-md px-3 py-2 hover:bg-white/10 hover:text-white">Pedidos de reserva</a>
                    <a href="{{ route('admin.contact-messages.index') }}" class="rounded-md px-3 py-2 hover:bg-white/10 hover:text-white">Mensagens</a>
                    <a href="{{ route('admin.activities.index') }}" class="rounded-md px-3 py-2 hover:bg-white/10 hover:text-white">Atividades</a>
                    <a href="{{ route('admin.calendar-sources.index') }}" class="rounded-md px-3 py-2 hover:bg-white/10 hover:text-white">Calendarios iCal</a>
                    <a href="{{ route('admin.blocked-dates.index') }}" class="rounded-md px-3 py-2 hover:bg-white/10 hover:text-white">Datas bloqueadas</a>
                    <a href="{{ route('admin.amenities.index') }}" class="rounded-md px-3 py-2 hover:bg-white/10 hover:text-white">Comodidades</a>
                </nav>
            </aside>

            <div>
                <header class="border-b border-stone-200 bg-white">
                    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
                        <div>
                            <p class="text-xs font-semibold uppercase text-emerald-800">Painel admin</p>
                            <h1 class="mt-1 text-xl font-semibold text-stone-950">@yield('header', 'Dashboard')</h1>
                        </div>

                        <div class="flex items-center gap-4">
                            <span class="hidden text-sm text-stone-600 sm:inline">{{ auth()->user()->name }}</span>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="rounded-md border border-stone-300 px-4 py-2 text-sm font-semibold text-stone-800 hover:border-emerald-700 hover:text-emerald-800">Logout</button>
                            </form>
                        </div>
                    </div>
                </header>

                <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                    @if (session('status'))
                        <div class="mb-6 rounded-md bg-emerald-50 p-4 text-sm text-emerald-900">{{ session('status') }}</div>
                    @endif

                    @if (session('error'))
                        <div class="mb-6 rounded-md bg-red-50 p-4 text-sm text-red-900">{{ session('error') }}</div>
                    @endif

                    @yield('content')
                </main>
            </div>
        </div>
    </body>
</html>
