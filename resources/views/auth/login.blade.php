<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="{{ $seo['description'] ?? 'Acesso ao painel de administracao.' }}">
        <title>{{ ($seo['title'] ?? 'Login admin') . ' | ' . config('app.name') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-stone-100 font-sans text-stone-900 antialiased">
        <main class="flex min-h-screen items-center justify-center px-4 py-12">
            <section class="w-full max-w-md rounded-lg border border-stone-200 bg-white p-6 shadow-sm">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-800 text-sm font-bold text-white">CG</span>
                    <span class="text-base font-semibold text-stone-950">Casas Geres</span>
                </a>

                <div class="mt-8">
                    <h1 class="text-2xl font-semibold text-stone-950">Entrar no admin</h1>
                    <p class="mt-2 text-sm leading-6 text-stone-600">Acesso reservado para gestao dos alojamentos.</p>
                </div>

                <form method="POST" action="{{ route('login.store') }}" class="mt-8 grid gap-5">
                    @csrf

                    <label class="grid gap-2 text-sm font-medium text-stone-800">
                        Email
                        <input type="email" name="email" value="{{ old('email') }}" autocomplete="email" autofocus class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none" required>
                        @error('email') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
                    </label>

                    <label class="grid gap-2 text-sm font-medium text-stone-800">
                        Password
                        <input type="password" name="password" autocomplete="current-password" class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none" required>
                        @error('password') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
                    </label>

                    <label class="flex items-center gap-2 text-sm text-stone-700">
                        <input type="checkbox" name="remember" value="1" class="rounded border-stone-300 text-emerald-800 focus:ring-emerald-700">
                        Manter sessao iniciada
                    </label>

                    <button class="rounded-md bg-emerald-800 px-5 py-3 text-sm font-semibold text-white hover:bg-emerald-900">Entrar</button>
                </form>
            </section>
        </main>
    </body>
</html>
