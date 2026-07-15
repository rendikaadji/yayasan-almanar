<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import SectionContainer from '@/Components/SectionContainer.vue';
import BaseButton from '@/Components/BaseButton.vue';

const props = defineProps({
  donationProgram: {
    type: Object,
    default: () => ({
      title: '[ISI: Judul Program Donasi Detail]',
      description: '[ISI: Deskripsi lengkap program donasi, menjelaskan tujuan aksi penggalangan dana, keutamaan bersedekah, perincian rencana alokasi penggunaan anggaran, dan cara melakukan verifikasi transfer.]',
      target_amount: 50000000,
      collected_amount: 32400000,
      deadline: '[ISI: Tanggal]',
      image: null,
    }),
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
      <title>{{ donationProgram.title }} - Al Manar</title>
      <meta name="description" content="Kanal donasi resmi Al Manar. Salurkan amal jariyah Anda dengan aman dan transparan." />
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
        <Link :href="route('donasi.index')" class="text-xs font-semibold text-brand-sage hover:text-brand-teal transition-all">&larr; Kembali ke Daftar Donasi</Link>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
        <!-- Visual Column -->
        <div class="lg:col-span-5 flex justify-center">
          <div class="relative w-full max-w-sm aspect-[3/4]">
            <div class="absolute inset-0 bg-transparent border-2 border-brand-gold translate-x-3 translate-y-3 rounded" style="clip-path: url(#ogee-arch);"></div>
            <div class="w-full h-full bg-brand-slate overflow-hidden shadow-xl" style="clip-path: url(#ogee-arch);">
              <img
                v-if="donationProgram.image"
                :src="donationProgram.image"
                :alt="donationProgram.title"
                class="w-full h-full object-cover"
                loading="lazy"
              />
              <div v-else class="w-full h-full flex items-center justify-center text-xs text-brand-moss select-none">[ISI: Foto Program]</div>
            </div>
          </div>
        </div>

        <!-- Detail & Payment Info Column -->
        <div class="lg:col-span-7 space-y-8">
          <div class="space-y-2">
            <span class="px-2.5 py-0.5 rounded text-[10px] font-utility font-bold uppercase tracking-wider bg-brand-sage/10 text-brand-sage border border-brand-sage/20 inline-block">Amal Jariyah</span>
            <h1 class="font-display text-3xl sm:text-4xl text-brand-teal uppercase tracking-wider font-bold">{{ donationProgram.title }}</h1>
          </div>

          <!-- Progress Info Card -->
          <div class="bg-brand-slate/40 p-6 rounded border border-brand-moss/10 space-y-4">
            <div class="flex justify-between text-xs font-utility uppercase tracking-wider font-semibold">
              <span class="text-brand-moss">Dana Terkumpul</span>
              <span class="text-brand-gold">{{ Math.round((donationProgram.collected_amount / (donationProgram.target_amount || 1)) * 100) }}%</span>
            </div>
            <!-- Progress Bar -->
            <div class="h-3 w-full bg-brand-stone rounded-full overflow-hidden border border-brand-moss/10">
              <div class="h-full bg-brand-gold rounded-full transition-all duration-1000" :style="`width: ${Math.min((donationProgram.collected_amount / (donationProgram.target_amount || 1)) * 100, 100)}%`"></div>
            </div>
            <div class="flex justify-between font-mono text-xs pt-1">
              <span class="font-semibold text-brand-teal">{{ formatCurrency(donationProgram.collected_amount) }}</span>
              <span class="text-brand-navy/60">Target: {{ formatCurrency(donationProgram.target_amount) }}</span>
            </div>
          </div>

          <!-- Description -->
          <div class="space-y-3">
            <h3 class="font-utility text-xs font-bold text-brand-moss uppercase tracking-wider">Deskripsi & Ajakan Donasi</h3>
            <p class="text-brand-navy/80 text-sm leading-relaxed whitespace-pre-wrap">{{ donationProgram.description }}</p>
          </div>

          <!-- Payment Methods Info -->
          <div class="bg-brand-teal text-brand-stone p-6 rounded-lg border border-brand-sage/20 space-y-4 shadow-md">
            <h3 class="font-display text-lg tracking-wider text-brand-gold uppercase">Informasi Transfer Rekening</h3>
            <p class="text-xs text-brand-stone/80 leading-relaxed">
              Silakan salurkan donasi Anda melalui transfer rekening bank resmi yayasan Al Manar di bawah ini. Harap tambahkan kode unik atau konfirmasi melalui WhatsApp kontak.
            </p>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs bg-brand-sage/20 p-4 rounded border border-brand-sage/30">
              <div>
                <span class="block text-brand-gold font-bold uppercase tracking-wider text-[10px] mb-1">Transfer Bank Syariah</span>
                <span class="block font-semibold text-sm">[ISI: Nama Bank Syariah]</span>
                <span class="block font-mono text-sm text-brand-stone font-bold mt-0.5">[ISI: Nomor Rekening]</span>
                <span class="block text-[10px] text-brand-stone/70 mt-0.5">a.n. Yayasan Al Manar</span>
              </div>
              <div class="flex flex-col items-center sm:items-end justify-center">
                <!-- QRIS Scan placeholder -->
                <div class="w-20 h-20 bg-brand-stone/10 border border-brand-stone/20 rounded flex items-center justify-center text-[10px] text-brand-stone/50 font-bold select-none">[ISI: QRIS]</div>
                <span class="text-[9px] text-brand-stone/60 mt-1 uppercase tracking-wider font-utility font-bold">Scan QRIS</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </SectionContainer>
  </PublicLayout>
</template>
