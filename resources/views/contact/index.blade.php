@extends('layouts.app')

@section('content')
    @php
        $phone = config('site.phone');
        $phoneHref = config('site.phone_href');
        $whatsapp = config('site.whatsapp');
        $email = config('site.email');
        $location = config('site.location');
    @endphp

    <x-page-hero
        size="sm"
        image="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1900&q=80"
        srcset="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80 800w, https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1280&q=80 1280w, https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1900&q=80 1900w"
        image-alt="Água e montanhas no Gerês"
        eyebrow="Contactos"
        title="Reserva direta por contacto"
        lead="Envie uma mensagem, ligue ou fale por WhatsApp para confirmar disponibilidade. A reserva pelo site não fica automaticamente confirmada."
        divider-to="white"
    />

    <section class="bg-white">
        <div class="mx-auto grid max-w-screen-2xl gap-10 px-4 py-16 sm:px-6 lg:grid-cols-[0.9fr_1.1fr] lg:gap-14 lg:px-10 lg:py-24">
            <div class="grid gap-8">
                <div class="reveal">
                    <x-section-heading
                        eyebrow="Fale connosco"
                        title="Confirmamos tudo diretamente"
                        description="Partilhe datas, número de hóspedes e unidade pretendida. Respondemos com disponibilidade e próximos passos."
                    />
                </div>

                <div class="reveal relative overflow-hidden rounded-2xl border border-sand-200 bg-sand-50 p-6 lg:p-7">
                    <svg class="pointer-events-none absolute inset-0 h-full w-full text-fir-700" viewBox="0 0 480 260" fill="none" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
                        <g stroke="currentColor" stroke-width="1.5" opacity="0.08">
                            <path d="M-20 60c60-30 130-20 180 5s120 40 180 15 110-25 160 0"/>
                            <path d="M-20 100c60-30 130-20 180 5s120 40 180 15 110-25 160 0"/>
                            <path d="M-20 140c60-30 130-20 180 5s120 40 180 15 110-25 160 0"/>
                            <path d="M-20 180c60-30 130-20 180 5s120 40 180 15 110-25 160 0"/>
                            <path d="M-20 220c60-30 130-20 180 5s120 40 180 15 110-25 160 0"/>
                        </g>
                        <g stroke="currentColor" opacity="0.12">
                            <circle cx="370" cy="105" r="28" stroke-width="1.5"/>
                            <circle cx="370" cy="105" r="48" stroke-width="1.2"/>
                            <circle cx="370" cy="105" r="70" stroke-width="1"/>
                        </g>
                    </svg>

                    <div class="relative flex items-start gap-4">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-white text-fir-700 shadow-sm ring-1 ring-inset ring-fir-700/10">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 21s7-5.6 7-11a7 7 0 1 0-14 0c0 5.4 7 11 7 11Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><circle cx="12" cy="10" r="2.4" stroke="currentColor" stroke-width="1.7"/></svg>
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold uppercase tracking-[0.12em] text-fir-800">Localização</p>
                            <p class="mt-1 font-display text-2xl font-semibold text-stone-950">{{ $location }}</p>
                            <p class="mt-1 text-base leading-7 text-stone-600">Casas em ambiente natural no Gerês, perto de trilhos e cascatas.</p>
                        </div>
                    </div>
                    <a href="https://www.google.com/maps/search/?api=1&amp;query={{ urlencode($location) }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline relative mt-5 bg-white">
                        Ver no Google Maps
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M14 5h5v5M19 5l-7 7M11 5H7a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </a>
                </div>

                <div class="reveal reveal-d1 grid gap-4">
                    @if ($whatsapp)
                        <a href="https://wa.me/{{ $whatsapp }}?text=Ol%C3%A1%2C%20gostava%20de%20pedir%20disponibilidade%20no%20Ger%C3%AAs." class="group flex items-center gap-4 rounded-2xl bg-[#1f9d57] p-5 text-white shadow-sm shadow-fir-950/10 transition hover:-translate-y-0.5 hover:bg-[#178045] hover:shadow-lg lg:p-6">
                            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-white/15 ring-1 ring-inset ring-white/25">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 0 0-8.5 15.2L2 22l4.9-1.4A10 10 0 1 0 12 2Zm5.1 14.1c-.2.6-1.2 1.1-1.7 1.2-.5.1-1 .1-1.7-.1-.4-.1-.9-.3-1.5-.6a9 9 0 0 1-3.6-3.7c-.3-.6-.5-1.1-.5-1.6 0-.7.4-1.4.8-1.7.2-.2.4-.2.5-.2h.4c.2 0 .3 0 .5.4l.6 1.5c.1.2 0 .4-.1.5l-.3.4c-.1.1-.2.3-.1.5.3.6.7 1.1 1.2 1.5.5.4.9.6 1.3.8.2.1.4.1.5-.1l.5-.6c.1-.2.3-.2.5-.1l1.4.7c.2.1.3.2.3.3.1.2.1.5 0 .9Z"/></svg>
                            </span>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold uppercase tracking-[0.1em] text-white/85">WhatsApp · contacto direto</p>
                                <p class="mt-1 text-xl font-semibold">Enviar mensagem agora</p>
                            </div>
                            <svg class="ml-auto h-5 w-5 shrink-0 transition-transform group-hover:translate-x-1" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M4 10h12m0 0-4.5-4.5M16 10l-4.5 4.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </a>
                    @endif

                    @if ($phone)
                        <a href="tel:{{ $phoneHref }}" class="group flex items-center gap-4 rounded-2xl border border-sand-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-fir-200 hover:shadow-md lg:p-6">
                            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-fir-600/10 text-fir-700 ring-1 ring-inset ring-fir-700/15">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 4h3l1.5 4-2 1.5a11 11 0 0 0 5 5l1.5-2 4 1.5v3a2 2 0 0 1-2 2A15 15 0 0 1 3 6a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>
                            </span>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold uppercase tracking-[0.1em] text-fir-800">Telefone</p>
                                <p class="mt-1 text-xl font-semibold text-stone-950">{{ $phone }}</p>
                            </div>
                        </a>
                    @endif

                    @if ($email)
                        <a href="mailto:{{ $email }}" class="group flex items-center gap-4 rounded-2xl border border-sand-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-fir-200 hover:shadow-md lg:p-6">
                            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-fir-600/10 text-fir-700 ring-1 ring-inset ring-fir-700/15">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="m4 7 8 6 8-6" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>
                            </span>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold uppercase tracking-[0.1em] text-fir-800">Email</p>
                                <p class="mt-1 break-all text-xl font-semibold text-stone-950">{{ $email }}</p>
                            </div>
                        </a>
                    @endif

                    <div class="note-info flex items-start gap-3">
                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-river-600" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.7"/><path d="M12 11v5M12 7.5h.01" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                        <span>A disponibilidade apresentada nas unidades é indicativa. A confirmação final é feita por contacto direto depois de validarmos os calendários.</span>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('contact.store') }}" class="reveal reveal-d1 self-start overflow-hidden rounded-2xl border border-sand-200 bg-white shadow-xl shadow-fir-950/10">
                @csrf
                <input type="text" name="website" value="" tabindex="-1" autocomplete="off" class="hidden" aria-hidden="true">

                <div class="border-b border-sand-200/70 bg-gradient-to-br from-fir-50 to-white p-6 lg:p-8">
                    <p class="eyebrow"><span class="eyebrow-line"></span>Formulário</p>
                    <h2 class="mt-2 font-display text-2xl font-semibold tracking-tight text-stone-950 sm:text-3xl">Enviar mensagem</h2>
                    <p class="mt-2 text-base leading-7 text-stone-600">Indique email ou telefone para podermos responder.</p>
                </div>

                <div class="p-6 lg:p-8">
                    @if (session('status'))
                        <div class="mb-5 flex items-start gap-3 rounded-xl bg-fir-50 p-4 text-base leading-7 text-fir-900 ring-1 ring-inset ring-fir-100">
                            <svg class="mt-0.5 h-5 w-5 shrink-0 text-fir-600" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.7"/><path d="m8 12 2.5 2.5L16 9" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <span>{{ session('status') }}</span>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-5 rounded-xl bg-red-50 p-4 text-base leading-7 text-red-900 ring-1 ring-inset ring-red-100">
                            <p class="font-semibold">Corrija os campos assinalados.</p>
                            @if ($errors->has('contact'))
                                <p class="mt-1">{{ $errors->first('contact') }}</p>
                            @endif
                        </div>
                    @endif

                    <div class="grid gap-5">
                        <label class="field-label">
                            Nome
                            <input name="name" value="{{ old('name') }}" autocomplete="name" class="field" required>
                            @error('name') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
                        </label>

                        <div class="grid gap-5 md:grid-cols-2">
                            <label class="field-label">
                                Email
                                <input type="email" name="email" value="{{ old('email') }}" autocomplete="email" class="field">
                                @error('email') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
                            </label>
                            <label class="field-label">
                                Telefone
                                <input name="phone" value="{{ old('phone') }}" autocomplete="tel" class="field">
                                @error('phone') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
                            </label>
                        </div>

                        <label class="field-label">
                            Assunto
                            <input name="subject" value="{{ old('subject') }}" class="field">
                            @error('subject') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
                        </label>

                        <label class="field-label">
                            Mensagem
                            <textarea name="message" rows="8" class="field" required>{{ old('message') }}</textarea>
                            @error('message') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
                        </label>

                        <p class="rounded-xl border border-amber-100 bg-amber-50/80 p-4 text-sm leading-7 text-amber-950">Pode reservar diretamente por contacto. Este pedido não confirma automaticamente a reserva.</p>

                        <button class="btn btn-primary btn-block py-4">Enviar mensagem</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection
