<script setup>
import { ref, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import SectionContainer from '@/Components/SectionContainer.vue';
import Card from '@/Components/Card.vue';

const props = defineProps({
  // Galleries passed from backend if any, otherwise fallback to static stubs
  galleries: {
    type: Array,
    default: () => [
      { id: 1, album_name: '[ISI: Dokumentasi Tabligh Akbar]', cover_image: null, items: [{ file_path: null }] },
      { id: 2, album_name: '[ISI: Kegiatan Santunan Sosial]', cover_image: null, items: [{ file_path: null }] },
    ],
  },
});

const activeLightboxGallery = ref(null);
const isLightboxOpen = ref(false);

const openLightbox = (gallery) => {
  activeLightboxGallery.value = gallery;
  isLightboxOpen.value = true;
};

const lightboxItemCount = computed(() => {
  return activeLightboxGallery.value?.items?.length || 0;
});

const modalWidthClass = computed(() => {
  const count = lightboxItemCount.value;
  if (count === 0 || count === 1) return 'max-w-md';
  if (count === 2) return 'max-w-2xl';
  return 'max-w-4xl';
});

const gridLayoutClass = computed(() => {
  const count = lightboxItemCount.value;
  if (count === 0) return 'flex justify-center items-center py-12';
  if (count === 1) return 'grid grid-cols-1 max-w-sm mx-auto';
  if (count === 2) return 'grid grid-cols-1 sm:grid-cols-2';
  return 'grid grid-cols-2 sm:grid-cols-3';
});
</script>

<template>
  <PublicLayout>
    <Head>
      <title>Galeri Dokumentasi Kegiatan - Al Manar</title>
      <meta name="description" content="Lihat kumpulan dokumentasi foto dan video dari kegiatan dakwah Islam, pengajaran Al-Qur'an, dan bimbingan jamaah Al Manar." />
      <meta name="keywords" content="Galeri Al Manar, Foto Masjid, Dokumentasi Kegiatan, Foto Aksi Kemanusiaan" />
    </Head>

    <!-- Header Banner -->
    <SectionContainer bg="slate" :compact="true" class="border-b border-brand-moss/10">
      <div class="text-center py-6">
        <span class="font-utility text-xs uppercase tracking-[0.25em] text-brand-gold font-bold">Dokumentasi Visual</span>
        <h1 class="font-display text-3xl sm:text-4xl lg:text-5xl text-brand-teal uppercase tracking-wider mt-2">Galeri Al Manar</h1>
      </div>
    </SectionContainer>

    <!-- Album Grid -->
    <SectionContainer bg="stone">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <Card v-for="gallery in galleries" :key="gallery.id" :hoverable="true" @click="openLightbox(gallery)" class="cursor-pointer">
          <template #image>
            <div class="aspect-video bg-brand-slate flex items-center justify-center text-xs text-brand-moss select-none relative group-hover:scale-105 transition-transform duration-500">
              <img
                v-if="gallery.cover_image"
                :src="gallery.cover_image"
                alt="Cover"
                class="w-full h-full object-cover"
                loading="lazy"
              />
              <span v-else>[ISI: Cover Album]</span>
              <div class="absolute inset-0 bg-brand-teal/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                <span class="px-3 py-1.5 rounded bg-white/95 text-brand-teal text-xs font-semibold tracking-wider uppercase font-utility shadow">Lihat Album</span>
              </div>
            </div>
          </template>
          <template #title>
            <h3 class="font-display text-lg text-brand-teal font-semibold text-center mt-2">{{ gallery.album_name }}</h3>
          </template>
          <div class="text-center text-xs text-brand-moss font-utility font-semibold">
            {{ gallery.items?.length || 0 }} Foto / Video
          </div>
        </Card>
      </div>

      <div v-if="galleries.length === 0" class="text-center py-12 text-brand-navy/40 italic">
        Belum ada dokumentasi galeri.
      </div>
    </SectionContainer>

    <!-- Lightbox Modal overlay -->
    <div v-show="isLightboxOpen" class="fixed inset-0 z-50 overflow-y-auto bg-brand-teal/90 backdrop-blur-md flex items-center justify-center p-4">
      <div
        v-if="activeLightboxGallery"
        :class="[
          'bg-white rounded-xl w-full border border-brand-slate shadow-2xl flex flex-col max-h-[90vh] transition-all duration-300 transform',
          modalWidthClass
        ]"
      >
        <!-- Header -->
        <div class="px-6 py-4 border-b border-brand-slate flex justify-between items-center bg-brand-stone/30">
          <h3 class="font-display text-lg text-brand-teal uppercase tracking-wider font-bold">
            Album: {{ activeLightboxGallery.album_name }}
          </h3>
          <button @click="isLightboxOpen = false" class="p-1 rounded hover:bg-brand-slate text-brand-navy focus:outline-none">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Body -->
        <div :class="['p-6 overflow-y-auto flex-1 gap-4', gridLayoutClass]">
          <div
            v-for="(item, idx) in activeLightboxGallery.items"
            :key="idx"
            class="aspect-square bg-brand-slate rounded border border-brand-slate flex items-center justify-center text-xs text-brand-moss select-none overflow-hidden relative group"
          >
            <img
              v-if="item.file_path"
              :src="item.file_path"
              alt="Photo item"
              class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
              loading="lazy"
            />
            <span v-else>[ISI: Foto {{ idx + 1 }}]</span>
          </div>
          <div v-if="!activeLightboxGallery.items || activeLightboxGallery.items.length === 0" class="col-span-full py-12 text-center text-brand-navy/40 italic">
            Album ini kosong.
          </div>
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 border-t border-brand-slate bg-brand-stone/30 flex justify-end">
          <BaseButton @click="isLightboxOpen = false" variant="primary" class="!py-2 !px-4">
            Tutup Galeri
          </BaseButton>
        </div>
      </div>
    </div>
  </PublicLayout>
</template>
