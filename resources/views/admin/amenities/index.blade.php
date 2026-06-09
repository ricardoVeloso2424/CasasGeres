@extends('layouts.admin')

@section('header', 'Comodidades')

@section('content')
    <section class="rounded-lg border border-stone-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-stone-950">Gerir comodidades</h2>
                <p class="mt-1 text-sm text-stone-600">Comodidades que podem ser associadas a unidades alugaveis.</p>
            </div>
            <a href="{{ route('admin.amenities.create') }}" class="rounded-md bg-emerald-800 px-4 py-3 text-sm font-semibold text-white hover:bg-emerald-900">Criar comodidade</a>
        </div>

        <form method="GET" action="{{ route('admin.amenities.index') }}" class="mt-6 flex flex-col gap-3 sm:flex-row">
            <input name="search" value="{{ $search }}" placeholder="Pesquisar por nome" class="min-w-0 flex-1 rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none">
            <button class="rounded-md border border-stone-300 px-4 py-3 text-sm font-semibold text-stone-800 hover:border-emerald-700 hover:text-emerald-800">Pesquisar</button>
            @if ($search)
                <a href="{{ route('admin.amenities.index') }}" class="rounded-md px-4 py-3 text-sm font-semibold text-stone-600 hover:text-stone-950">Limpar</a>
            @endif
        </form>
    </section>

    <section class="mt-6 overflow-hidden rounded-lg border border-stone-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-stone-200 text-sm">
                <thead class="bg-stone-50 text-left text-xs font-semibold uppercase text-stone-500">
                    <tr>
                        <th class="px-4 py-3">Nome</th>
                        <th class="px-4 py-3">Icon</th>
                        <th class="px-4 py-3">Unidades</th>
                        <th class="px-4 py-3 text-right">Acoes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse ($amenities as $amenity)
                        <tr>
                            <td class="px-4 py-4">
                                <p class="font-semibold text-stone-950">{{ $amenity->name }}</p>
                                <p class="mt-1 text-xs text-stone-500">{{ $amenity->slug }}</p>
                            </td>
                            <td class="px-4 py-4 text-stone-700">{{ $amenity->icon ?: '-' }}</td>
                            <td class="px-4 py-4 text-stone-700">{{ $amenity->rental_units_count }}</td>
                            <td class="px-4 py-4">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <a href="{{ route('admin.amenities.edit', $amenity) }}" class="rounded-md border border-stone-300 px-3 py-2 font-semibold text-stone-700 hover:border-emerald-700 hover:text-emerald-800">Editar</a>
                                    <form method="POST" action="{{ route('admin.amenities.destroy', $amenity) }}" onsubmit="return confirm('Tem a certeza que quer apagar esta comodidade?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-md border border-red-200 px-3 py-2 font-semibold text-red-700 hover:bg-red-50">Apagar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-stone-500">Ainda nao existem comodidades para mostrar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-stone-200 px-4 py-4">
            {{ $amenities->links() }}
        </div>
    </section>
@endsection
