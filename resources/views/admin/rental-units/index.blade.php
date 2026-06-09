@extends('layouts.admin')

@section('header', 'Unidades')

@section('content')
    @if (session('error'))
        <div class="mb-6 rounded-md bg-red-50 p-4 text-sm text-red-900">{{ session('error') }}</div>
    @endif

    <section class="rounded-lg border border-stone-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-stone-950">Gerir unidades</h2>
                <p class="mt-1 text-sm text-stone-600">Crie e edite unidades alugaveis associadas a casas.</p>
            </div>
            <a href="{{ route('admin.rental-units.create') }}" class="rounded-md bg-emerald-800 px-4 py-3 text-sm font-semibold text-white hover:bg-emerald-900">Criar unidade</a>
        </div>

        <form method="GET" action="{{ route('admin.rental-units.index') }}" class="mt-6 grid gap-3 lg:grid-cols-[1fr_260px_auto_auto]">
            <input name="search" value="{{ $search }}" placeholder="Pesquisar por nome, tipo ou casa" class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none">
            <select name="house_id" class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none">
                <option value="">Todas as casas</option>
                @foreach ($houses as $house)
                    <option value="{{ $house->id }}" @selected((string) $houseId === (string) $house->id)>{{ $house->name }}</option>
                @endforeach
            </select>
            <button class="rounded-md border border-stone-300 px-4 py-3 text-sm font-semibold text-stone-800 hover:border-emerald-700 hover:text-emerald-800">Filtrar</button>
            @if ($search || $houseId)
                <a href="{{ route('admin.rental-units.index') }}" class="rounded-md px-4 py-3 text-sm font-semibold text-stone-600 hover:text-stone-950">Limpar</a>
            @endif
        </form>
    </section>

    <section class="mt-6 overflow-hidden rounded-lg border border-stone-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-stone-200 text-sm">
                <thead class="bg-stone-50 text-left text-xs font-semibold uppercase text-stone-500">
                    <tr>
                        <th class="px-4 py-3">Unidade</th>
                        <th class="px-4 py-3">Casa</th>
                        <th class="px-4 py-3">Tipo</th>
                        <th class="px-4 py-3">Capacidade</th>
                        <th class="px-4 py-3">Comodidades</th>
                        <th class="px-4 py-3">Estado</th>
                        <th class="px-4 py-3 text-right">Acoes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse ($units as $unit)
                        <tr>
                            <td class="px-4 py-4">
                                <p class="font-semibold text-stone-950">{{ $unit->name }}</p>
                                <p class="mt-1 text-xs text-stone-500">{{ $unit->slug }}</p>
                            </td>
                            <td class="px-4 py-4 text-stone-700">{{ $unit->house?->name }}</td>
                            <td class="px-4 py-4 text-stone-700">{{ $unit->type }}</td>
                            <td class="px-4 py-4 text-stone-700">{{ $unit->capacity }} hospedes</td>
                            <td class="px-4 py-4 text-stone-700">
                                @if ($unit->amenities_count)
                                    <span>{{ $unit->amenities_count }}</span>
                                    <p class="mt-1 max-w-56 truncate text-xs text-stone-500">{{ $unit->amenities->take(3)->pluck('name')->join(', ') }}</p>
                                @else
                                    <span>-</span>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                <span class="rounded-md px-2 py-1 text-xs font-semibold {{ $unit->is_active ? 'bg-emerald-50 text-emerald-900' : 'bg-stone-100 text-stone-700' }}">
                                    {{ $unit->is_active ? 'Ativa' : 'Inativa' }}
                                </span>
                                @if ($unit->featured)
                                    <span class="ml-1 rounded-md bg-sky-50 px-2 py-1 text-xs font-semibold text-sky-900">Destaque</span>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex flex-wrap justify-end gap-2">
                                    @if ($unit->is_active && $unit->house?->is_active)
                                        <a href="{{ route('houses.units.show', [$unit->house, $unit]) }}" class="rounded-md border border-stone-300 px-3 py-2 font-semibold text-stone-700 hover:border-emerald-700 hover:text-emerald-800">Ver no site</a>
                                    @endif
                                    <a href="{{ route('admin.rental-units.edit', $unit->id) }}" class="rounded-md border border-stone-300 px-3 py-2 font-semibold text-stone-700 hover:border-emerald-700 hover:text-emerald-800">Editar</a>
                                    <form method="POST" action="{{ route('admin.rental-units.destroy', $unit->id) }}" onsubmit="return confirm('Tem a certeza que quer apagar esta unidade?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-md border border-red-200 px-3 py-2 font-semibold text-red-700 hover:bg-red-50">Apagar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-stone-500">Ainda nao existem unidades para mostrar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-stone-200 px-4 py-4">
            {{ $units->links() }}
        </div>
    </section>
@endsection
