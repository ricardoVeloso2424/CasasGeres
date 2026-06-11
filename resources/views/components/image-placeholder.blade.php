@props(['title' => 'Sem imagem'])

<div {{ $attributes->merge(['class' => 'relative flex items-center justify-center overflow-hidden bg-gradient-to-br from-fir-700 via-fir-800 to-fir-950 text-fir-50', 'aria-label' => 'Sem imagem']) }}>
    <svg class="absolute right-6 top-5 h-10 w-10 text-amber-200/35" viewBox="0 0 40 40" fill="currentColor" aria-hidden="true">
        <circle cx="20" cy="20" r="14"/>
    </svg>
    <svg class="absolute -bottom-2 left-0 h-2/3 w-full text-fir-950/45" viewBox="0 0 400 200" fill="none" preserveAspectRatio="xMidYMax slice" aria-hidden="true">
        <path d="M0 170 80 90l55 50 60-85 70 95 55-55 25 30v60H0z" fill="currentColor"/>
        <path d="M0 200 110 120l70 55 65-75 75 90 80-60v70H0z" fill="currentColor" opacity="0.6"/>
    </svg>
    <span class="relative z-10 px-6 text-center font-display text-base font-semibold lg:text-lg">{{ $title }}</span>
</div>
