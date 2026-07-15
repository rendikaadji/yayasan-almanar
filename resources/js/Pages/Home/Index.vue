<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import SectionContainer from '@/Components/SectionContainer.vue';
import BaseButton from '@/Components/BaseButton.vue';
import Card from '@/Components/Card.vue';
import Divider from '@/Components/Divider.vue';

const props = defineProps({
  programs: {
    type: Array,
    default: () => [],
  },
  posts: {
    type: Array,
    default: () => [],
  },
  donationProgram: {
    type: Object,
    default: null,
  },
  testimonials: {
    type: Array,
    default: () => [],
  },
});

const formatCurrency = (val) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(val);
};
</script>

<template>
  <PublicLayout>
    <Head>
      <title>Beranda - Al Manar Masjid & Lembaga Dakwah</title>
      <meta name="description" content="Selamat datang di portal resmi Al Manar. Temukan jadwal kajian rutin, program pendidikan Al-Qur'an, transparansi penyaluran donasi, dan berbagai kegiatan kemanusiaan." />
      <meta name="keywords" content="Al Manar, DKM Al Manar, masjid, dakwah, kajian islam, donasi masjid, santunan sosial" />
    </Head>

    <!-- SVG Ogee Clip Path Definition -->
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

    <!-- 1. Hero Section -->
    <SectionContainer bg="stone" class="relative overflow-hidden">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
        <!-- Hero Text Content -->
        <div class="lg:col-span-7 space-y-6 text-left">
          <div class="inline-flex items-center space-x-2 px-3 py-1 rounded bg-brand-sage/10 border border-brand-sage/20">
            <span class="w-2 h-2 rounded-full bg-brand-gold animate-pulse"></span>
            <span class="font-utility text-[10px] font-bold tracking-widest text-brand-sage uppercase">Selamat Datang di Al Manar</span>
          </div>

          <h1 class="font-display text-4xl sm:text-5xl lg:text-6xl tracking-wider text-brand-teal leading-tight uppercase">
            Tenang,<br />Berdaya, Berbagi.
          </h1>

          <p class="text-brand-navy/80 text-base sm:text-lg leading-relaxed max-w-xl">
            Al Manar hadir sebagai wadah spiritual yang mempersatukan umat lewat dakwah Islam moderat, pembinaan akhlak mulia, dan transparansi donasi kemanusiaan demi kemaslahatan bersama.
          </p>

          <div class="flex flex-col sm:flex-row gap-4 pt-4">
            <BaseButton :href="route('donasi.index')" variant="primary">
              Donasi Sekarang
            </BaseButton>
            <BaseButton :href="route('program.index')" variant="secondary">
              Jelajahi Program
            </BaseButton>
          </div>
        </div>

        <!-- Hero Image Container (Ogee Arch Mask) -->
        <div class="lg:col-span-5 flex justify-center">
          <div class="relative w-full max-w-sm sm:max-w-md aspect-[3/4] group">
            <!-- Decorative Gold Frame Behind Image -->
            <div 
              class="absolute inset-0 bg-transparent border-2 border-brand-gold translate-x-3 translate-y-3 transition-transform duration-500 group-hover:translate-x-1.5 group-hover:translate-y-1.5"
              style="clip-path: url(#ogee-arch);"
            ></div>
            
            <!-- Masked Image -->
            <div 
              class="w-full h-full bg-brand-slate overflow-hidden shadow-xl"
              style="clip-path: url(#ogee-arch);"
            >
              <img 
                src="/images/hero.png" 
                alt="Al Manar Mosque" 
                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
              />
            </div>
          </div>
        </div>
      </div>
    </SectionContainer>

    <!-- 2. Achievement Stats Section -->
    <SectionContainer bg="slate" :compact="true" class="border-y border-brand-moss/10">
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-8 text-center divide-y sm:divide-y-0 sm:divide-x divide-brand-moss/20">
        <div class="py-4 sm:py-0">
          <span class="block font-display text-4xl lg:text-5xl text-brand-sage">500+</span>
          <span class="block font-utility text-[10px] uppercase tracking-widest text-brand-moss mt-2 font-bold">Jamaah Aktif Binaan</span>
        </div>
        <div class="py-4 sm:py-0">
          <span class="block font-display text-4xl lg:text-5xl text-brand-sage">3+ Utama</span>
          <span class="block font-utility text-[10px] uppercase tracking-widest text-brand-moss mt-2 font-bold">Program Dakwah & Sosial</span>
        </div>
        <div class="py-4 sm:py-0">
          <span class="block font-display text-4xl lg:text-5xl text-brand-sage">2018</span>
          <span class="block font-utility text-[10px] uppercase tracking-widest text-brand-moss mt-2 font-bold">Tahun Berdiri</span>
        </div>
      </div>
    </SectionContainer>

    <!-- 3. Highlighted Programs Section -->
    <SectionContainer bg="stone">
      <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">
        <span class="font-utility text-xs uppercase tracking-[0.25em] text-brand-gold font-bold">Layanan Kami</span>
        <h2 class="font-display text-3xl sm:text-4xl text-brand-teal uppercase tracking-wider">Program Unggulan</h2>
        <p class="text-brand-navy/70 text-sm">
          Jelajahi program keagamaan, pembinaan hafalan Al-Qur'an anak-anak, santunan fakir miskin, dan layanan operasional kemanusiaan yang kami kelola secara aktif.
        </p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Loop over dynamic programs from SQLite -->
        <template v-if="programs && programs.length > 0">
          <Card v-for="program in programs" :key="program.id" :hoverable="true">
            <template #image>
              <div class="aspect-video bg-brand-slate flex items-center justify-center text-xs text-brand-moss select-none overflow-hidden relative">
                <img 
                  v-if="program.image" 
                  :src="program.image" 
                  :alt="program.title" 
                  class="w-full h-full object-cover transition-transform duration-700 hover:scale-105"
                  loading="lazy"
                />
                <span v-else class="italic font-utility">No Image</span>
              </div>
            </template>
            <template #tag>
              <span class="px-2 py-0.5 rounded bg-brand-sage/10 text-brand-sage font-utility text-[9px] font-bold uppercase tracking-wider">
                {{ program.category }}
              </span>
            </template>
            <template #title>
              <h3 class="font-display text-xl text-brand-teal font-semibold group-hover:text-brand-sage transition-colors duration-300">
                {{ program.title }}
              </h3>
            </template>
            <p class="line-clamp-3 text-sm text-brand-navy/80">{{ program.description }}</p>
            <template #footer>
              <span class="text-xs text-brand-moss font-utility font-medium">{{ program.schedule }}</span>
              <Link :href="route('program.show', program.slug)" class="text-xs font-bold text-brand-sage hover:text-brand-teal transition-colors">Detail &rarr;</Link>
            </template>
          </Card>
        </template>

        <!-- Static Fallbacks if SQLite is empty -->
        <template v-else>
          <!-- Program Card 1 -->
          <Card>
            <template #image>
              <div class="aspect-video bg-brand-slate flex items-center justify-center text-xs text-brand-moss select-none">[ISI: Thumbnail Kegiatan Kajian]</div>
            </template>
            <template #tag>
              <span class="px-2 py-0.5 rounded bg-brand-sage/10 text-brand-sage font-utility text-[9px] font-bold uppercase tracking-wider">Kajian Dakwah</span>
            </template>
            <template #title>
              <h3 class="font-display text-xl text-brand-teal font-semibold group-hover:text-brand-sage transition-colors duration-300">[ISI: Judul Program Kajian Rutin]</h3>
            </template>
            [ISI: Deskripsi Singkat Program Kajian - Ringkasan materi kajian mingguan/bulanan beserta pengisi materi/ustadz pembimbing.]
            <template #footer>
              <span class="text-xs text-brand-moss font-utility font-medium">[ISI: Jadwal Kajian]</span>
              <Link :href="route('program.index')" class="text-xs font-bold text-brand-sage hover:text-brand-teal transition-colors">Detail &rarr;</Link>
            </template>
          </Card>

          <!-- Program Card 2 -->
          <Card>
            <template #image>
              <div class="aspect-video bg-brand-slate flex items-center justify-center text-xs text-brand-moss select-none">[ISI: Thumbnail TPQ & Tahfidz]</div>
            </template>
            <template #tag>
              <span class="px-2 py-0.5 rounded bg-brand-sage/10 text-brand-sage font-utility text-[9px] font-bold uppercase tracking-wider">Pendidikan</span>
            </template>
            <template #title>
              <h3 class="font-display text-xl text-brand-teal font-semibold group-hover:text-brand-sage transition-colors duration-300">[ISI: Judul Program Pendidikan Quran]</h3>
            </template>
            [ISI: Deskripsi Singkat TPQ/Tahfidz - Ringkasan info pendaftaran santri, kurikulum menghafal Al-Qur'an, dan target kelulusan.]
            <template #footer>
              <span class="text-xs text-brand-moss font-utility font-medium">[ISI: Kelompok Usia/Waktu]</span>
              <Link :href="route('program.index')" class="text-xs font-bold text-brand-sage hover:text-brand-teal transition-colors">Detail &rarr;</Link>
            </template>
          </Card>

          <!-- Program Card 3 -->
          <Card>
            <template #image>
              <div class="aspect-video bg-brand-slate flex items-center justify-center text-xs text-brand-moss select-none">[ISI: Thumbnail Aksi Sosial]</div>
            </template>
            <template #tag>
              <span class="px-2 py-0.5 rounded bg-brand-sage/10 text-brand-sage font-utility text-[9px] font-bold uppercase tracking-wider">Sosial Kemanusiaan</span>
            </template>
            <template #title>
              <h3 class="font-display text-xl text-brand-teal font-semibold group-hover:text-brand-sage transition-colors duration-300">[ISI: Judul Program Sosial Peduli Umat]</h3>
            </template>
            [ISI: Deskripsi Singkat Program Sosial - Informasi pelayanan santunan, bantuan logistik, pembagian makanan gratis, dan pengelolaan ambulans.]
            <template #footer>
              <span class="text-xs text-brand-moss font-utility font-medium">[ISI: Lokasi/Target]</span>
              <Link :href="route('program.index')" class="text-xs font-bold text-brand-sage hover:text-brand-teal transition-colors">Detail &rarr;</Link>
            </template>
          </Card>
        </template>
      </div>

      <div class="text-center mt-12">
        <BaseButton :href="route('program.index')" variant="secondary">
          Lihat Semua Program & Kegiatan
        </BaseButton>
      </div>
    </SectionContainer>

    <!-- Signature Divider -->
    <Divider />

    <!-- 4. Latest News Section -->
    <SectionContainer bg="stone">
      <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-4">
        <div>
          <span class="font-utility text-xs uppercase tracking-[0.25em] text-brand-gold font-bold">Kabar Terbaru</span>
          <h2 class="font-display text-3xl sm:text-4xl text-brand-teal uppercase tracking-wider mt-2">Berita & Dokumentasi</h2>
        </div>
        <BaseButton :href="route('berita.index')" variant="ghost" class="group">
          Semua Berita <span class="transition-transform group-hover:translate-x-1 inline-block">&rarr;</span>
        </BaseButton>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Loop over dynamic posts from SQLite -->
        <template v-if="posts && posts.length > 0">
          <Card v-for="post in posts" :key="post.id" :hoverable="true" bg="slate">
            <template #image>
              <div class="aspect-video bg-brand-stone flex items-center justify-center text-xs text-brand-moss select-none overflow-hidden relative">
                <img 
                  v-if="post.thumbnail" 
                  :src="post.thumbnail" 
                  :alt="post.title" 
                  class="w-full h-full object-cover transition-transform duration-700 hover:scale-105"
                  loading="lazy"
                />
                <span v-else class="italic font-utility">No Image</span>
              </div>
            </template>
            <template #tag>
              <span class="font-utility text-[10px] text-brand-moss">{{ post.published_at ? post.published_at.substring(0, 10) : '' }}</span>
            </template>
            <template #title>
              <h3 class="font-display text-lg text-brand-teal font-semibold hover:text-brand-sage transition-colors duration-200">
                {{ post.title }}
              </h3>
            </template>
            <p class="line-clamp-3 text-sm text-brand-navy/80">{{ post.content }}</p>
            <template #footer>
              <span class="text-xs text-brand-moss/80 font-utility">Ditulis: {{ post.author?.name || 'Admin' }}</span>
              <Link :href="route('berita.show', post.slug)" class="text-xs font-bold text-brand-sage hover:text-brand-teal">Baca &rarr;</Link>
            </template>
          </Card>
        </template>

        <!-- Static Fallbacks if SQLite is empty -->
        <template v-else>
          <Card :hoverable="true" bg="slate">
            <template #image>
              <div class="aspect-video bg-brand-stone flex items-center justify-center text-xs text-brand-moss select-none">[ISI: Thumbnail Foto 1]</div>
            </template>
            <template #tag>
              <span class="font-utility text-[10px] text-brand-moss">[ISI: Tanggal Rilis]</span>
            </template>
            <template #title>
              <h3 class="font-display text-lg text-brand-teal font-semibold hover:text-brand-sage transition-colors duration-200">[ISI: Judul Artikel Berita 1]</h3>
            </template>
            [ISI: Cuplikan Konten Artikel...]
            <template #footer>
              <span class="text-xs text-brand-moss/80 font-utility">[ISI: Penulis/Editor]</span>
              <Link :href="route('berita.index')" class="text-xs font-bold text-brand-sage hover:text-brand-teal">Baca</Link>
            </template>
          </Card>
        </template>
      </div>
    </SectionContainer>

    <!-- 5. Testimonial Section -->
    <SectionContainer bg="slate">
      <div class="text-center max-w-2xl mx-auto mb-12 space-y-2">
        <span class="font-utility text-xs uppercase tracking-[0.25em] text-brand-gold font-bold">Testimoni</span>
        <h2 class="font-display text-3xl text-brand-teal uppercase tracking-wider">Suara Jamaah & Donatur</h2>
      </div>

      <div class="max-w-4xl mx-auto">
        <template v-if="testimonials && testimonials.length > 0">
          <div v-for="testi in testimonials" :key="testi.id" class="bg-brand-stone rounded p-8 md:p-12 border border-brand-moss/10 shadow-sm relative overflow-hidden group mb-6">
            <span class="absolute -top-6 -left-2 text-9xl text-brand-gold/10 font-display select-none pointer-events-none">&ldquo;</span>
            <div class="relative space-y-6">
              <blockquote class="text-brand-navy/90 text-base md:text-lg italic leading-relaxed text-center">
                "{{ testi.message }}"
              </blockquote>
              <div class="flex flex-col items-center">
                <span class="font-display text-lg text-brand-sage font-bold">{{ testi.name }}</span>
                <span class="font-utility text-[10px] text-brand-moss uppercase tracking-widest mt-1">Donatur & Jamaah</span>
              </div>
            </div>
          </div>
        </template>

        <!-- Static Fallback -->
        <template v-else>
          <div class="bg-brand-stone rounded p-8 md:p-12 border border-brand-moss/10 shadow-sm relative overflow-hidden group">
            <span class="absolute -top-6 -left-2 text-9xl text-brand-gold/10 font-display select-none pointer-events-none">&ldquo;</span>
            <div class="relative space-y-6">
              <blockquote class="text-brand-navy/90 text-base md:text-lg italic leading-relaxed text-center">
                "[ISI: Kutipan Testimoni Jamaah/Donatur - Menyampaikan pengalaman pribadi mereka mengenai kegunaan fasilitas masjid, transparansi dana program, atau kualitas bimbingan keagamaan di Al Manar.]"
              </blockquote>
              <div class="flex flex-col items-center">
                <span class="font-display text-lg text-brand-sage font-bold">[ISI: Nama Tokoh/Jamaah]</span>
                <span class="font-utility text-[10px] text-brand-moss uppercase tracking-widest mt-1">[ISI: Status/Jabatan]</span>
              </div>
            </div>
          </div>
        </template>
      </div>
    </SectionContainer>

    <!-- 6. Donation Call to Action (Arch Card) -->
    <SectionContainer bg="stone">
      <div v-if="donationProgram" class="max-w-4xl mx-auto bg-brand-teal text-brand-stone rounded-2xl p-8 md:p-12 relative overflow-hidden shadow-2xl border border-brand-sage/20 group">
        <!-- Mosque Background Image inside Ogee Arch -->
        <div 
          class="absolute inset-y-0 right-0 w-1/2 pointer-events-none select-none hidden md:block"
          style="clip-path: url(#ogee-arch);"
        >
          <img 
            :src="donationProgram.image || '/images/hero.png'" 
            alt="Mosque background" 
            class="w-full h-full object-cover object-center opacity-30 mix-blend-luminosity transform scale-105 group-hover:scale-100 transition-transform duration-1000"
          />
          <div class="absolute inset-0 bg-gradient-to-r from-brand-teal via-brand-teal/40 to-transparent"></div>
        </div>

        <!-- Subtle Ogee Arch Outline Background Decoration -->
        <div 
          class="absolute inset-y-0 right-0 w-1/2 border-l border-brand-gold/20 pointer-events-none select-none hidden md:block"
          style="clip-path: url(#ogee-arch); background: linear-gradient(90deg, transparent 0%, rgba(199, 168, 112, 0.05) 100%);"
        ></div>

        <div class="relative space-y-8 max-w-2xl text-left">
          <div class="space-y-4">
            <span class="font-utility text-xs uppercase tracking-[0.25em] text-brand-gold font-bold">Kanal Amal Jariyah</span>
            <h2 class="font-display text-3xl sm:text-4xl text-brand-stone uppercase tracking-wider">Mari Berbagi Kebaikan</h2>
            <h3 class="text-brand-gold font-display text-lg font-bold">{{ donationProgram.title }}</h3>
            <p class="text-brand-stone/80 text-sm leading-relaxed">
              {{ donationProgram.description }}
            </p>
          </div>

          <!-- Progress Bar -->
          <div class="space-y-3 bg-brand-sage/20 p-6 rounded-lg border border-brand-sage/30">
            <div class="flex justify-between text-xs font-utility uppercase tracking-wider font-semibold">
              <span>Dana Terkumpul</span>
              <span class="text-brand-gold">
                {{ Math.round((donationProgram.collected_amount / (donationProgram.target_amount || 1)) * 100) }}%
              </span>
            </div>
            <!-- Custom Progress Bar -->
            <div class="h-3 w-full bg-brand-stone/10 rounded-full overflow-hidden border border-brand-stone/10">
              <div class="h-full bg-brand-gold rounded-full transition-all duration-1000" :style="`width: ${Math.min((donationProgram.collected_amount / (donationProgram.target_amount || 1)) * 100, 100)}%`"></div>
            </div>
            <div class="flex justify-between text-sm pt-2">
              <span class="font-semibold text-brand-stone">{{ formatCurrency(donationProgram.collected_amount) }}</span>
              <span class="text-brand-stone/70">Target: {{ formatCurrency(donationProgram.target_amount) }}</span>
            </div>
          </div>

          <div class="flex flex-col sm:flex-row gap-6 pt-4 items-center">
            <BaseButton :href="route('donasi.index')" variant="primary" class="!bg-brand-gold !text-brand-teal hover:!bg-brand-stone focus:ring-brand-stone w-full sm:w-auto">
              Salurkan Donasi
            </BaseButton>
            <div class="text-xs text-brand-stone/70 text-center sm:text-left">
              Transfer Bank: BSI Rekening <span class="font-mono text-brand-gold font-bold">712-3456-7890</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Static Fallback if empty -->
      <div v-else class="max-w-4xl mx-auto bg-brand-teal text-brand-stone rounded-2xl p-8 md:p-12 relative overflow-hidden shadow-2xl border border-brand-sage/20 group">
        <!-- Mosque Background Image inside Ogee Arch -->
        <div 
          class="absolute inset-y-0 right-0 w-1/2 pointer-events-none select-none hidden md:block"
          style="clip-path: url(#ogee-arch);"
        >
          <img 
            src="/images/hero.png" 
            alt="Mosque background" 
            class="w-full h-full object-cover object-center opacity-30 mix-blend-luminosity transform scale-105 group-hover:scale-100 transition-transform duration-1000"
          />
          <div class="absolute inset-0 bg-gradient-to-r from-brand-teal via-brand-teal/40 to-transparent"></div>
        </div>

        <!-- Subtle Ogee Arch Outline Background Decoration -->
        <div 
          class="absolute inset-y-0 right-0 w-1/2 border-l border-brand-gold/20 pointer-events-none select-none hidden md:block"
          style="clip-path: url(#ogee-arch); background: linear-gradient(90deg, transparent 0%, rgba(199, 168, 112, 0.05) 100%);"
        ></div>

        <div class="relative space-y-8 max-w-2xl text-left">
          <div class="space-y-4">
            <span class="font-utility text-xs uppercase tracking-[0.25em] text-brand-gold font-bold">Kanal Amal Jariyah</span>
            <h2 class="font-display text-3xl sm:text-4xl text-brand-stone uppercase tracking-wider">Mari Berbagi Kebaikan</h2>
            <p class="text-brand-stone/80 text-sm leading-relaxed">
              [ISI: Deskripsi Ajakan Donasi - Memberikan motivasi keutamaan bersedekah, transparansi penyaluran dana program donasi kemanusiaan.]
            </p>
          </div>
          <div class="flex flex-col sm:flex-row gap-6 pt-4 items-center">
            <BaseButton :href="route('donasi.index')" variant="primary" class="!bg-brand-gold !text-brand-teal hover:!bg-brand-stone focus:ring-brand-stone w-full sm:w-auto">
              Salurkan Donasi
            </BaseButton>
          </div>
        </div>
      </div>
    </SectionContainer>
  </PublicLayout>
</template>
