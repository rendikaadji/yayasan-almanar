<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import SectionContainer from '@/Components/SectionContainer.vue';
import BaseButton from '@/Components/BaseButton.vue';

const props = defineProps({
  program: {
    type: Object,
    default: () => ({
      title: '[ISI: Judul Program Detail]',
      category: 'Kajian',
      schedule: '[ISI: Waktu/Jadwal Pelaksanaan]',
      location: '[ISI: Lokasi Tempat Kegiatan]',
      description: '[ISI: Deskripsi lengkap program kegiatan, menjelaskan tujuan, teknis kegiatan, pembimbing, materi kajian, dan benefit lainnya bagi jamaah.]',
      image: null,
    }),
  },
});
</script>

<template>
  <PublicLayout>
    <Head>
      <title>{{ program.title }} - Al Manar</title>
      <meta name="description" content="Informasi detail program dakwah, pendidikan Quran, dan santunan sosial Al Manar Masjid." />
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
        <Link :href="route('program.index')" class="text-xs font-semibold text-brand-sage hover:text-brand-teal transition-all">&larr; Kembali ke Daftar Program</Link>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
        <!-- Visual -->
        <div class="lg:col-span-5 flex justify-center">
          <div class="relative w-full max-w-sm aspect-[3/4]">
            <div class="absolute inset-0 bg-transparent border-2 border-brand-gold translate-x-3 translate-y-3 rounded" style="clip-path: url(#ogee-arch);"></div>
            <div class="w-full h-full bg-brand-slate overflow-hidden shadow-xl" style="clip-path: url(#ogee-arch);">
              <img
                v-if="program.image"
                :src="program.image"
                :alt="program.title"
                class="w-full h-full object-cover"
                loading="lazy"
              />
              <div v-else class="w-full h-full flex items-center justify-center text-xs text-brand-moss select-none">[ISI: Foto Kegiatan]</div>
            </div>
          </div>
        </div>

        <!-- Detail Information -->
        <div class="lg:col-span-7 space-y-6">
          <div class="space-y-2">
            <span
              class="px-2.5 py-0.5 rounded text-[10px] font-utility font-bold uppercase tracking-wider bg-brand-sage/10 text-brand-sage border border-brand-sage/20 inline-block"
            >
              {{ program.category }}
            </span>
            <h1 class="font-display text-3xl sm:text-4xl text-brand-teal uppercase tracking-wider font-bold">
              {{ program.title }}
            </h1>
          </div>

          <div class="bg-brand-slate/40 p-6 rounded border border-brand-moss/10 space-y-4">
            <h3 class="font-utility text-xs font-bold text-brand-moss uppercase tracking-wider">Detail Pelaksanaan</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
              <div>
                <span class="block text-brand-navy/60 text-xs">Jadwal:</span>
                <span class="font-semibold text-brand-teal">{{ program.schedule }}</span>
              </div>
              <div>
                <span class="block text-brand-navy/60 text-xs">Lokasi:</span>
                <span class="font-semibold text-brand-teal">{{ program.location }}</span>
              </div>
            </div>
          </div>

          <div class="space-y-3">
            <h3 class="font-utility text-xs font-bold text-brand-moss uppercase tracking-wider">Deskripsi Kegiatan</h3>
            <p class="text-brand-navy/80 text-sm leading-relaxed whitespace-pre-wrap">
              {{ program.description }}
            </p>
          </div>
        </div>
      </div>
    </SectionContainer>
  </PublicLayout>
</template>
