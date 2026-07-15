<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
  stats: {
    type: Object,
    required: true,
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
  <AdminLayout title="Overview Dashboard">
    <Head title="Admin Dashboard" />

    <div class="space-y-8 font-sans">
      <!-- Welcome Header -->
      <div class="bg-white rounded-xl border border-brand-slate p-6 shadow-sm">
        <h3 class="text-2xl font-display text-brand-teal uppercase tracking-wide">
          Assalamu'alaikum, Admin Al Manar
        </h3>
        <p class="text-sm text-brand-navy/70 mt-1">
          Gunakan panel kendali ini untuk memperbarui informasi publik, jadwal kegiatan, laporan donasi, dan membalas pesan jamaah.
        </p>
      </div>

      <!-- Stats Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Stat Item 1 -->
        <div class="bg-white rounded-xl border border-brand-slate p-6 shadow-sm flex flex-col justify-between">
          <div class="flex items-center justify-between">
            <span class="text-xs font-utility font-bold text-brand-moss uppercase tracking-wider">Berita Diterbitkan</span>
            <span class="p-2 rounded bg-brand-sage/10 text-brand-sage">
              <!-- Visual Placeholder Icon -->
              <span class="w-2.5 h-2.5 rounded-full bg-brand-sage block"></span>
            </span>
          </div>
          <div class="mt-4">
            <span class="text-4xl font-display font-semibold text-brand-teal">{{ stats.posts_count }}</span>
            <span class="block text-xs text-brand-navy/60 mt-1">Total artikel berita & kajian</span>
          </div>
          <div class="mt-4 pt-4 border-t border-brand-slate">
            <Link :href="route('admin.posts.index')" class="text-xs font-semibold text-brand-sage hover:text-brand-teal">
              Kelola Berita &rarr;
            </Link>
          </div>
        </div>

        <!-- Stat Item 2 -->
        <div class="bg-white rounded-xl border border-brand-slate p-6 shadow-sm flex flex-col justify-between">
          <div class="flex items-center justify-between">
            <span class="text-xs font-utility font-bold text-brand-moss uppercase tracking-wider">Program Aktif</span>
            <span class="p-2 rounded bg-brand-sage/10 text-brand-sage">
              <span class="w-2.5 h-2.5 rounded-full bg-brand-sage block"></span>
            </span>
          </div>
          <div class="mt-4">
            <span class="text-4xl font-display font-semibold text-brand-teal">{{ stats.programs_count }}</span>
            <span class="block text-xs text-brand-navy/60 mt-1">Kajian, santunan & pendidikan</span>
          </div>
          <div class="mt-4 pt-4 border-t border-brand-slate">
            <Link :href="route('admin.programs.index')" class="text-xs font-semibold text-brand-sage hover:text-brand-teal">
              Kelola Program &rarr;
            </Link>
          </div>
        </div>

        <!-- Stat Item 3 -->
        <div class="bg-white rounded-xl border border-brand-slate p-6 shadow-sm flex flex-col justify-between">
          <div class="flex items-center justify-between">
            <span class="text-xs font-utility font-bold text-brand-moss uppercase tracking-wider">Dana Donasi Terkumpul</span>
            <span class="p-2 rounded bg-brand-gold/15 text-brand-gold">
              <span class="w-2.5 h-2.5 rounded-full bg-brand-gold block"></span>
            </span>
          </div>
          <div class="mt-4">
            <span class="text-2xl lg:text-3xl font-display font-semibold text-brand-teal">
              {{ formatCurrency(stats.total_donations) }}
            </span>
            <span class="block text-xs text-brand-navy/60 mt-1">Akumulasi donasi terkumpul</span>
          </div>
          <div class="mt-4 pt-4 border-t border-brand-slate">
            <Link :href="route('admin.donation-programs.index')" class="text-xs font-semibold text-brand-sage hover:text-brand-teal">
              Program Donasi &rarr;
            </Link>
          </div>
        </div>

        <!-- Stat Item 4 -->
        <div class="bg-white rounded-xl border border-brand-slate p-6 shadow-sm flex flex-col justify-between">
          <div class="flex items-center justify-between">
            <span class="text-xs font-utility font-bold text-brand-moss uppercase tracking-wider">Pesan Baru</span>
            <span class="p-2 rounded bg-red-100 text-red-700">
              <span class="w-2.5 h-2.5 rounded-full bg-red-600 block animate-pulse"></span>
            </span>
          </div>
          <div class="mt-4">
            <span class="text-4xl font-display font-semibold text-brand-teal">{{ stats.unread_messages_count }}</span>
            <span class="block text-xs text-brand-navy/60 mt-1">Pesan masuk belum dibaca</span>
          </div>
          <div class="mt-4 pt-4 border-t border-brand-slate">
            <Link :href="route('admin.contact-messages.index')" class="text-xs font-semibold text-brand-sage hover:text-brand-teal">
              Buka Inbox &rarr;
            </Link>
          </div>
        </div>

        <!-- Stat Item 5 -->
        <div class="bg-white rounded-xl border border-brand-slate p-6 shadow-sm flex flex-col justify-between">
          <div class="flex items-center justify-between">
            <span class="text-xs font-utility font-bold text-brand-moss uppercase tracking-wider">Testimoni Masuk</span>
            <span class="p-2 rounded bg-brand-sage/10 text-brand-sage">
              <span class="w-2.5 h-2.5 rounded-full bg-brand-sage block"></span>
            </span>
          </div>
          <div class="mt-4">
            <span class="text-4xl font-display font-semibold text-brand-teal">{{ stats.testimonials_count }}</span>
            <span class="block text-xs text-brand-navy/60 mt-1">Ulasan jamaah/donatur</span>
          </div>
          <div class="mt-4 pt-4 border-t border-brand-slate">
            <Link :href="route('admin.testimonials.index')" class="text-xs font-semibold text-brand-sage hover:text-brand-teal">
              Kelola Testimoni &rarr;
            </Link>
          </div>
        </div>

        <!-- Quick Access Section -->
        <div class="bg-brand-teal text-brand-stone rounded-xl p-6 shadow-md border border-brand-sage/20 flex flex-col justify-between">
          <div>
            <h4 class="font-display text-lg tracking-wider text-brand-gold uppercase">Situs Utama</h4>
            <p class="text-xs text-brand-stone/75 mt-1 leading-relaxed">
              Ingin mengecek langsung visual frontend situs profil setelah melakukan perubahan konten di CMS?
            </p>
          </div>
          <a
            href="/"
            target="_blank"
            class="mt-6 w-full text-center px-4 py-2 text-xs font-semibold rounded bg-brand-gold text-brand-teal hover:bg-brand-stone hover:text-brand-sage transition-all duration-300"
          >
            Kunjungi Halaman Publik
          </a>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
