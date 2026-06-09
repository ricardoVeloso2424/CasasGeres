@extends('layouts.app')

@section('content')
    @php
        $phoneHref = config('site.phone_href');
        $whatsappNumber = config('site.whatsapp');
        $email = config('site.email');
        $coverImage = $unit->coverImage() ?? $house->coverImage();
        $cover = $coverImage?->url;
    @endphp

    <section class="relative min-h-[500px] overflow-hidden bg-stone-950 text-white lg:min-h-[620px]">
        @if ($cover)
            <img src="{{ $cover }}" alt="{{ $coverImage?->alt ?? $unit->name }}" fetchpriority="high" decoding="async" class="absolute inset-0 h-full w-full object-cover opacity-55">
        @else
            <x-image-placeholder :title="$unit->name" class="absolute inset-0 h-full w-full opacity-55" />
        @endif
        <div class="absolute inset-0 bg-gradient-to-r from-stone-950 via-stone-950/75 to-stone-950/20"></div>

        <div class="relative mx-auto max-w-screen-2xl px-4 py-24 sm:px-6 lg:px-10 lg:py-32">
            <a href="{{ route('houses.show', $house) }}" class="text-base font-semibold text-emerald-100 hover:text-white">{{ $house->name }}</a>
            <div class="mt-5 flex flex-wrap gap-2">
                <span class="rounded-md bg-white px-3 py-1.5 text-sm font-semibold text-stone-950">{{ $unit->type }}</span>
                <span class="rounded-md bg-emerald-100 px-3 py-1.5 text-sm font-semibold text-emerald-950">Ate {{ $unit->capacity }} hospedes</span>
                @if ($unit->base_price)
                    <span class="rounded-md bg-amber-100 px-3 py-1.5 text-sm font-semibold text-amber-950">desde {{ number_format($unit->base_price, 0) }} EUR/noite</span>
                @endif
            </div>

            <h1 class="mt-6 max-w-5xl text-4xl font-semibold leading-tight sm:text-5xl lg:text-6xl">{{ $unit->name }}</h1>
            <p class="mt-6 max-w-3xl text-base leading-8 text-stone-100 sm:text-lg lg:text-xl lg:leading-9">{{ $unit->short_description }}</p>

            <div class="mt-10 flex flex-wrap gap-4">
                <a href="#pedido-reserva" class="rounded-md bg-white px-6 py-3.5 text-base font-semibold text-stone-950 hover:bg-stone-100">Pedir reserva</a>
                <a href="https://wa.me/{{ $whatsappNumber }}?text=Ola%2C%20gostava%20de%20confirmar%20disponibilidade%20para%20{{ rawurlencode($unit->name) }}." class="rounded-md border border-white/50 px-6 py-3.5 text-base font-semibold text-white hover:bg-white/10">WhatsApp</a>
            </div>
        </div>
    </section>

    <section class="bg-white">
        <div class="mx-auto max-w-screen-2xl px-4 py-16 sm:px-6 lg:px-10 lg:py-24">
            <x-image-gallery :photos="$unit->photos" :fallback="$cover" :title="$unit->name" />

            <div class="mt-14 grid gap-10 lg:grid-cols-[minmax(0,1fr)_460px] lg:items-start lg:gap-12">
                <div class="grid gap-12">
                    <section>
                        <x-section-heading
                            eyebrow="Resumo"
                            :title="$unit->name"
                            :description="$unit->description"
                        />

                        <div class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                            <div class="rounded-lg border border-stone-200 bg-stone-50 p-5">
                                <p class="text-3xl font-semibold text-stone-950">{{ $unit->capacity }}</p>
                                <p class="mt-2 text-base text-stone-600">hospedes</p>
                            </div>
                            <div class="rounded-lg border border-stone-200 bg-stone-50 p-5">
                                <p class="text-3xl font-semibold text-stone-950">{{ $unit->bedrooms }}</p>
                                <p class="mt-2 text-base text-stone-600">quarto(s)</p>
                            </div>
                            <div class="rounded-lg border border-stone-200 bg-stone-50 p-5">
                                <p class="text-3xl font-semibold text-stone-950">{{ $unit->bathrooms }}</p>
                                <p class="mt-2 text-base text-stone-600">banho(s)</p>
                            </div>
                            <div class="rounded-lg border border-stone-200 bg-stone-50 p-5">
                                <p class="text-3xl font-semibold text-stone-950">{{ $unit->type }}</p>
                                <p class="mt-2 text-base text-stone-600">tipologia</p>
                            </div>
                        </div>
                    </section>

                    <section>
                        <h2 class="text-3xl font-semibold text-stone-950">Comodidades</h2>
                        <div class="mt-6 flex flex-wrap gap-3">
                            @forelse ($unit->amenities as $amenity)
                                <span class="rounded-md bg-stone-100 px-4 py-2.5 text-base font-medium text-stone-800 ring-1 ring-stone-200">{{ $amenity->name }}</span>
                            @empty
                                <span class="rounded-md bg-stone-100 px-4 py-2.5 text-base font-medium text-stone-800 ring-1 ring-stone-200">Comodidades a confirmar</span>
                            @endforelse
                        </div>
                    </section>

                    <x-availability-list :blocked-dates="$unit->blockedDates" />

                    <section class="rounded-lg border border-amber-100 bg-amber-50 p-6">
                        <h2 class="text-xl font-semibold text-amber-950">Aviso de disponibilidade</h2>
                        <p class="mt-3 text-base leading-7 text-amber-950">As datas apresentadas sao indicativas. O pedido enviado pelo site nao confirma automaticamente a reserva; a confirmacao final e feita por contacto direto.</p>
                    </section>

                    @if ($unit->rules)
                        <section>
                            <h2 class="text-3xl font-semibold text-stone-950">Regras</h2>
                            <p class="mt-4 text-base leading-8 text-stone-700">{{ $unit->rules }}</p>
                        </section>
                    @endif

                    <section class="grid gap-4 rounded-lg border border-stone-200 bg-stone-50 p-6 sm:grid-cols-3">
                        <a href="tel:{{ $phoneHref }}" class="rounded-md bg-white px-5 py-3.5 text-center text-base font-semibold text-stone-800 ring-1 ring-stone-200 hover:text-emerald-800">Ligar</a>
                        <a href="https://wa.me/{{ $whatsappNumber }}?text=Ola%2C%20gostava%20de%20confirmar%20disponibilidade%20para%20{{ rawurlencode($unit->name) }}." class="rounded-md bg-emerald-800 px-5 py-3.5 text-center text-base font-semibold text-white hover:bg-emerald-900">WhatsApp</a>
                        <a href="mailto:{{ $email }}?subject=Pedido%20de%20reserva%20{{ rawurlencode($unit->name) }}" class="rounded-md bg-white px-5 py-3.5 text-center text-base font-semibold text-stone-800 ring-1 ring-stone-200 hover:text-emerald-800">Email</a>
                    </section>
                </div>

                <aside
                    id="pedido-reserva"
                    class="rounded-lg border border-stone-200 bg-white p-6 shadow-xl lg:sticky lg:top-28 lg:p-7"
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
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.08em] text-emerald-800">Pedido direto</p>
                            <h2 class="mt-2 text-3xl font-semibold text-stone-950">Pedir reserva</h2>
                        </div>
                        @if ($unit->base_price)
                            <span class="rounded-md bg-emerald-50 px-3 py-2 text-base font-semibold text-emerald-950">{{ number_format($unit->base_price, 0) }} EUR+</span>
                        @endif
                    </div>

                    <p class="mt-4 text-base leading-7 text-stone-600">Preencha os dados e entraremos em contacto para confirmar disponibilidade e condicoes.</p>

                    <form method="POST" action="{{ route('booking-requests.store') }}" class="mt-7 grid gap-5">
                        @csrf
                        <input type="hidden" name="rental_unit_id" value="{{ $unit->id }}">
                        <input type="text" name="website" value="" tabindex="-1" autocomplete="off" class="hidden" aria-hidden="true">

                        @if (session('booking_status'))
                            <div class="rounded-md bg-emerald-50 p-4 text-base leading-7 text-emerald-900">{{ session('booking_status') }}</div>
                        @endif

                        @if ($errors->any())
                            <div class="rounded-md bg-red-50 p-4 text-base leading-7 text-red-900">
                                <p class="font-semibold">Corrija os campos assinalados.</p>
                                @if ($errors->has('contact'))
                                    <p class="mt-1">{{ $errors->first('contact') }}</p>
                                @endif
                            </div>
                        @endif

                        <label class="grid gap-2 text-base font-medium text-stone-800">
                            Nome
                            <input name="name" value="{{ old('name') }}" class="rounded-md border border-stone-300 px-3 py-3.5 text-base focus:border-emerald-700 focus:outline-none" required>
                            @error('name') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
                        </label>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <label class="grid gap-2 text-base font-medium text-stone-800">
                                Email
                                <input type="email" name="email" value="{{ old('email') }}" class="rounded-md border border-stone-300 px-3 py-3.5 text-base focus:border-emerald-700 focus:outline-none">
                                @error('email') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
                            </label>

                            <label class="grid gap-2 text-base font-medium text-stone-800">
                                Telefone
                                <input name="phone" value="{{ old('phone') }}" class="rounded-md border border-stone-300 px-3 py-3.5 text-base focus:border-emerald-700 focus:outline-none">
                                @error('phone') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
                            </label>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <label class="grid gap-2 text-base font-medium text-stone-800">
                                Check-in
                                <input type="date" name="check_in" min="{{ today()->toDateString() }}" x-model="checkIn" class="rounded-md border border-stone-300 px-3 py-3.5 text-base focus:border-emerald-700 focus:outline-none" required>
                                @error('check_in') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
                            </label>

                            <label class="grid gap-2 text-base font-medium text-stone-800">
                                Check-out
                                <input type="date" name="check_out" min="{{ today()->toDateString() }}" x-model="checkOut" class="rounded-md border border-stone-300 px-3 py-3.5 text-base focus:border-emerald-700 focus:outline-none" required>
                                @error('check_out') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
                            </label>
                        </div>

                        <label class="grid gap-2 text-base font-medium text-stone-800">
                            Numero de hospedes
                            <input type="number" name="guests" value="{{ old('guests') }}" min="1" max="{{ $unit->capacity }}" class="rounded-md border border-stone-300 px-3 py-3.5 text-base focus:border-emerald-700 focus:outline-none">
                            @error('guests') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
                        </label>

                        <label class="grid gap-2 text-base font-medium text-stone-800">
                            Mensagem
                            <textarea name="message" rows="5" class="rounded-md border border-stone-300 px-3 py-3.5 text-base focus:border-emerald-700 focus:outline-none">{{ old('message') }}</textarea>
                            @error('message') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
                        </label>

                        @error('rental_unit_id') <span class="text-sm text-red-700">{{ $message }}</span> @enderror

                        <button class="rounded-md bg-emerald-800 px-5 py-4 text-base font-semibold text-white hover:bg-emerald-900">Enviar pedido</button>
                    </form>

                    <div class="mt-5 grid gap-3 border-t border-stone-200 pt-5">
                        <a x-bind:href="whatsappHref()" class="rounded-md bg-stone-950 px-5 py-3.5 text-center text-base font-semibold text-white hover:bg-stone-800">Pedir por WhatsApp</a>
                        <a href="tel:{{ $phoneHref }}" class="rounded-md border border-stone-300 px-5 py-3.5 text-center text-base font-semibold text-stone-800 hover:border-emerald-700 hover:text-emerald-800">Ligar</a>
                        <a href="mailto:{{ $email }}?subject=Pedido%20de%20reserva%20{{ rawurlencode($unit->name) }}" class="rounded-md border border-stone-300 px-5 py-3.5 text-center text-base font-semibold text-stone-800 hover:border-emerald-700 hover:text-emerald-800">Enviar email</a>
                    </div>
                </aside>
            </div>
        </div>
    </section>
@endsection
