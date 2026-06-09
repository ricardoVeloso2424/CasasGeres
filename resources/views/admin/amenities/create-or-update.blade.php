@extends('layouts.admin')

@section('header', $amenity->exists ? 'Editar comodidade' : 'Criar comodidade')

@section('content')
    @if ($errors->any())
        <div class="mb-6 rounded-md bg-red-50 p-4 text-sm text-red-900">
            <p class="font-semibold">Corrija os campos assinalados.</p>
        </div>
    @endif

    <form method="POST" action="{{ $amenity->exists ? route('admin.amenities.update', $amenity) : route('admin.amenities.store') }}" class="rounded-lg border border-stone-200 bg-white p-5 shadow-sm">
        @csrf
        @if ($amenity->exists)
            @method('PUT')
        @endif

        <div class="grid gap-5 lg:grid-cols-2">
            <label class="grid gap-2 text-sm font-medium text-stone-800">
                Nome
                <input name="name" value="{{ old('name', $amenity->name) }}" class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none" required>
                @error('name') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
            </label>

            <label class="grid gap-2 text-sm font-medium text-stone-800">
                Slug
                <input name="slug" value="{{ old('slug', $amenity->slug) }}" placeholder="gerado automaticamente se ficar vazio" class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none">
                @error('slug') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
            </label>
        </div>

        <label class="mt-5 grid gap-2 text-sm font-medium text-stone-800">
            Icon
            <input name="icon" value="{{ old('icon', $amenity->icon) }}" placeholder="wifi, parking, kitchen..." class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none">
            @error('icon') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
        </label>

        <div class="mt-8 flex flex-wrap gap-3">
            <button class="rounded-md bg-emerald-800 px-5 py-3 text-sm font-semibold text-white hover:bg-emerald-900">Guardar</button>
            <a href="{{ route('admin.amenities.index') }}" class="rounded-md border border-stone-300 px-5 py-3 text-sm font-semibold text-stone-800 hover:border-emerald-700 hover:text-emerald-800">Cancelar</a>
        </div>
    </form>
@endsection
