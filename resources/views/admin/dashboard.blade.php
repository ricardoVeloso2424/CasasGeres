@extends('layouts.admin')

@section('header', 'Dashboard')

@section('content')
    @php
        $cards = [
            ['label' => 'Casas', 'value' => $counts['houses']],
            ['label' => 'Unidades alugaveis', 'value' => $counts['rentalUnits']],
            ['label' => 'Pedidos pendentes', 'value' => $counts['pendingBookingRequests'], 'route' => route('admin.booking-requests.index', ['status' => 'pending'])],
            ['label' => 'Mensagens nao lidas', 'value' => $counts['unreadContactMessages'], 'route' => route('admin.contact-messages.index', ['status' => 'unread'])],
            ['label' => 'Atividades ativas', 'value' => $counts['activeActivities']],
            ['label' => 'Calendarios ativos', 'value' => $counts['activeCalendarSources'], 'route' => route('admin.calendar-sources.index', ['active' => '1'])],
            ['label' => 'Datas bloqueadas futuras', 'value' => $counts['futureBlockedDates'], 'route' => route('admin.blocked-dates.index')],
        ];

        $links = [
            ['label' => 'Gerir casas', 'route' => route('admin.houses.index')],
            ['label' => 'Gerir unidades', 'route' => route('admin.rental-units.index')],
            ['label' => 'Pedidos de reserva', 'route' => route('admin.booking-requests.index')],
            ['label' => 'Mensagens', 'route' => route('admin.contact-messages.index')],
            ['label' => 'Atividades', 'route' => route('admin.activities.index')],
            ['label' => 'Comodidades', 'route' => route('admin.amenities.index')],
            ['label' => 'Gerir iCal sources', 'route' => route('admin.calendar-sources.index')],
            ['label' => 'Gerir datas bloqueadas', 'route' => route('admin.blocked-dates.index')],
        ];

        $bookingStatusLabels = [
            'pending' => 'Pendente',
            'contacted' => 'Contactado',
            'confirmed' => 'Confirmado',
            'cancelled' => 'Cancelado',
        ];

        $messageStatusLabels = [
            'unread' => 'Nao lida',
            'read' => 'Lida',
            'archived' => 'Arquivada',
        ];
    @endphp

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ($cards as $card)
            <article class="rounded-lg border border-stone-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-stone-600">{{ $card['label'] }}</p>
                <p class="mt-3 text-3xl font-semibold text-stone-950">{{ $card['value'] }}</p>
                @isset($card['route'])
                    <a href="{{ $card['route'] }}" class="mt-3 inline-block text-sm font-semibold text-emerald-800 hover:text-emerald-950">Ver lista</a>
                @endisset
            </article>
        @endforeach
    </section>

    <section class="mt-8 rounded-lg border border-stone-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-stone-950">Acessos rapidos</h2>
                <p class="mt-1 text-sm text-stone-600">Casas, unidades, comodidades e atividades ja podem ser geridas; as restantes areas ficam preparadas para as proximas subfases.</p>
            </div>
        </div>

        <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($links as $link)
                <a href="{{ $link['route'] }}" class="rounded-md border border-stone-200 px-4 py-3 text-sm font-semibold text-stone-800 hover:border-emerald-700 hover:text-emerald-800">{{ $link['label'] }}</a>
            @endforeach
        </div>
    </section>

    <section class="mt-8 rounded-lg border border-stone-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-stone-950">Proximo bloqueio futuro</h2>
                @if ($nextBlockedDate)
                    @php
                        $blockedUnit = $nextBlockedDate->rentalUnit;
                        $blockedHouse = $blockedUnit?->house;
                    @endphp
                    <p class="mt-2 text-sm text-stone-700">
                        {{ $nextBlockedDate->starts_at->format('d/m/Y') }} a {{ $nextBlockedDate->ends_at->format('d/m/Y') }}
                        - {{ $blockedHouse?->name ?? '-' }} - {{ $blockedUnit?->name ?? '-' }}
                        - {{ $nextBlockedDate->calendarSource?->platform ?? $nextBlockedDate->source }}
                    </p>
                    @if ($nextBlockedDate->summary)
                        <p class="mt-1 text-sm text-stone-500">{{ $nextBlockedDate->summary }}</p>
                    @endif
                @else
                    <p class="mt-2 text-sm text-stone-500">Nao existem bloqueios futuros registados.</p>
                @endif
            </div>

            @if ($nextBlockedDate)
                <a href="{{ route('admin.blocked-dates.edit', $nextBlockedDate) }}" class="rounded-md border border-stone-300 px-4 py-3 text-sm font-semibold text-stone-800 hover:border-emerald-700 hover:text-emerald-800">Editar bloqueio</a>
            @endif
        </div>
    </section>

    <section class="mt-8 grid gap-6 xl:grid-cols-2">
        <div class="rounded-lg border border-stone-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-lg font-semibold text-stone-950">Ultimos pedidos de reserva</h2>
                <a href="{{ route('admin.booking-requests.index') }}" class="text-sm font-semibold text-emerald-800 hover:text-emerald-950">Ver todos</a>
            </div>

            <div class="mt-5 divide-y divide-stone-100">
                @forelse ($latestBookingRequests as $bookingRequest)
                    @php
                        $unit = $bookingRequest->rentalUnit;
                        $house = $unit?->house;
                    @endphp
                    <a href="{{ route('admin.booking-requests.show', $bookingRequest) }}" class="block py-4 first:pt-0 last:pb-0">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-semibold text-stone-950">{{ $bookingRequest->name }}</p>
                                <p class="mt-1 text-sm text-stone-600">{{ $house?->name ?? '-' }} - {{ $unit?->name ?? '-' }}</p>
                            </div>
                            <span class="rounded-md bg-stone-100 px-2 py-1 text-xs font-semibold text-stone-700">{{ $bookingStatusLabels[$bookingRequest->status] ?? $bookingRequest->status }}</span>
                        </div>
                        <p class="mt-2 text-xs text-stone-500">{{ $bookingRequest->check_in->format('d/m/Y') }} a {{ $bookingRequest->check_out->format('d/m/Y') }} - {{ $bookingRequest->created_at?->format('d/m/Y H:i') }}</p>
                    </a>
                @empty
                    <p class="py-4 text-sm text-stone-500">Ainda nao existem pedidos de reserva.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-lg border border-stone-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-lg font-semibold text-stone-950">Ultimas mensagens</h2>
                <a href="{{ route('admin.contact-messages.index') }}" class="text-sm font-semibold text-emerald-800 hover:text-emerald-950">Ver todas</a>
            </div>

            <div class="mt-5 divide-y divide-stone-100">
                @forelse ($latestContactMessages as $contactMessage)
                    <a href="{{ route('admin.contact-messages.show', $contactMessage) }}" class="block py-4 first:pt-0 last:pb-0">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-semibold text-stone-950">{{ $contactMessage->name }}</p>
                                <p class="mt-1 text-sm text-stone-600">{{ $contactMessage->subject ?: 'Sem assunto' }}</p>
                            </div>
                            <span class="rounded-md bg-stone-100 px-2 py-1 text-xs font-semibold text-stone-700">{{ $messageStatusLabels[$contactMessage->status] ?? $contactMessage->status }}</span>
                        </div>
                        <p class="mt-2 text-xs text-stone-500">{{ $contactMessage->email ?: $contactMessage->phone ?: 'Sem contacto' }} - {{ $contactMessage->created_at?->format('d/m/Y H:i') }}</p>
                    </a>
                @empty
                    <p class="py-4 text-sm text-stone-500">Ainda nao existem mensagens.</p>
                @endforelse
            </div>
        </div>
    </section>
@endsection
