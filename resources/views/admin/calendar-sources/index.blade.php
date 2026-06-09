@extends('layouts.admin')

@section('header', 'Calendarios iCal')

@section('content')
    <section class="rounded-lg border border-stone-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-stone-950">Gerir fontes de calendario</h2>
                <p class="mt-1 text-sm text-stone-600">URLs iCal registadas por unidade, com sincronizacao manual ou pelo scheduler.</p>
            </div>
            <a href="{{ route('admin.calendar-sources.create') }}" class="rounded-md bg-emerald-800 px-4 py-3 text-sm font-semibold text-white hover:bg-emerald-900">Criar fonte</a>
        </div>

        <form method="GET" action="{{ route('admin.calendar-sources.index') }}" class="mt-6 grid gap-3 lg:grid-cols-[1fr_260px_180px_auto_auto]">
            <input name="search" value="{{ $search }}" placeholder="Pesquisar por plataforma, URL, casa ou unidade" class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none">

            <select name="rental_unit_id" class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none">
                <option value="">Todas as unidades</option>
                @foreach ($rentalUnits as $unit)
                    <option value="{{ $unit->id }}" @selected((string) $rentalUnitId === (string) $unit->id)>{{ $unit->house?->name }} - {{ $unit->name }}</option>
                @endforeach
            </select>

            <select name="active" class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none">
                <option value="">Todos os estados</option>
                <option value="1" @selected($active === '1')>Ativas</option>
                <option value="0" @selected($active === '0')>Inativas</option>
            </select>

            <button class="rounded-md border border-stone-300 px-4 py-3 text-sm font-semibold text-stone-800 hover:border-emerald-700 hover:text-emerald-800">Filtrar</button>
            @if ($search || $rentalUnitId || $active !== null)
                <a href="{{ route('admin.calendar-sources.index') }}" class="rounded-md px-4 py-3 text-sm font-semibold text-stone-600 hover:text-stone-950">Limpar</a>
            @endif
        </form>
    </section>

    <section class="mt-6 overflow-hidden rounded-lg border border-stone-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-stone-200 text-sm">
                <thead class="bg-stone-50 text-left text-xs font-semibold uppercase text-stone-500">
                    <tr>
                        <th class="px-4 py-3">Fonte</th>
                        <th class="px-4 py-3">Casa</th>
                        <th class="px-4 py-3">Unidade</th>
                        <th class="px-4 py-3">URL</th>
                        <th class="px-4 py-3">Bloqueios</th>
                        <th class="px-4 py-3">Estado</th>
                        <th class="px-4 py-3">Ultima sync</th>
                        <th class="px-4 py-3">Resultado sync</th>
                        <th class="px-4 py-3 text-right">Acoes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse ($calendarSources as $calendarSource)
                        @php
                            $unit = $calendarSource->rentalUnit;
                            $house = $unit?->house;
                        @endphp
                        <tr>
                            <td class="px-4 py-4">
                                <p class="font-semibold text-stone-950">{{ $calendarSource->platform }}</p>
                                <p class="mt-1 text-xs text-stone-500">#{{ $calendarSource->id }}</p>
                            </td>
                            <td class="px-4 py-4 text-stone-700">{{ $house?->name ?? '-' }}</td>
                            <td class="px-4 py-4 text-stone-700">{{ $unit?->name ?? '-' }}</td>
                            <td class="max-w-sm break-all px-4 py-4 text-stone-700">{{ $calendarSource->ical_url }}</td>
                            <td class="px-4 py-4 text-stone-700">{{ $calendarSource->blocked_dates_count }}</td>
                            <td class="px-4 py-4">
                                <span class="rounded-md px-2 py-1 text-xs font-semibold {{ $calendarSource->is_active ? 'bg-emerald-50 text-emerald-900' : 'bg-stone-100 text-stone-700' }}">
                                    {{ $calendarSource->is_active ? 'Ativa' : 'Inativa' }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-stone-700">{{ $calendarSource->last_synced_at?->format('d/m/Y H:i') ?? '-' }}</td>
                            <td class="px-4 py-4">
                                @if ($calendarSource->last_sync_status === 'success')
                                    <span class="rounded-md bg-emerald-50 px-2 py-1 text-xs font-semibold text-emerald-900">Success</span>
                                @elseif ($calendarSource->last_sync_status === 'failed')
                                    <span class="rounded-md bg-red-50 px-2 py-1 text-xs font-semibold text-red-900">Failed</span>
                                    @if ($calendarSource->last_sync_error)
                                        <p class="mt-2 max-w-xs text-xs leading-5 text-red-800">{{ str($calendarSource->last_sync_error)->limit(120) }}</p>
                                    @endif
                                @else
                                    <span class="text-stone-500">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <form method="POST" action="{{ route('admin.calendar-sources.sync', $calendarSource) }}">
                                        @csrf
                                        <button class="rounded-md border border-emerald-200 px-3 py-2 font-semibold text-emerald-800 hover:bg-emerald-50">Sincronizar</button>
                                    </form>
                                    <a href="{{ route('admin.calendar-sources.edit', $calendarSource) }}" class="rounded-md border border-stone-300 px-3 py-2 font-semibold text-stone-700 hover:border-emerald-700 hover:text-emerald-800">Editar</a>
                                    <form method="POST" action="{{ route('admin.calendar-sources.destroy', $calendarSource) }}" onsubmit="return confirm('Tem a certeza que quer apagar esta fonte de calendario?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-md border border-red-200 px-3 py-2 font-semibold text-red-700 hover:bg-red-50">Apagar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-8 text-center text-stone-500">Ainda nao existem fontes de calendario para mostrar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-stone-200 px-4 py-4">
            {{ $calendarSources->links() }}
        </div>
    </section>
@endsection
