@extends('layouts.admin')

@section('header', $title)

@section('content')
    <section class="rounded-lg border border-stone-200 bg-white p-6 shadow-sm">
        <p class="text-sm font-semibold uppercase text-emerald-800">Em construcao</p>
        <h2 class="mt-3 text-2xl font-semibold text-stone-950">{{ $title }}</h2>
        <p class="mt-4 max-w-2xl text-sm leading-6 text-stone-600">Esta area ja esta protegida por autenticacao e preparada para receber o CRUD correspondente na proxima subfase.</p>

        <a href="{{ route('admin.dashboard') }}" class="mt-6 inline-flex rounded-md bg-emerald-800 px-4 py-3 text-sm font-semibold text-white hover:bg-emerald-900">Voltar ao dashboard</a>
    </section>
@endsection
