<script setup>
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import SectionContainer from '@/Components/SectionContainer.vue';
import Card from '@/Components/Card.vue';
import Divider from '@/Components/Divider.vue';

const props = defineProps({
  structureItems: {
    type: Array,
    default: () => [],
  },
});

// Define static placeholder pengurus if data from backend is empty
const defaultStructure = [
  { name: 'Ustadz H. Lukman Hakim, Lc.', position: 'Ketua Dewan Kemakmuran Masjid (DKM)', photo: '/images/pengurus_avatar.png' },
  { name: 'Bapak Ir. H. Bambang Widjojo', position: 'Sekretaris Umum', photo: '/images/pengurus_avatar.png' },
  { name: 'Bapak H. Teddy Hartanto, S.E.', position: 'Bendahara Masjid', photo: '/images/pengurus_avatar.png' },
];

const displayStructure = computed(() => {
  return props.structureItems && props.structureItems.length > 0 ? props.structureItems : defaultStructure;
});
</script>

<template>
  <PublicLayout>
    <Head>
      <title>Profil & Tentang Kami - Al Manar</title>
      <meta name="description" content="Ketahui sejarah berdirinya Al Manar, visi dan misi kami untuk kemaslahatan umat, serta struktur kepengurusan DKM Masjid." />
      <meta name="keywords" content="Profil Al Manar, Sejarah Al Manar, Visi Misi DKM, Pengurus Masjid" />
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

    <!-- Header Banner -->
    <SectionContainer bg="slate" :compact="true" class="border-b border-brand-moss/10">
      <div class="text-center py-6">
        <span class="font-utility text-xs uppercase tracking-[0.25em] text-brand-gold font-bold">Tentang Kami</span>
        <h1 class="font-display text-3xl sm:text-4xl lg:text-5xl text-brand-teal uppercase tracking-wider mt-2">Profil Al Manar</h1>
      </div>
    </SectionContainer>

    <!-- Sejarah Section -->
    <SectionContainer bg="stone">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
        <div class="lg:col-span-7 space-y-6 text-left">
          <h2 class="font-display text-2xl sm:text-3xl text-brand-teal uppercase tracking-wider">Sejarah Singkat</h2>
          <div class="text-brand-navy/80 text-sm leading-relaxed space-y-4 font-sans">
            <p>
              Masjid dan Lembaga Dakwah Al Manar dirintis pembangunannya sejak pertengahan tahun 2018 oleh tokoh masyarakat, ulama setempat, dan para muwakif. Cita-cita awal pendirian yayasan ini adalah mewujudkan pusat kajian Islam terpadu yang memadukan kegiatan ibadah rutin dengan kemaslahatan sosial kemanusiaan bagi warga sekitar Tangerang.
            </p>
            <p>
              Melalui gotong royong dan dukungan umat, gedung utama Masjid Al Manar resmi dirampungkan pada awal tahun 2020. Kini, Al Manar tidak hanya berfungsi sebagai sarana ibadah shalat berjamaah, melainkan juga aktif membina ratusan santri TPQ gratis, mengelola pelayanan armada ambulans siaga darurat gratis, serta menyalurkan santunan beasiswa dhuafa bulanan.
            </p>
          </div>
        </div>
        <div class="lg:col-span-5 flex justify-center">
          <div class="relative w-full max-w-xs aspect-[3/4] group">
            <div class="absolute inset-0 bg-transparent border-2 border-brand-gold translate-x-3 translate-y-3 rounded transition-transform duration-300 group-hover:translate-x-1.5 group-hover:translate-y-1.5" style="clip-path: url(#ogee-arch);"></div>
            <div class="w-full h-full bg-brand-slate overflow-hidden shadow-lg" style="clip-path: url(#ogee-arch);">
              <img src="/images/hero.png" alt="Masjid Al Manar" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" />
            </div>
          </div>
        </div>
      </div>
    </SectionContainer>

    <Divider />

    <!-- Visi Misi Section -->
    <SectionContainer bg="stone">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-12 text-left">
        <div class="bg-brand-slate/40 p-8 rounded border border-brand-moss/10 space-y-4">
          <h2 class="font-display text-2xl text-brand-teal uppercase tracking-wider">Visi</h2>
          <p class="text-brand-navy/85 text-sm leading-relaxed">
            Menjadi pusat dakwah Islamiyah yang mandiri, moderat, serta unggul dalam mencetak generasi qur'ani berakhlak karimah untuk kemaslahatan umat.
          </p>
        </div>
        
        <div class="bg-brand-slate/40 p-8 rounded border border-brand-moss/10 space-y-4">
          <h2 class="font-display text-2xl text-brand-teal uppercase tracking-wider">Misi</h2>
          <ul class="list-disc pl-5 text-brand-navy/85 text-sm leading-relaxed space-y-2 font-sans">
            <li>Menyelenggarakan kajian keislaman yang damai, inklusif, dan berlandaskan Al-Qur'an serta Sunnah.</li>
            <li>Mengelola program sosial santunan kemanusiaan dan ambulans siaga darurat secara amanah serta transparan.</li>
            <li>Membina pendidikan Al-Qur'an anak-anak (TPQ/Tahfidz) secara terarah dan profesional tanpa membebankan biaya kepada dhuafa.</li>
          </ul>
        </div>
      </div>
    </SectionContainer>

    <Divider />

    <!-- Kepengurusan Section -->
    <SectionContainer bg="stone">
      <div class="text-center max-w-2xl mx-auto mb-16 space-y-2">
        <span class="font-utility text-xs uppercase tracking-[0.25em] text-brand-gold font-bold">DKM Al Manar</span>
        <h2 class="font-display text-3xl text-brand-teal uppercase tracking-wider">Struktur Kepengurusan</h2>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-8">
        <Card v-for="member in displayStructure" :key="member.name" :hoverable="true">
          <template #image>
            <div class="aspect-square bg-brand-slate flex items-center justify-center text-xs text-brand-moss select-none overflow-hidden relative">
              <img
                v-if="member.photo"
                :src="member.photo"
                :alt="member.name"
                class="w-full h-full object-cover"
                loading="lazy"
              />
              <span v-else>[ISI: Foto Resmi {{ member.name }}]</span>
            </div>
          </template>
          <template #title>
            <h3 class="font-display text-lg text-brand-teal text-center font-bold">{{ member.name }}</h3>
          </template>
          <div class="text-center font-utility text-xs text-brand-moss uppercase tracking-wider font-semibold -mt-2">
            {{ member.position }}
          </div>
        </Card>
      </div>
    </SectionContainer>

    <Divider />

    <!-- Legalitas Section -->
    <SectionContainer bg="slate">
      <div class="max-w-3xl mx-auto space-y-6 text-center font-sans">
        <h2 class="font-display text-2xl text-brand-teal uppercase tracking-wider">Legalitas Lembaga</h2>
        <p class="text-brand-navy/70 text-sm leading-relaxed">
          Yayasan Al Manar secara hukum terdaftar sebagai Lembaga Keagamaan resmi di Kementerian Hukum dan HAM serta Kementerian Agama Republik Indonesia.
        </p>
        <div class="bg-white rounded border border-brand-moss/20 p-6 inline-block font-mono text-xs text-brand-navy/80 space-y-2 text-left shadow-sm">
          <div><span class="font-semibold text-brand-teal">No. Akta Notaris Yayasan:</span> Akta Notaris Akhmad, S.H., No. 24 Tanggal 12 Oktober 2018</div>
          <div><span class="font-semibold text-brand-teal">SK Pengesahan Kemenkumham:</span> AHU-0012345.AH.01.04.Tahun 2018</div>
          <div><span class="font-semibold text-brand-teal">Izin Operasional Kemenag RI:</span> Kd.15.02/1/PP.00.7/4321/2019</div>
        </div>
      </div>
    </SectionContainer>
  </PublicLayout>
</template>
