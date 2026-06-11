@props(['blockedDates'])

<div class="rounded-2xl border border-river-100 bg-river-50/80 p-6 lg:p-7">
    <div class="flex items-start justify-between gap-4">
        <div class="flex items-start gap-3">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-river-700 ring-1 ring-inset ring-river-100">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="4" y="5" width="16" height="15" rx="2" stroke="currentColor" stroke-width="1.7"/><path d="M4 9h16M8 3v3M16 3v3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
            </span>
            <div>
                <h2 class="text-lg font-semibold text-river-950">Datas indisponíveis</h2>
                <p class="mt-1 text-base leading-7 text-river-900/80">Consulta indicativa com base nos bloqueios registados.</p>
            </div>
        </div>
        <span class="shrink-0 rounded-full bg-white px-3 py-1.5 text-sm font-semibold text-river-900 ring-1 ring-inset ring-river-100">{{ $blockedDates->count() }}</span>
    </div>

    <div class="mt-5 grid gap-3 text-base text-river-950 sm:grid-cols-2">
        @forelse ($blockedDates as $blockedDate)
            <div class="rounded-xl bg-white p-4 ring-1 ring-inset ring-river-100">
                <p class="font-semibold">{{ $blockedDate->starts_at->format('d/m/Y') }} a {{ $blockedDate->ends_at->format('d/m/Y') }}</p>
                <p class="mt-1 text-xs font-medium uppercase tracking-[0.08em] text-river-700">{{ $blockedDate->calendarSource?->platform ?? $blockedDate->source ?? 'Calendário' }}</p>
            </div>
        @empty
            <p class="rounded-xl bg-white p-4 text-river-900 ring-1 ring-inset ring-river-100 sm:col-span-2">Não existem datas bloqueadas futuras registadas para esta unidade.</p>
        @endforelse
    </div>
</div>
