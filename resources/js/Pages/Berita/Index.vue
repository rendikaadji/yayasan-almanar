<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import SectionContainer from '@/Components/SectionContainer.vue';
import Card from '@/Components/Card.vue';

const props = defineProps({
  posts: {
    type: Array,
    default: () => [],
  },
});

const defaultPosts = [
  { id: 1, title: '[ISI: Judul Artikel Berita Kajian]', slug: 'berita-1', content: '[ISI: Ringkasan isi berita terbaru mengenai jalannya tabligh akbar atau peresmian program sosial.]', thumbnail: null, published_at: '[ISI: Tanggal Rilis]', author: { name: 'Admin' } },
  { id: 2, title: '[ISI: Pengumuman Jadwal Shalat Idul Fitri]', slug: 'berita-2', content: '[ISI: Laporan pengumuman pelaksanaan shalat Idul Fitri beserta nama khotib dan imam pembimbing.]', thumbnail: null, published_at: '[ISI: Tanggal Rilis]', author: { name: 'Admin' } },
];

const displayPosts = computed(() => {
  return props.posts && props.posts.length > 0 ? props.posts : defaultPosts;
});
</script>

<template>
  <PublicLayout>
    <Head>
      <title>Kabar & Artikel - Al Manar</title>
      <meta name="description" content="Baca warta kegiatan terbaru, artikel keagamaan, dan dokumentasi program donasi kemanusiaan Al Manar." />
      <meta name="keywords" content="Berita Al Manar, Berita Masjid, Artikel Keislaman, Dokumentasi Kegiatan" />
    </Head>

    <!-- Header Banner -->
    <SectionContainer bg="slate" :compact="true" class="border-b border-brand-moss/10">
      <div class="text-center py-6">
        <span class="font-utility text-xs uppercase tracking-[0.25em] text-brand-gold font-bold">Kabar Umat</span>
        <h1 class="font-display text-3xl sm:text-4xl lg:text-5xl text-brand-teal uppercase tracking-wider mt-2">Berita & Artikel</h1>
      </div>
    </SectionContainer>

    <!-- Listing Section -->
    <SectionContainer bg="stone">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <Card v-for="post in displayPosts" :key="post.id" :hoverable="true" bg="slate">
          <template #image>
            <div class="aspect-video bg-brand-stone flex items-center justify-center text-xs text-brand-moss select-none overflow-hidden relative">
              <img
                v-if="post.thumbnail"
                :src="post.thumbnail"
                :alt="post.title"
                class="w-full h-full object-cover"
                loading="lazy"
              />
              <span v-else>[ISI: Foto {{ post.title }}]</span>
            </div>
          </template>
          <template #tag>
            <span class="font-utility text-[10px] text-brand-moss font-semibold">
              {{ post.published_at ? post.published_at.substring(0, 10) : '' }}
            </span>
          </template>
          <template #title>
            <Link :href="route('berita.show', post.slug)">
              <h3 class="font-display text-xl text-brand-teal font-semibold hover:text-brand-sage transition-colors duration-300">
                {{ post.title }}
              </h3>
            </Link>
          </template>
          <p class="line-clamp-3 text-sm text-brand-navy/80">{{ post.content }}</p>
          <template #footer>
            <span class="text-xs text-brand-moss font-utility font-medium text-left">Ditulis oleh: {{ post.author?.name || 'Admin' }}</span>
            <Link :href="route('berita.show', post.slug)" class="text-xs font-bold text-brand-sage hover:text-brand-teal transition-colors">Baca &rarr;</Link>
          </template>
        </Card>
      </div>

      <div v-if="displayPosts.length === 0" class="text-center py-12 text-brand-navy/40 italic">
        Belum ada berita yang diterbitkan.
      </div>
    </SectionContainer>
  </PublicLayout>
</template>
