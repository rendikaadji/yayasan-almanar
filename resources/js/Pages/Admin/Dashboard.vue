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

        <!-- Stat: Berita Diterbitkan — icon: news (same as sidebar) -->
        <div class="bg-white rounded-xl border border-brand-slate p-6 shadow-sm flex flex-col justify-between group hover:shadow-md transition-shadow duration-300">
          <div class="flex items-center justify-between">
            <span class="text-xs font-utility font-bold text-brand-moss uppercase tracking-wider">Berita Diterbitkan</span>
            <span class="p-2.5 rounded-lg bg-brand-sage/10 text-brand-sage group-hover:bg-brand-sage/20 transition-colors">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a1 1 0 00-.293-.707l-2-2A1 1 0 0018 6.5V7m0 13a2 2 0 01-2-2V7M9 8h4m-4 4h4m-4 4h2" />
              </svg>
            </span>
          </div>
          <div class="mt-4">
            <span class="text-4xl font-display font-semibold text-brand-teal">{{ stats.posts_count }}</span>
            <span class="block text-xs text-brand-navy/60 mt-1">Total artikel berita &amp; kajian</span>
          </div>
          <div class="mt-4 pt-4 border-t border-brand-slate">
            <Link :href="route('admin.posts.index')" class="text-xs font-semibold text-brand-sage hover:text-brand-teal transition-colors">
              Kelola Berita &rarr;
            </Link>
          </div>
        </div>

        <!-- Stat: Program Aktif — icon: program (same as sidebar) -->
        <div class="bg-white rounded-xl border border-brand-slate p-6 shadow-sm flex flex-col justify-between group hover:shadow-md transition-shadow duration-300">
          <div class="flex items-center justify-between">
            <span class="text-xs font-utility font-bold text-brand-moss uppercase tracking-wider">Program Aktif</span>
            <span class="p-2.5 rounded-lg bg-brand-teal/10 text-brand-teal group-hover:bg-brand-teal/20 transition-colors">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.168.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
              </svg>
            </span>
          </div>
          <div class="mt-4">
            <span class="text-4xl font-display font-semibold text-brand-teal">{{ stats.programs_count }}</span>
            <span class="block text-xs text-brand-navy/60 mt-1">Kajian, santunan &amp; pendidikan</span>
          </div>
          <div class="mt-4 pt-4 border-t border-brand-slate">
            <Link :href="route('admin.programs.index')" class="text-xs font-semibold text-brand-sage hover:text-brand-teal transition-colors">
              Kelola Program &rarr;
            </Link>
          </div>
        </div>

        <!-- Stat: Dana Donasi — icon: donation/heart (same as sidebar) -->
        <div class="bg-white rounded-xl border border-brand-slate p-6 shadow-sm flex flex-col justify-between group hover:shadow-md transition-shadow duration-300">
          <div class="flex items-center justify-between">
            <span class="text-xs font-utility font-bold text-brand-moss uppercase tracking-wider">Dana Donasi Terkumpul</span>
            <span class="p-2.5 rounded-lg bg-brand-gold/15 text-brand-gold group-hover:bg-brand-gold/25 transition-colors">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
              </svg>
            </span>
          </div>
          <div class="mt-4">
            <span class="text-2xl lg:text-3xl font-display font-semibold text-brand-teal">
              {{ formatCurrency(stats.total_donations) }}
            </span>
            <span class="block text-xs text-brand-navy/60 mt-1">Akumulasi donasi terkumpul</span>
          </div>
          <div class="mt-4 pt-4 border-t border-brand-slate">
            <Link :href="route('admin.donation-programs.index')" class="text-xs font-semibold text-brand-sage hover:text-brand-teal transition-colors">
              Program Donasi &rarr;
            </Link>
          </div>
        </div>

        <!-- Stat: Pesan Baru — icon: message (same as sidebar) -->
        <div class="bg-white rounded-xl border border-brand-slate p-6 shadow-sm flex flex-col justify-between group hover:shadow-md transition-shadow duration-300">
          <div class="flex items-center justify-between">
            <span class="text-xs font-utility font-bold text-brand-moss uppercase tracking-wider">Pesan Baru</span>
            <span class="p-2.5 rounded-lg bg-red-50 text-red-500 group-hover:bg-red-100 transition-colors relative">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
              <span v-if="stats.unread_messages_count > 0" class="absolute -top-1 -right-1 w-2.5 h-2.5 rounded-full bg-red-500 animate-pulse"></span>
            </span>
          </div>
          <div class="mt-4">
            <span class="text-4xl font-display font-semibold text-brand-teal">{{ stats.unread_messages_count }}</span>
            <span class="block text-xs text-brand-navy/60 mt-1">Pesan masuk belum dibaca</span>
          </div>
          <div class="mt-4 pt-4 border-t border-brand-slate">
            <Link :href="route('admin.contact-messages.index')" class="text-xs font-semibold text-brand-sage hover:text-brand-teal transition-colors">
              Buka Inbox &rarr;
            </Link>
          </div>
        </div>

        <!-- Stat: Testimoni — icon: testimonial/chat (same as sidebar) -->
        <div class="bg-white rounded-xl border border-brand-slate p-6 shadow-sm flex flex-col justify-between group hover:shadow-md transition-shadow duration-300">
          <div class="flex items-center justify-between">
            <span class="text-xs font-utility font-bold text-brand-moss uppercase tracking-wider">Testimoni Masuk</span>
            <span class="p-2.5 rounded-lg bg-brand-moss/10 text-brand-moss group-hover:bg-brand-moss/20 transition-colors">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
              </svg>
            </span>
          </div>
          <div class="mt-4">
            <span class="text-4xl font-display font-semibold text-brand-teal">{{ stats.testimonials_count }}</span>
            <span class="block text-xs text-brand-navy/60 mt-1">Ulasan jamaah/donatur</span>
          </div>
          <div class="mt-4 pt-4 border-t border-brand-slate">
            <Link :href="route('admin.testimonials.index')" class="text-xs font-semibold text-brand-sage hover:text-brand-teal transition-colors">
              Kelola Testimoni &rarr;
            </Link>
          </div>
        </div>

        <!-- Quick Access Section -->
        <div class="bg-brand-teal text-brand-stone rounded-xl p-6 shadow-md border border-brand-sage/20 flex flex-col justify-between">
          <div class="flex items-start space-x-3">
            <span class="p-2.5 rounded-lg bg-white/10 text-brand-gold shrink-0 mt-0.5">
              <!-- Globe / Website Icon -->
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
              </svg>
            </span>
            <div>
              <h4 class="font-display text-lg tracking-wider text-brand-gold uppercase">Situs Utama</h4>
              <p class="text-xs text-brand-stone/75 mt-1 leading-relaxed">
                Ingin mengecek langsung visual frontend situs profil setelah melakukan perubahan konten di CMS?
              </p>
            </div>
          </div>
          <a
            href="/"
            target="_blank"
            class="mt-6 w-full text-center px-4 py-2.5 text-xs font-semibold rounded-lg bg-brand-gold text-brand-teal hover:bg-brand-stone hover:text-brand-sage transition-all duration-300 flex items-center justify-center gap-2"
          >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
            </svg>
            Kunjungi Halaman Publik
          </a>
        </div>

      </div>
    </div>
  </AdminLayout>
</template>
