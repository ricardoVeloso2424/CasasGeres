@props(['question', 'answer'])

<div class="rounded-lg border border-stone-200 bg-white shadow-sm" x-data="{ open: false }">
    <button type="button" class="flex w-full items-center justify-between gap-5 px-6 py-5 text-left lg:px-7 lg:py-6" x-on:click="open = ! open" x-bind:aria-expanded="open.toString()">
        <span class="text-lg font-semibold text-stone-950">{{ $question }}</span>
        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-md bg-stone-100 text-xl font-semibold text-emerald-900" x-text="open ? '-' : '+'"></span>
    </button>
    <div class="px-6 pb-6 text-base leading-8 text-stone-600 lg:px-7" x-show="open" x-cloak>
        {{ $answer }}
    </div>
</div>
