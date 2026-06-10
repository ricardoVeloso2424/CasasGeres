@props(['title' => 'Sem imagem'])

<div {{ $attributes->merge(['class' => 'relative flex items-center justify-center overflow-hidden bg-gradient-to-br from-emerald-800 via-emerald-900 to-stone-900 text-emerald-50', 'aria-label' => 'Sem imagem']) }}>
    <svg class="absolute -bottom-2 left-0 h-2/3 w-full text-emerald-950/40" viewBox="0 0 400 200" fill="none" preserveAspectRatio="xMidYMax slice" aria-hidden="true">
        <path d="M0 170 80 90l55 50 60-85 70 95 55-55 25 30v60H0z" fill="currentColor"/>
        <path d="M0 200 110 120l70 55 65-75 75 90 80-60v70H0z" fill="currentColor" opacity="0.6"/>
    </svg>
    <span class="relative z-10 px-6 text-center font-display text-base font-semibold lg:text-lg">{{ $title }}</span>
</div>
