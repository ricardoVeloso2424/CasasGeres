@props(['question', 'answer'])

<div x-data="{ open: false }" x-id="['faq-panel']">
    <h3>
        <button
            type="button"
            class="flex w-full items-center justify-between gap-5 px-6 py-5 text-left transition-colors duration-200 hover:bg-sand-50/80 lg:px-7 lg:py-6"
            x-on:click="open = ! open"
            x-bind:aria-expanded="open.toString()"
            x-bind:aria-controls="$id('faq-panel')"
        >
            <span class="text-lg font-semibold text-stone-950">{{ $question }}</span>
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-fir-600/10 text-fir-800 ring-1 ring-inset ring-fir-700/15 transition-transform duration-300 motion-reduce:transition-none" x-bind:class="open && 'rotate-45'">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M10 4v12M4 10h12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            </span>
        </button>
    </h3>
    <div
        x-cloak
        x-bind:id="$id('faq-panel')"
        class="grid transition-all duration-300 ease-out motion-reduce:transition-none"
        x-bind:class="open ? 'grid-rows-[1fr] opacity-100' : 'grid-rows-[0fr] opacity-0'"
    >
        <div class="overflow-hidden">
            <p class="max-w-2xl px-6 pb-6 text-base leading-8 text-stone-600 lg:px-7">{{ $answer }}</p>
        </div>
    </div>
</div>
