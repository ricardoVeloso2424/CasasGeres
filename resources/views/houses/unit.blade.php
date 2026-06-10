@extends('layouts.app')

@section('content')
    @php
        $phoneHref = config('site.phone_href');
        $whatsappNumber = config('site.whatsapp');
        $email = config('site.email');
        $coverImage = $unit->coverImage() ?? $house->coverImage();
        $cover = $coverImage?->url;
    @endphp

    <section class="relative isolate flex min-h-[480px] items-center overflow-hidden bg-stone-950 text-white lg:min-h-[600px]">
        @if ($cover)
            <img src="{{ $cover }}" alt="{{ $coverImage?->alt ?? $unit->name }}" fetchpriority="high" decoding="async" class="absolute inset-0 -z-10 h-full w-full object-cover">
        @else
            <x-image-placeholder :title="$unit->name" class="absolute inset-0 -z-10 h-full w-full" />
        @endif
        <div class="absolute inset-0 -z-10 bg-gradient-to-br from-stone-950/95 via-stone-950/78 to-stone-950/35"></div>
        <div class="absolute inset-0 -z-10 bg-gradient-to-t from-stone-950/85 via-transparent to-transparent"></div>

        <div class="mx-auto w-full max-w-screen-2xl px-4 py-20 sm:px-6 lg:px-10 lg:py-24">
            <a href="{{ route('houses.show', $house) }}" class="animate-rise d1 inline-flex items-center gap-2 rounded-full bg-white/10 px-3.5 py-1.5 text-sm font-semibold text-white ring-1 ring-inset ring-white/25 backdrop-blur transition hover:bg-white/20">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M16 10H4m0 0 4.5-4.5M4 10l4.5 4.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                {{ $house->name }}
            </a>

            <div class="animate-rise d2 mt-6 flex flex-wrap gap-2">
                <span class="chip chip-light">{{ $unit->type }}</span>
                <span class="chip chip-glass">Ate {{ $unit->capacity }} hospedes</span>
                @if ($unit->base_price)
                    <span class="chip chip-glass">desde {{ number_format($unit->base_price, 0) }} EUR/noite</span>
                @endif
            </div>

            <h1 class="animate-rise d2 mt-5 max-w-4xl font-display text-4xl font-semibold leading-[1.05] tracking-tight text-balance sm:text-5xl lg:text-6xl">{{ $unit->name }}</h1>
            <p class="animate-rise d3 mt-6 max-w-2xl text-base leading-8 text-stone-200 sm:text-lg lg:text-xl lg:leading-9">{{ $unit->short_description }}</p>

            <div class="animate-rise d4 mt-9">
                <a href="#pedido-reserva" class="btn btn-light">
                    Pedir reserva
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M4 10h12m0 0-4.5-4.5M16 10l-4.5 4.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
            </div>
        </div>
    </section>

    <section class="bg-white">
        <div class="mx-auto max-w-screen-2xl px-4 py-16 sm:px-6 lg:px-10 lg:py-24">
            <div class="reveal">
                <x-image-gallery :photos="$unit->photos" :fallback="$cover" :title="$unit->name" />
            </div>

            <div class="mt-14 grid gap-10 lg:grid-cols-[minmax(0,1fr)_460px] lg:items-start lg:gap-12">
                <div class="grid gap-12">
                    <section class="reveal">
                        <x-section-heading
                            eyebrow="Resumo"
                            :title="$unit->name"
                            :description="$unit->description"
                        />

                        <div class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                            @php
                                $stats = [
                                    ['value' => $unit->capacity, 'label' => 'hospedes', 'icon' => 'M9 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM3 20a6 6 0 0 1 12 0M16 8a3 3 0 1 0 0-6M15 20a6 6 0 0 1 6-4.5'],
                                    ['value' => $unit->bedrooms, 'label' => 'quarto(s)', 'icon' => 'M3 18v-5h18v5M3 13V8a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v5M3 18v2M21 18v2'],
                                    ['value' => $unit->bathrooms, 'label' => 'banho(s)', 'icon' => 'M4 12h16v3a4 4 0 0 1-4 4H8a4 4 0 0 1-4-4v-3ZM6 12V6a2 2 0 0 1 2-2c1 0 1.6.6 2 1.5'],
                                    ['value' => $unit->type, 'label' => 'tipologia', 'icon' => 'M3 11 12 4l9 7M5 10v9h14v-9'],
                                ];
                            @endphp
                            @foreach ($stats as $stat)
                                <div class="rounded-2xl border border-stone-200/80 bg-stone-50/70 p-5">
                                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-700/10 text-emerald-700 ring-1 ring-inset ring-emerald-700/15">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="{{ $stat['icon'] }}" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </span>
                                    <p class="mt-4 font-display text-3xl font-semibold text-stone-950">{{ $stat['value'] }}</p>
                                    <p class="mt-1 text-base text-stone-600">{{ $stat['label'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </section>

                    <section class="reveal">
                        <h2 class="font-display text-2xl font-semibold tracking-tight text-stone-950 sm:text-3xl">Comodidades</h2>
                        <div class="mt-6 flex flex-wrap gap-2.5">
                            @forelse ($unit->amenities as $amenity)
                                <span class="inline-flex items-center gap-2 rounded-full bg-stone-50 px-4 py-2.5 text-base font-medium text-stone-800 ring-1 ring-inset ring-stone-200">
                                    <svg class="h-4 w-4 text-emerald-600" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="m4 10.5 3.5 3.5L16 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    {{ $amenity->name }}
                                </span>
                            @empty
                                <span class="rounded-full bg-stone-50 px-4 py-2.5 text-base font-medium text-stone-800 ring-1 ring-inset ring-stone-200">Comodidades a confirmar</span>
                            @endforelse
                        </div>
                    </section>

                    <div class="reveal">
                        <x-availability-list :blocked-dates="$unit->blockedDates" />
                    </div>

                    <section class="reveal note-amber flex items-start gap-3">
                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-amber-600" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 4 3 19h18L12 4Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M12 10v4M12 17h.01" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                        <div>
                            <h2 class="text-lg font-semibold text-amber-950">Aviso de disponibilidade</h2>
                            <p class="mt-2 text-base leading-7 text-amber-950">As datas apresentadas sao indicativas. O pedido enviado pelo site nao confirma automaticamente a reserva; a confirmacao final e feita por contacto direto.</p>
                        </div>
                    </section>

                    @if ($unit->rules)
                        <section class="reveal">
                            <h2 class="font-display text-2xl font-semibold tracking-tight text-stone-950 sm:text-3xl">Regras</h2>
                            <p class="mt-4 text-base leading-8 text-stone-700">{{ $unit->rules }}</p>
                        </section>
                    @endif
                </div>

                <aside
                    id="pedido-reserva"
                    class="scroll-mt-24 overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-xl shadow-stone-900/10 lg:sticky lg:top-24"
                    x-data="{
                        checkIn: @js(old('check_in', '')),
                        checkOut: @js(old('check_out', '')),
                        unitName: @js($unit->name),
                        houseName: @js($house->name),
                        whatsappNumber: @js($whatsappNumber),
                        whatsappHref() {
                            let message = `Ola, estou interessado em reservar ${this.unitName} da ${this.houseName}. Podem confirmar disponibilidade?`;

                            if (this.checkIn && this.checkOut) {
                                message = `Ola, estou interessado em reservar ${this.unitName} da ${this.houseName} de ${this.checkIn} a ${this.checkOut}. Podem confirmar disponibilidade?`;
                            }

                            return `https://wa.me/${this.whatsappNumber}?text=${encodeURIComponent(message)}`;
                        },
                    }"
                >
                    <div class="flex items-start justify-between gap-4 border-b border-stone-100 bg-gradient-to-br from-emerald-50/70 to-white p-6 lg:p-7">
                        <div>
                            <p class="eyebrow"><span class="eyebrow-line"></span>Pedido direto</p>
                            <h2 class="mt-2 font-display text-2xl font-semibold tracking-tight text-stone-950 sm:text-3xl">Pedir reserva</h2>
                        </div>
                        @if ($unit->base_price)
                            <span class="shrink-0 chip chip-emerald">{{ number_format($unit->base_price, 0) }} EUR+</span>
                        @endif
                    </div>

                    <div class="p-6 lg:p-7">
                        <p class="text-base leading-7 text-stone-600">Preencha os dados e entraremos em contacto para confirmar disponibilidade e condicoes.</p>

                        <form method="POST" action="{{ route('booking-requests.store') }}" class="mt-7 grid gap-5">
                            @csrf
                            <input type="hidden" name="rental_unit_id" value="{{ $unit->id }}">
                            <input type="text" name="website" value="" tabindex="-1" autocomplete="off" class="hidden" aria-hidden="true">

                            @if (session('booking_status'))
                                <div class="flex items-start gap-3 rounded-xl bg-emerald-50 p-4 text-base leading-7 text-emerald-900 ring-1 ring-inset ring-emerald-100">
                                    <svg class="mt-0.5 h-5 w-5 shrink-0 text-emerald-600" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.7"/><path d="m8 12 2.5 2.5L16 9" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    <span>{{ session('booking_status') }}</span>
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="rounded-xl bg-red-50 p-4 text-base leading-7 text-red-900 ring-1 ring-inset ring-red-100">
                                    <p class="font-semibold">Corrija os campos assinalados.</p>
                                    @if ($errors->has('contact'))
                                        <p class="mt-1">{{ $errors->first('contact') }}</p>
                                    @endif
                                </div>
                            @endif

                            <label class="field-label">
                                Nome
                                <input name="name" value="{{ old('name') }}" autocomplete="name" class="field" required>
                                @error('name') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
                            </label>

                            <div class="grid gap-4 sm:grid-cols-2">
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

                            <div class="grid gap-4 sm:grid-cols-2">
                                <label class="field-label">
                                    Check-in
                                    <input type="date" name="check_in" min="{{ today()->toDateString() }}" x-model="checkIn" class="field" required>
                                    @error('check_in') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
                                </label>

                                <label class="field-label">
                                    Check-out
                                    <input type="date" name="check_out" min="{{ today()->toDateString() }}" x-model="checkOut" class="field" required>
                                    @error('check_out') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
                                </label>
                            </div>

                            <label class="field-label">
                                Numero de hospedes
                                <input type="number" name="guests" value="{{ old('guests') }}" min="1" max="{{ $unit->capacity }}" class="field">
                                @error('guests') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
                            </label>

                            <label class="field-label">
                                Mensagem
                                <textarea name="message" rows="5" class="field">{{ old('message') }}</textarea>
                                @error('message') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
                            </label>

                            @error('rental_unit_id') <span class="text-sm text-red-700">{{ $message }}</span> @enderror

                            <button class="btn btn-primary btn-block py-4">Enviar pedido</button>

                            <p class="inline-flex items-center justify-center gap-2 text-sm text-stone-500">
                                <svg class="h-4 w-4 text-emerald-600" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="5" y="11" width="14" height="9" rx="2" stroke="currentColor" stroke-width="1.7"/><path d="M8 11V8a4 4 0 0 1 8 0v3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                                Sem pagamento online. Confirmamos por contacto direto.
                            </p>
                        </form>

                        <div class="mt-6 grid gap-3 border-t border-stone-200 pt-6">
                            <a x-bind:href="whatsappHref()" class="btn btn-whatsapp btn-block">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 0 0-8.5 15.2L2 22l4.9-1.4A10 10 0 1 0 12 2Zm5.1 14.1c-.2.6-1.2 1.1-1.7 1.2-.5.1-1 .1-1.7-.1-.4-.1-.9-.3-1.5-.6a9 9 0 0 1-3.6-3.7c-.3-.6-.5-1.1-.5-1.6 0-.7.4-1.4.8-1.7.2-.2.4-.2.5-.2h.4c.2 0 .3 0 .5.4l.6 1.5c.1.2 0 .4-.1.5l-.3.4c-.1.1-.2.3-.1.5.3.6.7 1.1 1.2 1.5.5.4.9.6 1.3.8.2.1.4.1.5-.1l.5-.6c.1-.2.3-.2.5-.1l1.4.7c.2.1.3.2.3.3.1.2.1.5 0 .9Z"/></svg>
                                Pedir por WhatsApp
                            </a>
                            <div class="grid grid-cols-2 gap-3">
                                <a href="tel:{{ $phoneHref }}" class="btn btn-outline">Ligar</a>
                                <a href="mailto:{{ $email }}?subject=Pedido%20de%20reserva%20{{ rawurlencode($unit->name) }}" class="btn btn-outline">Email</a>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>
@endsection
