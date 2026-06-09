@extends('layouts.admin')

@section('header', 'Pedidos de reserva')

@section('content')
    @php
        $statusClasses = [
            'pending' => 'bg-amber-50 text-amber-900',
            'contacted' => 'bg-sky-50 text-sky-900',
            'confirmed' => 'bg-emerald-50 text-emerald-900',
            'cancelled' => 'bg-stone-100 text-stone-700',
        ];
    @endphp

    <section class="rounded-lg border border-stone-200 bg-white p-5 shadow-sm">
        <div>
            <h2 class="text-lg font-semibold text-stone-950">Gerir pedidos de reserva</h2>
            <p class="mt-1 text-sm text-stone-600">Pedidos recebidos pelo formulario publico, com estado manual no admin.</p>
        </div>

        <form method="GET" action="{{ route('admin.booking-requests.index') }}" class="mt-6 grid gap-3 lg:grid-cols-[1fr_220px_260px_auto_auto]">
            <input name="search" value="{{ $search }}" placeholder="Pesquisar por nome, email ou telefone" class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none">

            <select name="status" class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none">
                <option value="">Todos os estados</option>
                @foreach ($statuses as $statusValue => $statusLabel)
                    <option value="{{ $statusValue }}" @selected($status === $statusValue)>{{ $statusLabel }}</option>
                @endforeach
            </select>

            <select name="house_id" class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none">
                <option value="">Todas as casas</option>
                @foreach ($houses as $house)
                    <option value="{{ $house->id }}" @selected((string) $houseId === (string) $house->id)>{{ $house->name }}</option>
                @endforeach
            </select>

            <button class="rounded-md border border-stone-300 px-4 py-3 text-sm font-semibold text-stone-800 hover:border-emerald-700 hover:text-emerald-800">Filtrar</button>
            @if ($search || $status || $houseId)
                <a href="{{ route('admin.booking-requests.index') }}" class="rounded-md px-4 py-3 text-sm font-semibold text-stone-600 hover:text-stone-950">Limpar</a>
            @endif
        </form>
    </section>

    <section class="mt-6 overflow-hidden rounded-lg border border-stone-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-stone-200 text-sm">
                <thead class="bg-stone-50 text-left text-xs font-semibold uppercase text-stone-500">
                    <tr>
                        <th class="px-4 py-3">Cliente</th>
                        <th class="px-4 py-3">Casa</th>
                        <th class="px-4 py-3">Unidade</th>
                        <th class="px-4 py-3">Datas</th>
                        <th class="px-4 py-3">Hospedes</th>
                        <th class="px-4 py-3">Contactos</th>
                        <th class="px-4 py-3">Estado</th>
                        <th class="px-4 py-3">Pedido</th>
                        <th class="px-4 py-3 text-right">Acoes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse ($bookingRequests as $bookingRequest)
                        @php
                            $unit = $bookingRequest->rentalUnit;
                            $house = $unit?->house;
                            $statusClass = $statusClasses[$bookingRequest->status] ?? 'bg-stone-100 text-stone-700';
                        @endphp
                        <tr>
                            <td class="px-4 py-4">
                                <p class="font-semibold text-stone-950">{{ $bookingRequest->name }}</p>
                                <p class="mt-1 text-xs text-stone-500">#{{ $bookingRequest->id }}</p>
                            </td>
                            <td class="px-4 py-4 text-stone-700">{{ $house?->name ?? '-' }}</td>
                            <td class="px-4 py-4 text-stone-700">{{ $unit?->name ?? '-' }}</td>
                            <td class="px-4 py-4 text-stone-700">
                                <p>{{ $bookingRequest->check_in->format('d/m/Y') }}</p>
                                <p class="mt-1 text-xs text-stone-500">{{ $bookingRequest->check_out->format('d/m/Y') }}</p>
                            </td>
                            <td class="px-4 py-4 text-stone-700">{{ $bookingRequest->guests ?? '-' }}</td>
                            <td class="px-4 py-4 text-stone-700">
                                <p>{{ $bookingRequest->email ?: '-' }}</p>
                                <p class="mt-1 text-xs text-stone-500">{{ $bookingRequest->phone ?: '-' }}</p>
                            </td>
                            <td class="px-4 py-4">
                                <span class="rounded-md px-2 py-1 text-xs font-semibold {{ $statusClass }}">
                                    {{ $statuses[$bookingRequest->status] ?? $bookingRequest->status }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-stone-700">{{ $bookingRequest->created_at?->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-4">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <a href="{{ route('admin.booking-requests.show', $bookingRequest) }}" class="rounded-md border border-stone-300 px-3 py-2 font-semibold text-stone-700 hover:border-emerald-700 hover:text-emerald-800">Ver detalhes</a>
                                    <form method="POST" action="{{ route('admin.booking-requests.destroy', $bookingRequest) }}" onsubmit="return confirm('Tem a certeza que quer apagar este pedido de reserva?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-md border border-red-200 px-3 py-2 font-semibold text-red-700 hover:bg-red-50">Apagar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-8 text-center text-stone-500">Ainda nao existem pedidos de reserva para mostrar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-stone-200 px-4 py-4">
            {{ $bookingRequests->links() }}
        </div>
    </section>
@endsection
