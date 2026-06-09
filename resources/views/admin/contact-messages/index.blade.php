@extends('layouts.admin')

@section('header', 'Mensagens')

@section('content')
    @php
        $statusClasses = [
            'unread' => 'bg-amber-50 text-amber-900',
            'read' => 'bg-emerald-50 text-emerald-900',
            'archived' => 'bg-stone-100 text-stone-700',
        ];
    @endphp

    <section class="rounded-lg border border-stone-200 bg-white p-5 shadow-sm">
        <div>
            <h2 class="text-lg font-semibold text-stone-950">Gerir mensagens</h2>
            <p class="mt-1 text-sm text-stone-600">Mensagens recebidas pela pagina de contactos, com estado manual no admin.</p>
        </div>

        <form method="GET" action="{{ route('admin.contact-messages.index') }}" class="mt-6 grid gap-3 lg:grid-cols-[1fr_220px_auto_auto]">
            <input name="search" value="{{ $search }}" placeholder="Pesquisar por nome, email, telefone ou assunto" class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none">

            <select name="status" class="rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none">
                <option value="">Todos os estados</option>
                @foreach ($statuses as $statusValue => $statusLabel)
                    <option value="{{ $statusValue }}" @selected($status === $statusValue)>{{ $statusLabel }}</option>
                @endforeach
            </select>

            <button class="rounded-md border border-stone-300 px-4 py-3 text-sm font-semibold text-stone-800 hover:border-emerald-700 hover:text-emerald-800">Filtrar</button>
            @if ($search || $status)
                <a href="{{ route('admin.contact-messages.index') }}" class="rounded-md px-4 py-3 text-sm font-semibold text-stone-600 hover:text-stone-950">Limpar</a>
            @endif
        </form>
    </section>

    <section class="mt-6 overflow-hidden rounded-lg border border-stone-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-stone-200 text-sm">
                <thead class="bg-stone-50 text-left text-xs font-semibold uppercase text-stone-500">
                    <tr>
                        <th class="px-4 py-3">Nome</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Telefone</th>
                        <th class="px-4 py-3">Assunto</th>
                        <th class="px-4 py-3">Estado</th>
                        <th class="px-4 py-3">Data</th>
                        <th class="px-4 py-3 text-right">Acoes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse ($contactMessages as $contactMessage)
                        @php
                            $statusClass = $statusClasses[$contactMessage->status] ?? 'bg-stone-100 text-stone-700';
                        @endphp
                        <tr>
                            <td class="px-4 py-4">
                                <p class="font-semibold text-stone-950">{{ $contactMessage->name }}</p>
                                <p class="mt-1 text-xs text-stone-500">#{{ $contactMessage->id }}</p>
                            </td>
                            <td class="px-4 py-4 text-stone-700">{{ $contactMessage->email ?: '-' }}</td>
                            <td class="px-4 py-4 text-stone-700">{{ $contactMessage->phone ?: '-' }}</td>
                            <td class="px-4 py-4 text-stone-700">{{ $contactMessage->subject ?: '-' }}</td>
                            <td class="px-4 py-4">
                                <span class="rounded-md px-2 py-1 text-xs font-semibold {{ $statusClass }}">
                                    {{ $statuses[$contactMessage->status] ?? $contactMessage->status }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-stone-700">{{ $contactMessage->created_at?->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-4">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <a href="{{ route('admin.contact-messages.show', $contactMessage) }}" class="rounded-md border border-stone-300 px-3 py-2 font-semibold text-stone-700 hover:border-emerald-700 hover:text-emerald-800">Ver</a>
                                    <form method="POST" action="{{ route('admin.contact-messages.destroy', $contactMessage) }}" onsubmit="return confirm('Tem a certeza que quer apagar esta mensagem?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-md border border-red-200 px-3 py-2 font-semibold text-red-700 hover:bg-red-50">Apagar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-stone-500">Ainda nao existem mensagens para mostrar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-stone-200 px-4 py-4">
            {{ $contactMessages->links() }}
        </div>
    </section>
@endsection
