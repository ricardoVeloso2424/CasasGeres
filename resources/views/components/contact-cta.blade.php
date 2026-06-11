@php
    $ctaWhatsapp = config('site.whatsapp');
@endphp

<section class="relative overflow-hidden bg-fir-950 text-white">
    <img src="https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?auto=format&fit=crop&w=1800&q=80" alt="" loading="lazy" decoding="async" class="absolute inset-0 h-full w-full object-cover opacity-25">
    <div class="absolute inset-0 bg-gradient-to-br from-fir-950 via-fir-950/90 to-fir-900/75"></div>

    <div class="relative mx-auto flex max-w-screen-2xl flex-col gap-8 px-4 py-16 sm:px-6 md:flex-row md:items-center md:justify-between lg:px-10 lg:py-20">
        <div class="reveal max-w-2xl">
            <p class="eyebrow text-amber-200"><span class="eyebrow-line bg-amber-300/60"></span>Reserva direta</p>
            <h2 class="mt-4 font-display text-3xl font-semibold leading-[1.1] tracking-tight text-balance sm:text-4xl">Confirme a sua estadia connosco</h2>
            <p class="mt-4 text-base leading-8 text-sand-100/85 lg:text-lg">Fale connosco para validar datas, unidade e condições antes de reservar.</p>
        </div>

        <div class="reveal reveal-d1 flex shrink-0 flex-col gap-3 sm:flex-row md:flex-col lg:flex-row">
            <a href="{{ route('contact.index') }}" class="btn btn-light">
                Contactar
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M4 10h12m0 0-4.5-4.5M16 10l-4.5 4.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
            @if ($ctaWhatsapp)
                <a href="https://wa.me/{{ $ctaWhatsapp }}?text=Ol%C3%A1%2C%20gostava%20de%20pedir%20disponibilidade%20no%20Ger%C3%AAs." class="btn btn-glass">
                    <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 0 0-8.5 15.2L2 22l4.9-1.4A10 10 0 1 0 12 2Zm5.1 14.1c-.2.6-1.2 1.1-1.7 1.2-.5.1-1 .1-1.7-.1-.4-.1-.9-.3-1.5-.6a9 9 0 0 1-3.6-3.7c-.3-.6-.5-1.1-.5-1.6 0-.7.4-1.4.8-1.7.2-.2.4-.2.5-.2h.4c.2 0 .3 0 .5.4l.6 1.5c.1.2 0 .4-.1.5l-.3.4c-.1.1-.2.3-.1.5.3.6.7 1.1 1.2 1.5.5.4.9.6 1.3.8.2.1.4.1.5-.1l.5-.6c.1-.2.3-.2.5-.1l1.4.7c.2.1.3.2.3.3.1.2.1.5 0 .9Z"/></svg>
                    WhatsApp
                </a>
            @endif
        </div>
    </div>
</section>
