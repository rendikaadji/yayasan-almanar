<template>
  <div class="w-full flex items-center justify-center my-12 text-brand-moss/30 pointer-events-none select-none">
    <svg class="w-full max-w-7xl h-6 px-4" viewBox="0 0 1200 24" fill="none" xmlns="http://www.w3.org/2000/svg">
      <defs>
        <!-- Gradient for fading out in the center -->
        <linearGradient id="fade-center" x1="0%" y1="0%" x2="100%" y2="0%">
          <stop offset="0%" stop-color="currentColor" stop-opacity="1" />
          <stop offset="15%" stop-color="currentColor" stop-opacity="0.6" />
          <stop offset="35%" stop-color="currentColor" stop-opacity="0.15" />
          <stop offset="50%" stop-color="currentColor" stop-opacity="0.05" />
          <stop offset="65%" stop-color="currentColor" stop-opacity="0.15" />
          <stop offset="85%" stop-color="currentColor" stop-opacity="0.6" />
          <stop offset="100%" stop-color="currentColor" stop-opacity="1" />
        </linearGradient>
      </defs>

      <!-- Left Edge Girih Motif (weaving lines & star structure) -->
      <g stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
        <path d="M 0 12 L 80 12" />
        <path d="M 15 4 L 30 20 L 45 4 L 60 20 L 75 12" />
        <path d="M 15 20 L 30 4 L 45 20 L 60 4 L 75 12" />
        <!-- 8-pointed star container -->
        <path d="M 45 12 L 50 7 L 55 12 L 50 17 Z" fill="currentColor" fill-opacity="0.1" />
      </g>
      
      <!-- Fading Connecting Line -->
      <line x1="80" y1="12" x2="1120" y2="12" stroke="url(#fade-center)" stroke-width="1.2" />

      <!-- Right Edge Girih Motif (Mirrored) -->
      <g stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" transform="translate(1120, 0)">
        <path d="M 80 12 L 0 12" />
        <path d="M 65 4 L 50 20 L 35 4 L 20 20 L 5 12" />
        <path d="M 65 20 L 50 4 L 35 20 L 20 4 L 5 12" />
        <!-- 8-pointed star container -->
        <path d="M 35 12 L 30 7 L 25 12 L 30 17 Z" fill="currentColor" fill-opacity="0.1" />
      </g>
    </svg>
  </div>
</template>
