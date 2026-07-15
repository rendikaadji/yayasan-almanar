<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import SectionContainer from '@/Components/SectionContainer.vue';

const props = defineProps({
  post: {
    type: Object,
    default: () => ({
      title: '[ISI: Judul Lengkap Berita/Artikel]',
      content: '[ISI: Isi tulisan lengkap artikel berita keagamaan atau laporan dokumentasi secara rinci. Paragraf 1, Paragraf 2, dst.]',
      thumbnail: null,
      published_at: '[ISI: Tanggal Rilis]',
      author: { name: 'Admin' },
    }),
  },
});
</script>

<template>
  <PublicLayout>
    <Head>
      <title>{{ post.title }} - Al Manar</title>
      <meta name="description" content="Baca warta keagamaan, kajian, dan rilis pengumuman resmi terbaru dari Al Manar." />
    </Head>

    <!-- Ogee Clip Path SVG -->
    <svg class="w-0 h-0 absolute pointer-events-none select-none" width="0" height="0">
      <defs>
        <clipPath id="ogee-arch" clipPathUnits="objectBoundingBox">
          <path d="M 0.5, 0 
                   C 0.42, 0.08, 0.32, 0.12, 0.15, 0.22 
                   C 0.05, 0.28, 0, 0.38, 0, 0.48 
                   L 0, 1 
                   L 1, 1 
                   L 1, 0.48 
                   C 1, 0.38, 0.95, 0.28, 0.85, 0.22 
                   C 0.68, 0.12, 0.58, 0.08, 0.5, 0 Z" />
        </clipPath>
      </defs>
    </svg>

    <SectionContainer bg="stone">
      <div class="mb-8">
        <Link :href="route('berita.index')" class="text-xs font-semibold text-brand-sage hover:text-brand-teal transition-all">&larr; Kembali ke Daftar Berita</Link>
      </div>

      <article class="max-w-3xl mx-auto space-y-8 text-left">
        <!-- Title & Metadata -->
        <div class="space-y-4">
          <h1 class="font-display text-3xl sm:text-4xl lg:text-5xl text-brand-teal uppercase tracking-wider leading-tight font-bold">
            {{ post.title }}
          </h1>
          <div class="flex items-center space-x-4 text-xs text-brand-moss font-utility font-semibold">
            <span>Oleh: {{ post.author?.name || 'Admin' }}</span>
            <span>&bull;</span>
            <span>Rilis: {{ post.published_at }}</span>
          </div>
        </div>

        <!-- Featured Image -->
        <div class="flex justify-center py-4">
          <div class="relative w-full max-w-xl aspect-video">
            <div class="absolute inset-0 bg-transparent border border-brand-gold translate-x-2 translate-y-2 rounded" style="clip-path: url(#ogee-arch);"></div>
            <div class="w-full h-full bg-brand-slate overflow-hidden shadow" style="clip-path: url(#ogee-arch);">
              <img
                v-if="post.thumbnail"
                :src="post.thumbnail"
                :alt="post.title"
                class="w-full h-full object-cover"
                loading="lazy"
              />
              <div v-else class="w-full h-full flex items-center justify-center text-xs text-brand-moss select-none">[ISI: Foto Artikel]</div>
            </div>
          </div>
        </div>

        <!-- Content Body -->
        <div class="text-brand-navy/85 text-base sm:text-lg leading-relaxed whitespace-pre-wrap space-y-6 pt-4 font-sans">
          {{ post.content }}
        </div>
      </article>
    </SectionContainer>
  </PublicLayout>
</template>
