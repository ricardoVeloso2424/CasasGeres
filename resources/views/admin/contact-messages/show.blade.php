@extends('layouts.admin')

@section('header', 'Detalhe da mensagem')

@section('content')
    @php
        $statusClasses = [
            'unread' => 'bg-amber-50 text-amber-900',
            'read' => 'bg-emerald-50 text-emerald-900',
            'archived' => 'bg-stone-100 text-stone-700',
        ];
        $statusClass = $statusClasses[$contactMessage->status] ?? 'bg-stone-100 text-stone-700';
        $phoneForLinks = preg_replace('/\D+/', '', (string) $contactMessage->phone);
    @endphp

    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('admin.contact-messages.index') }}" class="rounded-md border border-stone-300 px-4 py-2 text-sm font-semibold text-stone-700 hover:border-emerald-700 hover:text-emerald-800">Voltar</a>

        <form method="POST" action="{{ route('admin.contact-messages.destroy', $contactMessage) }}" onsubmit="return confirm('Tem a certeza que quer apagar esta mensagem?');">
            @csrf
            @method('DELETE')
            <button class="rounded-md border border-red-200 px-4 py-2 text-sm font-semibold text-red-700 hover:bg-red-50">Apagar</button>
        </form>
    </div>

    @if ($errors->any())
        <div class="mb-6 rounded-md bg-red-50 p-4 text-sm text-red-900">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-[1fr_360px]">
        <section class="rounded-lg border border-stone-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-sm font-medium text-stone-500">Mensagem #{{ $contactMessage->id }}</p>
                    <h2 class="mt-1 text-xl font-semibold text-stone-950">{{ $contactMessage->name }}</h2>
                </div>
                <span class="w-fit rounded-md px-2 py-1 text-xs font-semibold {{ $statusClass }}">
                    {{ $statuses[$contactMessage->status] ?? $contactMessage->status }}
                </span>
            </div>

            <dl class="mt-6 grid gap-5 sm:grid-cols-2">
                <div>
                    <dt class="text-xs font-semibold uppercase text-stone-500">Email</dt>
                    <dd class="mt-1 text-sm text-stone-900">{{ $contactMessage->email ?: '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase text-stone-500">Telefone</dt>
                    <dd class="mt-1 text-sm text-stone-900">{{ $contactMessage->phone ?: '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase text-stone-500">Assunto</dt>
                    <dd class="mt-1 text-sm text-stone-900">{{ $contactMessage->subject ?: '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase text-stone-500">Data</dt>
                    <dd class="mt-1 text-sm text-stone-900">{{ $contactMessage->created_at?->format('d/m/Y H:i') }}</dd>
                </div>
            </dl>

            <div class="mt-6 border-t border-stone-200 pt-5">
                <h3 class="text-sm font-semibold text-stone-950">Mensagem</h3>
                <p class="mt-2 whitespace-pre-line text-sm leading-6 text-stone-700">{{ $contactMessage->message }}</p>
            </div>
        </section>

        <aside class="space-y-6">
            <section class="rounded-lg border border-stone-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold text-stone-950">Atualizar status</h2>
                <p class="mt-1 text-sm text-stone-600">Abrir a mensagem nao altera o estado automaticamente.</p>

                <form method="POST" action="{{ route('admin.contact-messages.update-status', $contactMessage) }}" class="mt-5 space-y-4">
                    @csrf
                    @method('PATCH')

                    <select name="status" class="w-full rounded-md border border-stone-300 px-3 py-3 text-sm focus:border-emerald-700 focus:outline-none">
                        @foreach ($statuses as $statusValue => $statusLabel)
                            <option value="{{ $statusValue }}" @selected(old('status', $contactMessage->status) === $statusValue)>{{ $statusLabel }}</option>
                        @endforeach
                    </select>

                    <button class="w-full rounded-md bg-emerald-800 px-4 py-3 text-sm font-semibold text-white hover:bg-emerald-900">Atualizar status</button>
                </form>
            </section>

            <section class="rounded-lg border border-stone-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold text-stone-950">Contactos rapidos</h2>
                <div class="mt-5 grid gap-3">
                    @if ($contactMessage->email)
                        <a href="mailto:{{ $contactMessage->email }}" class="rounded-md border border-stone-300 px-4 py-3 text-sm font-semibold text-stone-700 hover:border-emerald-700 hover:text-emerald-800">Responder por email</a>
                    @endif

                    @if ($contactMessage->phone)
                        <a href="tel:{{ $contactMessage->phone }}" class="rounded-md border border-stone-300 px-4 py-3 text-sm font-semibold text-stone-700 hover:border-emerald-700 hover:text-emerald-800">Ligar</a>
                    @endif

                    @if ($phoneForLinks)
                        <a href="https://wa.me/{{ $phoneForLinks }}" target="_blank" rel="noreferrer" class="rounded-md border border-stone-300 px-4 py-3 text-sm font-semibold text-stone-700 hover:border-emerald-700 hover:text-emerald-800">WhatsApp</a>
                    @endif

                    @unless ($contactMessage->email || $contactMessage->phone)
                        <p class="text-sm text-stone-500">Sem contactos registados.</p>
                    @endunless
                </div>
            </section>
        </aside>
    </div>
@endsection
