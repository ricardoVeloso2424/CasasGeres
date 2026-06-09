@extends('layouts.admin')

@section('header', 'Casas')

@section('content')
    @if (session('error'))
        <div class="mb-6 rounded-md bg-red-50 p-4 text-sm text-red-900">{{ session('error') }}</div>
    @endif

    <section class="rounded-lg border border-stone-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-stone-950">Gerir casas</h2>
                <p class="mt-1 text-sm text-stone-600">Crie e edite as casas apresentadas no site publico.</p>
            </div>
            <a href="{{ route('admin.houses.create') }}" class="rounded-md bg-emerald-800 px-4 py-3 text-sm font-semibold text-white hover:bg-emerald-900">Criar casa</a>
        </div>

        <form method="GET" action="{{ route('admin.houses.index') }}" class="mt-6 flex flex-col gap-3 sm:flex-row">
            <input name="search" value="{{ $search }}" placeholder="Pesquisar por nome ou localizacao" class="min-w-0 flex-1 rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none">
            <button class="rounded-md border border-stone-300 px-4 py-3 text-sm font-semibold text-stone-800 hover:border-emerald-700 hover:text-emerald-800">Pesquisar</button>
            @if ($search)
                <a href="{{ route('admin.houses.index') }}" class="rounded-md px-4 py-3 text-sm font-semibold text-stone-600 hover:text-stone-950">Limpar</a>
            @endif
        </form>
    </section>

    <section class="mt-6 overflow-hidden rounded-lg border border-stone-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-stone-200 text-sm">
                <thead class="bg-stone-50 text-left text-xs font-semibold uppercase text-stone-500">
                    <tr>
                        <th class="px-4 py-3">Nome</th>
                        <th class="px-4 py-3">Localizacao</th>
                        <th class="px-4 py-3">Unidades</th>
                        <th class="px-4 py-3">Estado</th>
                        <th class="px-4 py-3 text-right">Acoes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse ($houses as $house)
                        <tr>
                            <td class="px-4 py-4">
                                <p class="font-semibold text-stone-950">{{ $house->name }}</p>
                                <p class="mt-1 text-xs text-stone-500">{{ $house->slug }}</p>
                            </td>
                            <td class="px-4 py-4 text-stone-700">{{ $house->location ?: '-' }}</td>
                            <td class="px-4 py-4 text-stone-700">{{ $house->rental_units_count }}</td>
                            <td class="px-4 py-4">
                                <span class="rounded-md px-2 py-1 text-xs font-semibold {{ $house->is_active ? 'bg-emerald-50 text-emerald-900' : 'bg-stone-100 text-stone-700' }}">
                                    {{ $house->is_active ? 'Ativa' : 'Inativa' }}
                                </span>
                                @if ($house->featured)
                                    <span class="ml-1 rounded-md bg-sky-50 px-2 py-1 text-xs font-semibold text-sky-900">Destaque</span>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex flex-wrap justify-end gap-2">
                                    @if ($house->is_active)
                                        <a href="{{ route('houses.show', $house) }}" class="rounded-md border border-stone-300 px-3 py-2 font-semibold text-stone-700 hover:border-emerald-700 hover:text-emerald-800">Ver no site</a>
                                    @endif
                                    <a href="{{ route('admin.houses.edit', $house) }}" class="rounded-md border border-stone-300 px-3 py-2 font-semibold text-stone-700 hover:border-emerald-700 hover:text-emerald-800">Editar</a>
                                    <form method="POST" action="{{ route('admin.houses.destroy', $house) }}" onsubmit="return confirm('Tem a certeza que quer apagar esta casa?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-md border border-red-200 px-3 py-2 font-semibold text-red-700 hover:bg-red-50">Apagar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-stone-500">Ainda nao existem casas para mostrar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-stone-200 px-4 py-4">
            {{ $houses->links() }}
        </div>
    </section>
@endsection
