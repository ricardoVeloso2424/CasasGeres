@extends('layouts.admin')

@section('header', $unit->exists ? 'Editar unidade' : 'Criar unidade')

@section('content')
    @if (session('error'))
        <div class="mb-6 rounded-md bg-red-50 p-4 text-sm text-red-900">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="mb-6 rounded-md bg-red-50 p-4 text-sm text-red-900">
            <p class="font-semibold">Corrija os campos assinalados.</p>
        </div>
    @endif

    <form method="POST" action="{{ $unit->exists ? route('admin.rental-units.update', $unit->id) : route('admin.rental-units.store') }}" class="rounded-lg border border-stone-200 bg-white p-5 shadow-sm">
        @csrf
        @if ($unit->exists)
            @method('PUT')
        @endif

        <label class="grid gap-2 text-sm font-medium text-stone-800">
            Casa
            <select name="house_id" class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none" required>
                <option value="">Escolha uma casa</option>
                @foreach ($houses as $house)
                    <option value="{{ $house->id }}" @selected((string) old('house_id', $unit->house_id) === (string) $house->id)>{{ $house->name }}</option>
                @endforeach
            </select>
            @error('house_id') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
        </label>

        <div class="mt-5 grid gap-5 lg:grid-cols-2">
            <label class="grid gap-2 text-sm font-medium text-stone-800">
                Nome
                <input name="name" value="{{ old('name', $unit->name) }}" class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none" required>
                @error('name') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
            </label>

            <label class="grid gap-2 text-sm font-medium text-stone-800">
                Slug
                <input name="slug" value="{{ old('slug', $unit->slug) }}" placeholder="gerado automaticamente se ficar vazio" class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none">
                @error('slug') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
            </label>
        </div>

        <div class="mt-5 grid gap-5 lg:grid-cols-2">
            <label class="grid gap-2 text-sm font-medium text-stone-800">
                Tipo
                <input name="type" value="{{ old('type', $unit->type) }}" placeholder="T1, T2, Casa inteira..." class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none" required>
                @error('type') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
            </label>

            <label class="grid gap-2 text-sm font-medium text-stone-800">
                Preco base
                <input name="base_price" value="{{ old('base_price', $unit->base_price) }}" class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none">
                @error('base_price') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
            </label>
        </div>

        <div class="mt-5 grid gap-5 lg:grid-cols-3">
            <label class="grid gap-2 text-sm font-medium text-stone-800">
                Capacidade
                <input type="number" name="capacity" min="1" value="{{ old('capacity', $unit->capacity) }}" class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none" required>
                @error('capacity') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
            </label>

            <label class="grid gap-2 text-sm font-medium text-stone-800">
                Quartos
                <input type="number" name="bedrooms" min="0" value="{{ old('bedrooms', $unit->bedrooms) }}" class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none">
                @error('bedrooms') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
            </label>

            <label class="grid gap-2 text-sm font-medium text-stone-800">
                Casas de banho
                <input type="number" name="bathrooms" min="0" value="{{ old('bathrooms', $unit->bathrooms) }}" class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none">
                @error('bathrooms') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
            </label>
        </div>

        <label class="mt-5 grid gap-2 text-sm font-medium text-stone-800">
            Descricao curta
            <textarea name="short_description" rows="3" class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none">{{ old('short_description', $unit->short_description) }}</textarea>
            @error('short_description') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
        </label>

        <label class="mt-5 grid gap-2 text-sm font-medium text-stone-800">
            Descricao completa
            <textarea name="description" rows="7" class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none">{{ old('description', $unit->description) }}</textarea>
            @error('description') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
        </label>

        <label class="mt-5 grid gap-2 text-sm font-medium text-stone-800">
            Regras
            <textarea name="rules" rows="4" class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none">{{ old('rules', $unit->rules) }}</textarea>
            @error('rules') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
        </label>

        @php
            $selectedAmenities = collect(old('amenity_ids', $unit->exists ? $unit->amenities->pluck('id')->all() : []))
                ->map(fn ($id) => (string) $id)
                ->all();
        @endphp

        <section class="mt-5 rounded-lg border border-stone-200 bg-stone-50 p-4">
            <h2 class="text-sm font-semibold text-stone-950">Comodidades</h2>
            <p class="mt-1 text-sm text-stone-600">Selecione as comodidades disponiveis nesta unidade.</p>

            <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($amenities as $amenity)
                    <label class="flex items-center gap-2 text-sm font-medium text-stone-800">
                        <input type="checkbox" name="amenity_ids[]" value="{{ $amenity->id }}" @checked(in_array((string) $amenity->id, $selectedAmenities, true)) class="rounded border-stone-300 text-emerald-800 focus:ring-emerald-700">
                        {{ $amenity->name }}
                    </label>
                @empty
                    <p class="text-sm text-stone-600">Ainda nao existem comodidades criadas.</p>
                @endforelse
            </div>

            @error('amenity_ids') <span class="mt-3 block text-sm text-red-700">{{ $message }}</span> @enderror
            @error('amenity_ids.*') <span class="mt-3 block text-sm text-red-700">{{ $message }}</span> @enderror
        </section>

        <div class="mt-5 flex flex-wrap gap-5">
            <label class="flex items-center gap-2 text-sm font-medium text-stone-800">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $unit->is_active)) class="rounded border-stone-300 text-emerald-800 focus:ring-emerald-700">
                Ativa
            </label>
            <label class="flex items-center gap-2 text-sm font-medium text-stone-800">
                <input type="checkbox" name="featured" value="1" @checked(old('featured', $unit->featured)) class="rounded border-stone-300 text-emerald-800 focus:ring-emerald-700">
                Destaque
            </label>
        </div>

        <div class="mt-8 flex flex-wrap gap-3">
            <button class="rounded-md bg-emerald-800 px-5 py-3 text-sm font-semibold text-white hover:bg-emerald-900">Guardar</button>
            <a href="{{ route('admin.rental-units.index') }}" class="rounded-md border border-stone-300 px-5 py-3 text-sm font-semibold text-stone-800 hover:border-emerald-700 hover:text-emerald-800">Cancelar</a>
            @if ($unit->exists && $unit->is_active && $unit->house?->is_active)
                <a href="{{ route('houses.units.show', [$unit->house, $unit]) }}" class="rounded-md border border-stone-300 px-5 py-3 text-sm font-semibold text-stone-800 hover:border-emerald-700 hover:text-emerald-800">Ver no site</a>
            @endif
        </div>
    </form>

    @if ($unit->exists)
        @include('admin.photos.manager', [
            'imageable' => $unit,
            'type' => 'rental-units',
            'title' => $unit->name,
        ])
    @endif
@endsection
