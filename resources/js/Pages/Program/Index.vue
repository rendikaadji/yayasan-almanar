<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import SectionContainer from '@/Components/SectionContainer.vue';
import Card from '@/Components/Card.vue';

const props = defineProps({
  programs: {
    type: Array,
    default: () => [],
  },
});

const activeCategory = ref('Semua');
const categories = ['Semua', 'Kajian', 'Pendidikan', 'Sosial'];

const defaultPrograms = [
  { id: 1, title: '[ISI: Kajian Tafsir Al-Qur\'an]', category: 'Kajian', schedule: '[ISI: Setiap Sabtu Ba\'da Subuh]', location: '[ISI: Masjid Al Manar]', description: '[ISI: Kajian tafsir tematik dibimbing oleh Dewan Asatidzah Al Manar.]', image: null, slug: 'kajian' },
  { id: 2, title: '[ISI: TPA / TPQ Anak]', category: 'Pendidikan', schedule: '[ISI: Senin - Kamis, Jam 16.00]', location: '[ISI: Aula Belakang]', description: '[ISI: Pengajaran baca tulis Al-Qur\'an, fiqih ibadah dasar, dan hafalan hadits pendek.]', image: null, slug: 'tpq' },
  { id: 3, title: '[ISI: Santunan Yatim & Dhuafa]', category: 'Sosial', schedule: '[ISI: Setiap Tanggal 10]', location: '[ISI: Kantor Sekretariat]', description: '[ISI: Pembagian paket sembako bulanan dan dana beasiswa pendidikan bagi yang membutuhkan.]', image: null, slug: 'santunan' },
];

const displayPrograms = computed(() => {
  return props.programs && props.programs.length > 0 ? props.programs : defaultPrograms;
});

const filteredPrograms = computed(() => {
  if (activeCategory.value === 'Semua') {
    return displayPrograms.value;
  }
  return displayPrograms.value.filter((p) => p.category === activeCategory.value);
});
</script>

<template>
  <PublicLayout>
    <Head>
      <title>Program & Kegiatan Dakwah - Al Manar</title>
      <meta name="description" content="Jelajahi jadwal kajian rutin, program tahfidz Al-Qur'an anak-dewasa, santunan anak yatim, dan layanan sosial kemanusiaan Al Manar." />
      <meta name="keywords" content="Program Dakwah, Kajian Masjid, TPQ Anak, Tahfidz Al Manar, Layanan Sosial Masjid" />
    </Head>

    <!-- Header Banner -->
    <SectionContainer bg="slate" :compact="true" class="border-b border-brand-moss/10">
      <div class="text-center py-6">
        <span class="font-utility text-xs uppercase tracking-[0.25em] text-brand-gold font-bold">Layanan Jamaah</span>
        <h1 class="font-display text-3xl sm:text-4xl lg:text-5xl text-brand-teal uppercase tracking-wider mt-2">Program & Kegiatan</h1>
      </div>
    </SectionContainer>

    <!-- Filters and Grid -->
    <SectionContainer bg="stone">
      <!-- Category Filter Tabs -->
      <div class="flex flex-wrap justify-center gap-2 mb-12">
        <button
          v-for="cat in categories"
          :key="cat"
          @click="activeCategory = cat"
          class="px-5 py-2 rounded text-xs font-utility font-bold uppercase tracking-wider transition-all duration-300 border focus:outline-none cursor-pointer"
          :class="[
            activeCategory === cat
              ? 'bg-brand-sage text-brand-stone border-transparent shadow-md'
              : 'bg-white text-brand-navy/70 border-brand-slate hover:bg-brand-slate/40'
          ]"
        >
          {{ cat === 'Semua' ? 'Semua Kategori' : cat }}
        </button>
      </div>

      <!-- Programs Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <Card v-for="program in filteredPrograms" :key="program.id" :hoverable="true">
          <template #image>
            <div class="aspect-video bg-brand-slate flex items-center justify-center text-xs text-brand-moss select-none overflow-hidden relative">
              <img
                v-if="program.image"
                :src="program.image"
                :alt="program.title"
                class="w-full h-full object-cover"
                loading="lazy"
              />
              <span v-else>[ISI: Foto {{ program.title }}]</span>
            </div>
          </template>
          <template #tag>
            <span
              class="px-2 py-0.5 rounded text-[10px] font-utility font-bold uppercase tracking-wider"
              :class="[
                program.category === 'Kajian' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : '',
                program.category === 'Pendidikan' ? 'bg-amber-50 text-amber-700 border border-amber-200' : '',
                program.category === 'Sosial' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : '',
              ]"
            >
              {{ program.category }}
            </span>
          </template>
          <template #title>
            <Link :href="route('program.show', program.slug || 'slug')">
              <h3 class="font-display text-xl text-brand-teal font-semibold hover:text-brand-sage transition-colors duration-300">
                {{ program.title }}
              </h3>
            </Link>
          </template>
          <p class="line-clamp-3 text-sm text-brand-navy/80">{{ program.description }}</p>
          <template #footer>
            <div class="flex flex-col space-y-1 text-xs text-brand-navy/60 font-utility text-left">
              <div>Waktu: <span class="text-brand-navy font-medium">{{ program.schedule }}</span></div>
              <div>Lokasi: <span class="text-brand-navy font-medium">{{ program.location }}</span></div>
            </div>
          </template>
        </Card>
      </div>

      <div v-if="filteredPrograms.length === 0" class="text-center py-12 text-brand-navy/40 italic">
        Belum ada program untuk kategori ini.
      </div>
    </SectionContainer>
  </PublicLayout>
</template>
