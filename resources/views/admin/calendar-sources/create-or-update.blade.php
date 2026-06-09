@extends('layouts.admin')

@section('header', $calendarSource->exists ? 'Editar fonte iCal' : 'Criar fonte iCal')

@section('content')
    @if ($errors->any())
        <div class="mb-6 rounded-md bg-red-50 p-4 text-sm text-red-900">
            <p class="font-semibold">Corrija os campos assinalados.</p>
        </div>
    @endif

    <form method="POST" action="{{ $calendarSource->exists ? route('admin.calendar-sources.update', $calendarSource) : route('admin.calendar-sources.store') }}" class="rounded-lg border border-stone-200 bg-white p-5 shadow-sm">
        @csrf
        @if ($calendarSource->exists)
            @method('PUT')
        @endif

        <label class="grid gap-2 text-sm font-medium text-stone-800">
            Unidade
            <select name="rental_unit_id" class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none" required>
                <option value="">Escolha uma unidade</option>
                @foreach ($rentalUnits as $unit)
                    <option value="{{ $unit->id }}" @selected((string) old('rental_unit_id', $calendarSource->rental_unit_id) === (string) $unit->id)>{{ $unit->house?->name }} - {{ $unit->name }}</option>
                @endforeach
            </select>
            @error('rental_unit_id') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
        </label>

        <div class="mt-5 grid gap-5 lg:grid-cols-2">
            <label class="grid gap-2 text-sm font-medium text-stone-800">
                Plataforma
                <input name="platform" list="platform-options" value="{{ old('platform', $calendarSource->platform) }}" placeholder="Booking, Airbnb, Vrbo, Manual ou Outro" class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none" required>
                <datalist id="platform-options">
                    @foreach ($platforms as $platform)
                        <option value="{{ $platform }}">
                    @endforeach
                </datalist>
                @error('platform') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
            </label>

            <label class="flex items-center gap-2 pt-8 text-sm font-medium text-stone-800">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $calendarSource->is_active)) class="rounded border-stone-300 text-emerald-800 focus:ring-emerald-700">
                Ativa
            </label>
        </div>

        <label class="mt-5 grid gap-2 text-sm font-medium text-stone-800">
            URL iCal
            <input name="ical_url" value="{{ old('ical_url', $calendarSource->ical_url) }}" placeholder="https://example.com/calendar.ics" class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none" required>
            @error('ical_url') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
        </label>

        @if ($calendarSource->exists)
            <div class="mt-5 rounded-lg border border-stone-200 bg-stone-50 p-4 text-sm text-stone-700">
                <p><span class="font-semibold text-stone-950">Ultima sincronizacao:</span> {{ $calendarSource->last_synced_at?->format('d/m/Y H:i') ?? 'Ainda nao sincronizada' }}</p>
                <p class="mt-1">Nesta fase, a URL e apenas registada. Nao ha fetch nem sincronizacao iCal real.</p>
            </div>
        @endif

        <div class="mt-8 flex flex-wrap gap-3">
            <button class="rounded-md bg-emerald-800 px-5 py-3 text-sm font-semibold text-white hover:bg-emerald-900">Guardar</button>
            <a href="{{ route('admin.calendar-sources.index') }}" class="rounded-md border border-stone-300 px-5 py-3 text-sm font-semibold text-stone-800 hover:border-emerald-700 hover:text-emerald-800">Cancelar</a>
        </div>
    </form>
@endsection
