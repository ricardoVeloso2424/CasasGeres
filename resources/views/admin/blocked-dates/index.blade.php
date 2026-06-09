@extends('layouts.admin')

@section('header', 'Datas bloqueadas')

@section('content')
    <section class="rounded-lg border border-stone-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-stone-950">Gerir datas bloqueadas</h2>
                <p class="mt-1 text-sm text-stone-600">Bloqueios manuais ou importados. A data final e exclusiva para manter compatibilidade com a disponibilidade.</p>
            </div>
            <a href="{{ route('admin.blocked-dates.create') }}" class="rounded-md bg-emerald-800 px-4 py-3 text-sm font-semibold text-white hover:bg-emerald-900">Criar bloqueio</a>
        </div>

        <form method="GET" action="{{ route('admin.blocked-dates.index') }}" class="mt-6 grid gap-3 lg:grid-cols-[1fr_240px_160px_160px_auto_auto]">
            <input name="search" value="{{ $search }}" placeholder="Pesquisar por casa, unidade, origem, resumo ou UID" class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none">

            <select name="rental_unit_id" class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none">
                <option value="">Todas as unidades</option>
                @foreach ($rentalUnits as $unit)
                    <option value="{{ $unit->id }}" @selected((string) $rentalUnitId === (string) $unit->id)>{{ $unit->house?->name }} - {{ $unit->name }}</option>
                @endforeach
            </select>

            <select name="source" class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none">
                <option value="">Todas as origens</option>
                @foreach ($sources as $sourceOption)
                    <option value="{{ $sourceOption }}" @selected($source === $sourceOption)>{{ $sourceOption }}</option>
                @endforeach
            </select>

            <select name="scope" class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none">
                <option value="future" @selected($scope !== 'all')>Futuras/atuais</option>
                <option value="all" @selected($scope === 'all')>Todas</option>
            </select>

            <button class="rounded-md border border-stone-300 px-4 py-3 text-sm font-semibold text-stone-800 hover:border-emerald-700 hover:text-emerald-800">Filtrar</button>
            @if ($search || $rentalUnitId || $source || $scope === 'all')
                <a href="{{ route('admin.blocked-dates.index') }}" class="rounded-md px-4 py-3 text-sm font-semibold text-stone-600 hover:text-stone-950">Limpar</a>
            @endif
        </form>
    </section>

    <section class="mt-6 overflow-hidden rounded-lg border border-stone-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-stone-200 text-sm">
                <thead class="bg-stone-50 text-left text-xs font-semibold uppercase text-stone-500">
                    <tr>
                        <th class="px-4 py-3">Datas</th>
                        <th class="px-4 py-3">Casa</th>
                        <th class="px-4 py-3">Unidade</th>
                        <th class="px-4 py-3">Origem</th>
                        <th class="px-4 py-3">Fonte iCal</th>
                        <th class="px-4 py-3">Resumo</th>
                        <th class="px-4 py-3">UID externo</th>
                        <th class="px-4 py-3 text-right">Acoes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse ($blockedDates as $blockedDate)
                        @php
                            $unit = $blockedDate->rentalUnit;
                            $house = $unit?->house;
                        @endphp
                        <tr>
                            <td class="px-4 py-4 text-stone-700">
                                <p class="font-semibold text-stone-950">{{ $blockedDate->starts_at->format('d/m/Y') }}</p>
                                <p class="mt-1 text-xs text-stone-500">ate {{ $blockedDate->ends_at->format('d/m/Y') }} exclusive</p>
                            </td>
                            <td class="px-4 py-4 text-stone-700">{{ $house?->name ?? '-' }}</td>
                            <td class="px-4 py-4 text-stone-700">{{ $unit?->name ?? '-' }}</td>
                            <td class="px-4 py-4 text-stone-700">{{ $blockedDate->source }}</td>
                            <td class="px-4 py-4 text-stone-700">{{ $blockedDate->calendarSource?->platform ?? 'Manual' }}</td>
                            <td class="px-4 py-4 text-stone-700">{{ $blockedDate->summary ?: '-' }}</td>
                            <td class="px-4 py-4 text-stone-700">{{ $blockedDate->external_uid ?: '-' }}</td>
                            <td class="px-4 py-4">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <a href="{{ route('admin.blocked-dates.edit', $blockedDate) }}" class="rounded-md border border-stone-300 px-3 py-2 font-semibold text-stone-700 hover:border-emerald-700 hover:text-emerald-800">Editar</a>
                                    <form method="POST" action="{{ route('admin.blocked-dates.destroy', $blockedDate) }}" onsubmit="return confirm('Tem a certeza que quer apagar esta data bloqueada?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-md border border-red-200 px-3 py-2 font-semibold text-red-700 hover:bg-red-50">Apagar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-stone-500">Ainda nao existem datas bloqueadas para mostrar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-stone-200 px-4 py-4">
            {{ $blockedDates->links() }}
        </div>
    </section>
@endsection
