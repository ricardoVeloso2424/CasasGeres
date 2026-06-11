@props(['title' => 'Sem imagem'])

<div {{ $attributes->merge(['class' => 'relative flex items-center justify-center overflow-hidden bg-gradient-to-b from-fir-700 via-fir-800 to-fir-950 text-fir-50', 'aria-label' => 'Sem imagem']) }}>
    <svg class="absolute inset-0 h-full w-full" viewBox="0 0 400 240" fill="none" preserveAspectRatio="xMidYMax slice" aria-hidden="true">
        <circle cx="312" cy="58" r="34" fill="#fcd34d" opacity="0.22"/>
        <circle cx="312" cy="58" r="18" fill="#fcd34d" opacity="0.4"/>
        <path d="M-10 150 60 92l50 42 64-74 70 80 52-48 64 52 60-36v132H-10z" fill="#141f17" opacity="0.22"/>
        <path d="M-10 178 70 122l58 44 70-64 64 70 58-40 60 44 40-22v86H-10z" fill="#141f17" opacity="0.38"/>
        <path d="M-10 240v-36l84-40 70 44 78-54 76 58 62-34 50 30 0 32z" fill="#141f17" opacity="0.55"/>
        <g fill="#141f17" opacity="0.5">
            <path d="m58 196 9-18 9 18zM84 202 94 182l10 20zM304 188l8-16 8 16zM328 194l9-18 9 18z"/>
        </g>
        <rect x="0" y="170" width="400" height="70" fill="url(#cg-mist)"/>
        <defs>
            <linearGradient id="cg-mist" x1="0" y1="170" x2="0" y2="240" gradientUnits="userSpaceOnUse">
                <stop stop-color="#f3efe6" stop-opacity="0"/>
                <stop offset="1" stop-color="#f3efe6" stop-opacity="0.16"/>
            </linearGradient>
        </defs>
    </svg>
    <span class="relative z-10 px-6 text-center font-display text-base font-semibold drop-shadow-[0_1px_8px_rgba(20,31,23,0.5)] lg:text-lg">{{ $title }}</span>
</div>
