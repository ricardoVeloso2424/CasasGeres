@props(['blockedDates'])

<div class="rounded-lg border border-sky-100 bg-sky-50 p-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-lg font-semibold text-sky-950">Datas indisponiveis</p>
            <p class="mt-2 text-base leading-7 text-sky-900">Consulta indicativa com base nos bloqueios registados.</p>
        </div>
        <span class="rounded-md bg-white px-3 py-1.5 text-sm font-semibold text-sky-900 ring-1 ring-sky-100">{{ $blockedDates->count() }}</span>
    </div>

    <div class="mt-5 grid gap-3 text-base text-sky-950">
        @forelse ($blockedDates as $blockedDate)
            <div class="rounded-md bg-white p-4 ring-1 ring-sky-100">
                <p class="font-semibold">{{ $blockedDate->starts_at->format('d/m/Y') }} a {{ $blockedDate->ends_at->format('d/m/Y') }}</p>
                <p class="mt-1 text-sm uppercase tracking-[0.08em] text-sky-700">{{ $blockedDate->calendarSource?->platform ?? $blockedDate->source ?? 'Calendario' }}</p>
            </div>
        @empty
            <p class="rounded-md bg-white p-4 text-sky-900 ring-1 ring-sky-100">Nao existem datas bloqueadas futuras registadas para esta unidade.</p>
        @endforelse
    </div>
</div>
