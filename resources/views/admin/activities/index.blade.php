@extends('layouts.admin')

@section('header', 'Atividades')

@section('content')
    <section class="rounded-lg border border-stone-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-stone-950">Gerir atividades</h2>
                <p class="mt-1 text-sm text-stone-600">Trilhos, cascatas, restaurantes e locais a visitar.</p>
            </div>
            <a href="{{ route('admin.activities.create') }}" class="rounded-md bg-emerald-800 px-4 py-3 text-sm font-semibold text-white hover:bg-emerald-900">Criar atividade</a>
        </div>

        <form method="GET" action="{{ route('admin.activities.index') }}" class="mt-6 grid gap-3 lg:grid-cols-[1fr_260px_auto_auto]">
            <input name="search" value="{{ $search }}" placeholder="Pesquisar por titulo, categoria ou localizacao" class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none">
            <select name="category" class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none">
                <option value="">Todas as categorias</option>
                @foreach ($categories as $categoryOption)
                    <option value="{{ $categoryOption }}" @selected($category === $categoryOption)>{{ $categoryOption }}</option>
                @endforeach
            </select>
            <button class="rounded-md border border-stone-300 px-4 py-3 text-sm font-semibold text-stone-800 hover:border-emerald-700 hover:text-emerald-800">Filtrar</button>
            @if ($search || $category)
                <a href="{{ route('admin.activities.index') }}" class="rounded-md px-4 py-3 text-sm font-semibold text-stone-600 hover:text-stone-950">Limpar</a>
            @endif
        </form>
    </section>

    <section class="mt-6 overflow-hidden rounded-lg border border-stone-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-stone-200 text-sm">
                <thead class="bg-stone-50 text-left text-xs font-semibold uppercase text-stone-500">
                    <tr>
                        <th class="px-4 py-3">Titulo</th>
                        <th class="px-4 py-3">Categoria</th>
                        <th class="px-4 py-3">Localizacao</th>
                        <th class="px-4 py-3">Estado</th>
                        <th class="px-4 py-3 text-right">Acoes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse ($activities as $activity)
                        <tr>
                            <td class="px-4 py-4">
                                <p class="font-semibold text-stone-950">{{ $activity->title }}</p>
                                <p class="mt-1 text-xs text-stone-500">{{ $activity->slug }}</p>
                            </td>
                            <td class="px-4 py-4 text-stone-700">{{ $activity->category }}</td>
                            <td class="px-4 py-4 text-stone-700">{{ $activity->location ?: '-' }}</td>
                            <td class="px-4 py-4">
                                <span class="rounded-md px-2 py-1 text-xs font-semibold {{ $activity->is_active ? 'bg-emerald-50 text-emerald-900' : 'bg-stone-100 text-stone-700' }}">
                                    {{ $activity->is_active ? 'Ativa' : 'Inativa' }}
                                </span>
                                @if ($activity->is_featured)
                                    <span class="ml-1 rounded-md bg-sky-50 px-2 py-1 text-xs font-semibold text-sky-900">Destaque</span>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex flex-wrap justify-end gap-2">
                                    @if ($activity->is_active)
                                        <a href="{{ route('activities.index') }}" class="rounded-md border border-stone-300 px-3 py-2 font-semibold text-stone-700 hover:border-emerald-700 hover:text-emerald-800">Ver no site</a>
                                    @endif
                                    <a href="{{ route('admin.activities.edit', $activity) }}" class="rounded-md border border-stone-300 px-3 py-2 font-semibold text-stone-700 hover:border-emerald-700 hover:text-emerald-800">Editar</a>
                                    <form method="POST" action="{{ route('admin.activities.destroy', $activity) }}" onsubmit="return confirm('Tem a certeza que quer apagar esta atividade?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-md border border-red-200 px-3 py-2 font-semibold text-red-700 hover:bg-red-50">Apagar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-stone-500">Ainda nao existem atividades para mostrar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-stone-200 px-4 py-4">
            {{ $activities->links() }}
        </div>
    </section>
@endsection
