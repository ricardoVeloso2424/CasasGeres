@extends('layouts.admin')

@section('header', $house->exists ? 'Editar casa' : 'Criar casa')

@section('content')
    @if (session('error'))
        <div class="mb-6 rounded-md bg-red-50 p-4 text-sm text-red-900">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="mb-6 rounded-md bg-red-50 p-4 text-sm text-red-900">
            <p class="font-semibold">Corrija os campos assinalados.</p>
        </div>
    @endif

    <form method="POST" action="{{ $house->exists ? route('admin.houses.update', $house) : route('admin.houses.store') }}" class="rounded-lg border border-stone-200 bg-white p-5 shadow-sm">
        @csrf
        @if ($house->exists)
            @method('PUT')
        @endif

        <div class="grid gap-5 lg:grid-cols-2">
            <label class="grid gap-2 text-sm font-medium text-stone-800">
                Nome
                <input name="name" value="{{ old('name', $house->name) }}" class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none" required>
                @error('name') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
            </label>

            <label class="grid gap-2 text-sm font-medium text-stone-800">
                Slug
                <input name="slug" value="{{ old('slug', $house->slug) }}" placeholder="gerado automaticamente se ficar vazio" class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none">
                @error('slug') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
            </label>
        </div>

        <div class="mt-5 grid gap-5 lg:grid-cols-2">
            <label class="grid gap-2 text-sm font-medium text-stone-800">
                Localizacao
                <input name="location" value="{{ old('location', $house->location) }}" class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none">
                @error('location') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
            </label>

            <label class="grid gap-2 text-sm font-medium text-stone-800">
                Morada opcional
                <input name="address_optional" value="{{ old('address_optional', $house->address_optional) }}" class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none">
                @error('address_optional') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
            </label>
        </div>

        <label class="mt-5 grid gap-2 text-sm font-medium text-stone-800">
            Descricao curta
            <textarea name="short_description" rows="3" class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none">{{ old('short_description', $house->short_description) }}</textarea>
            @error('short_description') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
        </label>

        <label class="mt-5 grid gap-2 text-sm font-medium text-stone-800">
            Descricao completa
            <textarea name="description" rows="7" class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none">{{ old('description', $house->description) }}</textarea>
            @error('description') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
        </label>

        <div class="mt-5 grid gap-5 lg:grid-cols-2">
            <label class="grid gap-2 text-sm font-medium text-stone-800">
                Latitude
                <input name="latitude" value="{{ old('latitude', $house->latitude) }}" class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none">
                @error('latitude') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
            </label>

            <label class="grid gap-2 text-sm font-medium text-stone-800">
                Longitude
                <input name="longitude" value="{{ old('longitude', $house->longitude) }}" class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none">
                @error('longitude') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
            </label>
        </div>

        <div class="mt-5 flex flex-wrap gap-5">
            <label class="flex items-center gap-2 text-sm font-medium text-stone-800">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $house->is_active)) class="rounded border-stone-300 text-emerald-800 focus:ring-emerald-700">
                Ativa
            </label>
            <label class="flex items-center gap-2 text-sm font-medium text-stone-800">
                <input type="checkbox" name="featured" value="1" @checked(old('featured', $house->featured)) class="rounded border-stone-300 text-emerald-800 focus:ring-emerald-700">
                Destaque
            </label>
        </div>

        <div class="mt-8 flex flex-wrap gap-3">
            <button class="rounded-md bg-emerald-800 px-5 py-3 text-sm font-semibold text-white hover:bg-emerald-900">Guardar</button>
            <a href="{{ route('admin.houses.index') }}" class="rounded-md border border-stone-300 px-5 py-3 text-sm font-semibold text-stone-800 hover:border-emerald-700 hover:text-emerald-800">Cancelar</a>
            @if ($house->exists && $house->is_active)
                <a href="{{ route('houses.show', $house) }}" class="rounded-md border border-stone-300 px-5 py-3 text-sm font-semibold text-stone-800 hover:border-emerald-700 hover:text-emerald-800">Ver no site</a>
            @endif
        </div>
    </form>

    @if ($house->exists)
        @include('admin.photos.manager', [
            'imageable' => $house,
            'type' => 'houses',
            'title' => $house->name,
        ])
    @endif
@endsection
