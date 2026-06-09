@props(['title' => 'Sem imagem'])

<div {{ $attributes->merge(['class' => 'flex items-center justify-center bg-stone-100 text-stone-500', 'aria-label' => 'Sem imagem']) }}>
    <span class="px-6 text-center text-base font-semibold lg:text-lg">{{ $title }}</span>
</div>
