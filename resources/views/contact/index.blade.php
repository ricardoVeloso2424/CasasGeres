@extends('layouts.app')

@section('content')
    @php
        $phone = config('site.phone');
        $phoneHref = config('site.phone_href');
        $whatsapp = config('site.whatsapp');
        $email = config('site.email');
        $location = config('site.location');
    @endphp

    <section class="relative min-h-[460px] overflow-hidden bg-stone-950 text-white lg:min-h-[540px]">
        <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1900&q=80" alt="Agua e montanhas no Geres" fetchpriority="high" decoding="async" class="absolute inset-0 h-full w-full object-cover opacity-50">
        <div class="absolute inset-0 bg-gradient-to-r from-stone-950 via-stone-950/75 to-stone-950/20"></div>

        <div class="relative mx-auto max-w-screen-2xl px-4 py-24 sm:px-6 lg:px-10 lg:py-32">
            <p class="text-sm font-semibold uppercase tracking-[0.08em] text-emerald-100 lg:text-base">Contactos</p>
            <h1 class="mt-5 max-w-5xl text-4xl font-semibold leading-tight sm:text-5xl lg:text-6xl">Reserva direta por contacto</h1>
            <p class="mt-6 max-w-3xl text-base leading-8 text-stone-100 sm:text-lg lg:text-xl lg:leading-9">Envie uma mensagem, ligue ou fale por WhatsApp para confirmar disponibilidade. A reserva pelo site nao fica automaticamente confirmada.</p>
        </div>
    </section>

    <section class="bg-white">
        <div class="mx-auto grid max-w-screen-2xl gap-10 px-4 py-16 sm:px-6 lg:grid-cols-[0.9fr_1.1fr] lg:gap-14 lg:px-10 lg:py-24">
            <div class="grid gap-8">
                <x-section-heading
                    eyebrow="Fale connosco"
                    title="Confirmamos tudo diretamente"
                    description="Partilhe datas, numero de hospedes e unidade pretendida. Respondemos com disponibilidade e proximos passos."
                />

                <div class="grid gap-5">
                    <a href="tel:{{ $phoneHref }}" class="rounded-lg border border-stone-200 bg-stone-50 p-6 hover:border-emerald-300">
                        <p class="text-sm font-semibold uppercase tracking-[0.08em] text-emerald-800">Telefone</p>
                        <p class="mt-3 text-2xl font-semibold text-stone-950">{{ $phone }}</p>
                    </a>

                    <a href="https://wa.me/{{ $whatsapp }}?text=Ola%2C%20gostava%20de%20pedir%20disponibilidade%20no%20Geres." class="rounded-lg border border-stone-200 bg-stone-50 p-6 hover:border-emerald-300">
                        <p class="text-sm font-semibold uppercase tracking-[0.08em] text-emerald-800">WhatsApp</p>
                        <p class="mt-3 text-2xl font-semibold text-stone-950">Enviar mensagem</p>
                    </a>

                    <a href="mailto:{{ $email }}" class="rounded-lg border border-stone-200 bg-stone-50 p-6 hover:border-emerald-300">
                        <p class="text-sm font-semibold uppercase tracking-[0.08em] text-emerald-800">Email</p>
                        <p class="mt-3 text-2xl font-semibold text-stone-950">{{ $email }}</p>
                    </a>

                    <div class="rounded-lg border border-sky-100 bg-sky-50 p-6 text-base leading-8 text-sky-950">
                        A disponibilidade apresentada nas unidades e indicativa. A confirmacao final e feita por contacto direto depois de validarmos os calendarios.
                    </div>
                </div>

                <div class="flex min-h-96 items-center justify-center rounded-lg border border-dashed border-stone-300 bg-stone-50 p-8 text-center">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.08em] text-stone-500">Localizacao</p>
                        <p class="mt-3 text-3xl font-semibold text-stone-950">{{ $location }}</p>
                        <p class="mt-3 text-base leading-7 text-stone-600">Espaco reservado para mapa.</p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('contact.store') }}" class="rounded-lg border border-stone-200 bg-stone-50 p-6 shadow-lg lg:p-8">
                @csrf
                <input type="text" name="website" value="" tabindex="-1" autocomplete="off" class="hidden" aria-hidden="true">

                <div class="mb-6">
                    <p class="text-sm font-semibold uppercase tracking-[0.08em] text-emerald-800">Formulario</p>
                    <h2 class="mt-2 text-3xl font-semibold text-stone-950">Enviar mensagem</h2>
                    <p class="mt-3 text-base leading-7 text-stone-600">Indique email ou telefone para podermos responder.</p>
                </div>

                @if (session('status'))
                    <div class="mb-5 rounded-md bg-emerald-50 p-4 text-sm text-emerald-900">{{ session('status') }}</div>
                @endif

                @if ($errors->any())
                    <div class="mb-5 rounded-md bg-red-50 p-4 text-sm text-red-900">
                        <p class="font-semibold">Corrija os campos assinalados.</p>
                        @if ($errors->has('contact'))
                            <p class="mt-1">{{ $errors->first('contact') }}</p>
                        @endif
                    </div>
                @endif

                <div class="grid gap-5">
                    <label class="grid gap-2 text-base font-medium text-stone-800">
                        Nome
                        <input name="name" value="{{ old('name') }}" class="rounded-md border border-stone-300 bg-white px-3 py-3.5 text-base focus:border-emerald-700 focus:outline-none" required>
                        @error('name') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
                    </label>

                    <div class="grid gap-5 md:grid-cols-2">
                        <label class="grid gap-2 text-base font-medium text-stone-800">
                            Email
                            <input type="email" name="email" value="{{ old('email') }}" class="rounded-md border border-stone-300 bg-white px-3 py-3.5 text-base focus:border-emerald-700 focus:outline-none">
                            @error('email') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
                        </label>
                        <label class="grid gap-2 text-base font-medium text-stone-800">
                            Telefone
                            <input name="phone" value="{{ old('phone') }}" class="rounded-md border border-stone-300 bg-white px-3 py-3.5 text-base focus:border-emerald-700 focus:outline-none">
                            @error('phone') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
                        </label>
                    </div>

                    <label class="grid gap-2 text-base font-medium text-stone-800">
                        Assunto
                        <input name="subject" value="{{ old('subject') }}" class="rounded-md border border-stone-300 bg-white px-3 py-3.5 text-base focus:border-emerald-700 focus:outline-none">
                        @error('subject') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
                    </label>

                    <label class="grid gap-2 text-base font-medium text-stone-800">
                        Mensagem
                        <textarea name="message" rows="8" class="rounded-md border border-stone-300 bg-white px-3 py-3.5 text-base focus:border-emerald-700 focus:outline-none" required>{{ old('message') }}</textarea>
                        @error('message') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
                    </label>

                    <p class="rounded-md bg-amber-50 p-4 text-base leading-7 text-amber-950">Pode reservar diretamente por contacto. Este pedido nao confirma automaticamente a reserva.</p>

                    <button class="rounded-md bg-emerald-800 px-6 py-4 text-base font-semibold text-white hover:bg-emerald-900">Enviar mensagem</button>
                </div>
            </form>
        </div>
    </section>
@endsection
