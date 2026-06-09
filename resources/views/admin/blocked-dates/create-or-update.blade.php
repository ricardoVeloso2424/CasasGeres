@extends('layouts.admin')

@section('header', $blockedDate->exists ? 'Editar data bloqueada' : 'Criar data bloqueada')

@section('content')
    @if ($errors->any())
        <div class="mb-6 rounded-md bg-red-50 p-4 text-sm text-red-900">
            <p class="font-semibold">Corrija os campos assinalados.</p>
        </div>
    @endif

    <form method="POST" action="{{ $blockedDate->exists ? route('admin.blocked-dates.update', $blockedDate) : route('admin.blocked-dates.store') }}" class="rounded-lg border border-stone-200 bg-white p-5 shadow-sm">
        @csrf
        @if ($blockedDate->exists)
            @method('PUT')
        @endif

        <div class="grid gap-5 lg:grid-cols-2">
            <label class="grid gap-2 text-sm font-medium text-stone-800">
                Unidade
                <select name="rental_unit_id" class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none" required>
                    <option value="">Escolha uma unidade</option>
                    @foreach ($rentalUnits as $unit)
                        <option value="{{ $unit->id }}" @selected((string) old('rental_unit_id', $blockedDate->rental_unit_id) === (string) $unit->id)>{{ $unit->house?->name }} - {{ $unit->name }}</option>
                    @endforeach
                </select>
                @error('rental_unit_id') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
            </label>

            <label class="grid gap-2 text-sm font-medium text-stone-800">
                Fonte iCal opcional
                <select name="calendar_source_id" class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none">
                    <option value="">Sem fonte iCal</option>
                    @foreach ($calendarSources as $calendarSource)
                        @php
                            $sourceUnit = $calendarSource->rentalUnit;
                            $sourceHouse = $sourceUnit?->house;
                        @endphp
                        <option value="{{ $calendarSource->id }}" @selected((string) old('calendar_source_id', $blockedDate->calendar_source_id) === (string) $calendarSource->id)>{{ $sourceHouse?->name }} - {{ $sourceUnit?->name }} - {{ $calendarSource->platform }}</option>
                    @endforeach
                </select>
                <span class="text-xs font-normal text-stone-500">Se escolher uma fonte, ela tem de pertencer a unidade selecionada.</span>
                @error('calendar_source_id') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
            </label>
        </div>

        <div class="mt-5 grid gap-5 lg:grid-cols-2">
            <label class="grid gap-2 text-sm font-medium text-stone-800">
                Origem
                <select name="source" class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none" required>
                    <option value="">Escolha uma origem</option>
                    @foreach ($sources as $sourceOption)
                        <option value="{{ $sourceOption }}" @selected(old('source', $blockedDate->source) === $sourceOption)>{{ $sourceOption }}</option>
                    @endforeach
                </select>
                @error('source') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
            </label>

            <label class="grid gap-2 text-sm font-medium text-stone-800">
                UID externo
                <input name="external_uid" value="{{ old('external_uid', $blockedDate->external_uid) }}" class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none">
                @error('external_uid') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
            </label>
        </div>

        <div class="mt-5 grid gap-5 lg:grid-cols-2">
            <label class="grid gap-2 text-sm font-medium text-stone-800">
                Inicio
                <input type="date" name="starts_at" value="{{ old('starts_at', $blockedDate->starts_at?->toDateString()) }}" class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none" required>
                @error('starts_at') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
            </label>

            <label class="grid gap-2 text-sm font-medium text-stone-800">
                Fim
                <input type="date" name="ends_at" value="{{ old('ends_at', $blockedDate->ends_at?->toDateString()) }}" class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none" required>
                <span class="text-xs font-normal text-stone-500">A data de fim e exclusiva. Exemplo: 10 a 12 bloqueia as noites de 10 e 11.</span>
                @error('ends_at') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
            </label>
        </div>

        <label class="mt-5 grid gap-2 text-sm font-medium text-stone-800">
            Resumo
            <textarea name="summary" rows="4" class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none">{{ old('summary', $blockedDate->summary) }}</textarea>
            @error('summary') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
        </label>

        <div class="mt-8 flex flex-wrap gap-3">
            <button class="rounded-md bg-emerald-800 px-5 py-3 text-sm font-semibold text-white hover:bg-emerald-900">Guardar</button>
            <a href="{{ route('admin.blocked-dates.index') }}" class="rounded-md border border-stone-300 px-5 py-3 text-sm font-semibold text-stone-800 hover:border-emerald-700 hover:text-emerald-800">Cancelar</a>
        </div>
    </form>
@endsection
