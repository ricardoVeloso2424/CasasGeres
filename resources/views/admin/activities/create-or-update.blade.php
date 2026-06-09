@extends('layouts.admin')

@section('header', $activity->exists ? 'Editar atividade' : 'Criar atividade')

@section('content')
    @if ($errors->any())
        <div class="mb-6 rounded-md bg-red-50 p-4 text-sm text-red-900">
            <p class="font-semibold">Corrija os campos assinalados.</p>
        </div>
    @endif

    <form method="POST" action="{{ $activity->exists ? route('admin.activities.update', $activity) : route('admin.activities.store') }}" class="rounded-lg border border-stone-200 bg-white p-5 shadow-sm">
        @csrf
        @if ($activity->exists)
            @method('PUT')
        @endif

        <div class="grid gap-5 lg:grid-cols-2">
            <label class="grid gap-2 text-sm font-medium text-stone-800">
                Titulo
                <input name="title" value="{{ old('title', $activity->title) }}" class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none" required>
                @error('title') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
            </label>

            <label class="grid gap-2 text-sm font-medium text-stone-800">
                Slug
                <input name="slug" value="{{ old('slug', $activity->slug) }}" placeholder="gerado automaticamente se ficar vazio" class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none">
                @error('slug') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
            </label>
        </div>

        <div class="mt-5 grid gap-5 lg:grid-cols-2">
            <label class="grid gap-2 text-sm font-medium text-stone-800">
                Categoria
                <select name="category" class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none" required>
                    <option value="">Escolha uma categoria</option>
                    @foreach ($categories as $categoryOption)
                        <option value="{{ $categoryOption }}" @selected(old('category', $activity->category) === $categoryOption)>{{ $categoryOption }}</option>
                    @endforeach
                </select>
                @error('category') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
            </label>

            <label class="grid gap-2 text-sm font-medium text-stone-800">
                Imagem URL/path
                <input name="image" value="{{ old('image', $activity->image) }}" class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none">
                @error('image') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
            </label>
        </div>

        <div class="mt-5 grid gap-5 lg:grid-cols-2">
            <label class="grid gap-2 text-sm font-medium text-stone-800">
                Localizacao
                <input name="location" value="{{ old('location', $activity->location) }}" class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none">
                @error('location') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
            </label>

            <label class="grid gap-2 text-sm font-medium text-stone-800">
                Distancia
                <input name="distance" value="{{ old('distance', $activity->distance) }}" class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none">
                @error('distance') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
            </label>
        </div>

        <label class="mt-5 grid gap-2 text-sm font-medium text-stone-800">
            Descricao
            <textarea name="description" rows="8" class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none" required>{{ old('description', $activity->description) }}</textarea>
            @error('description') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
        </label>

        <div class="mt-5 flex flex-wrap gap-5">
            <label class="flex items-center gap-2 text-sm font-medium text-stone-800">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $activity->is_active)) class="rounded border-stone-300 text-emerald-800 focus:ring-emerald-700">
                Ativa
            </label>
            <label class="flex items-center gap-2 text-sm font-medium text-stone-800">
                <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $activity->is_featured)) class="rounded border-stone-300 text-emerald-800 focus:ring-emerald-700">
                Destaque
            </label>
        </div>

        <div class="mt-8 flex flex-wrap gap-3">
            <button class="rounded-md bg-emerald-800 px-5 py-3 text-sm font-semibold text-white hover:bg-emerald-900">Guardar</button>
            <a href="{{ route('admin.activities.index') }}" class="rounded-md border border-stone-300 px-5 py-3 text-sm font-semibold text-stone-800 hover:border-emerald-700 hover:text-emerald-800">Cancelar</a>
            @if ($activity->exists && $activity->is_active)
                <a href="{{ route('activities.index') }}" class="rounded-md border border-stone-300 px-5 py-3 text-sm font-semibold text-stone-800 hover:border-emerald-700 hover:text-emerald-800">Ver no site</a>
            @endif
        </div>
    </form>

    @if ($activity->exists)
        @include('admin.photos.manager', [
            'imageable' => $activity,
            'type' => 'activities',
            'title' => $activity->title,
        ])
    @endif
@endsection
