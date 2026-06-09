<section class="relative overflow-hidden bg-stone-950 text-white">
    <img src="https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?auto=format&fit=crop&w=1800&q=80" alt="Montanhas e vales verdes no Geres" loading="lazy" decoding="async" class="absolute inset-0 h-full w-full object-cover opacity-35">
    <div class="absolute inset-0 bg-gradient-to-r from-stone-950 via-stone-950/85 to-emerald-950/50"></div>

    <div class="relative mx-auto flex max-w-screen-2xl flex-col gap-8 px-4 py-20 sm:px-6 md:flex-row md:items-center md:justify-between lg:px-10 lg:py-24">
        <div class="max-w-4xl">
            <p class="text-sm font-semibold uppercase tracking-[0.08em] text-emerald-200">Reserva direta</p>
            <h2 class="mt-3 text-3xl font-semibold leading-tight sm:text-4xl lg:text-5xl">Confirme datas e condicoes connosco</h2>
            <p class="mt-5 text-base leading-8 text-stone-100 lg:text-lg">A disponibilidade apresentada e indicativa. A reserva fica segura depois de validarmos datas, unidade e detalhes por contacto direto.</p>
        </div>

        <div class="flex flex-wrap gap-3">
            <a href="{{ route('contact.index') }}" class="rounded-md bg-white px-6 py-3.5 text-base font-semibold text-stone-950 hover:bg-stone-100">Reservar / Contactar</a>
            <a href="https://wa.me/{{ config('site.whatsapp') }}?text=Ola%2C%20gostava%20de%20confirmar%20disponibilidade%20no%20Geres." class="rounded-md border border-white/50 px-6 py-3.5 text-base font-semibold text-white hover:bg-white/10">WhatsApp</a>
        </div>
    </div>
</section>
