@php
    $unit = $bookingRequest->rentalUnit;
    $house = $unit?->house;
@endphp

<h1>Novo pedido de reserva</h1>

<p>Foi recebido um novo pedido de reserva direta.</p>

<dl>
    <dt>Nome</dt>
    <dd>{{ $bookingRequest->name }}</dd>

    <dt>Email</dt>
    <dd>{{ $bookingRequest->email ?: '-' }}</dd>

    <dt>Telefone</dt>
    <dd>{{ $bookingRequest->phone ?: '-' }}</dd>

    <dt>Casa</dt>
    <dd>{{ $house?->name ?? '-' }}</dd>

    <dt>Unidade</dt>
    <dd>{{ $unit?->name ?? '-' }}</dd>

    <dt>Check-in</dt>
    <dd>{{ $bookingRequest->check_in?->format('d/m/Y') }}</dd>

    <dt>Check-out</dt>
    <dd>{{ $bookingRequest->check_out?->format('d/m/Y') }}</dd>

    <dt>Hospedes</dt>
    <dd>{{ $bookingRequest->guests ?? '-' }}</dd>

    <dt>Mensagem</dt>
    <dd>{{ $bookingRequest->message ?: '-' }}</dd>
</dl>

<p>
    <a href="{{ route('admin.booking-requests.show', $bookingRequest) }}">Abrir pedido no admin</a>
</p>
