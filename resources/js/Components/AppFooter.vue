<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
  <footer class="bg-brand-teal text-brand-stone/90 border-t border-brand-sage/20 py-16 transition-colors duration-300 font-sans">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
        <!-- Brand Info -->
        <div class="md:col-span-2 space-y-4">
          <Link :href="route('home')" class="inline-block focus:outline-none">
            <span class="font-display text-3xl tracking-widest text-brand-stone hover:text-brand-gold transition-colors duration-300">
              AL MANAR
            </span>
            <div class="font-utility text-xs uppercase tracking-[0.2em] text-brand-gold -mt-1">
              Masjid & Lembaga Dakwah
            </div>
          </Link>
          <p class="text-sm text-brand-stone/75 leading-relaxed max-w-sm">
            Menjadi pusat dakwah yang moderat, transparan, dan inklusif untuk membimbing umat menuju ketenteraman dan kebaikan dunia-akhirat.
          </p>
        </div>

        <!-- Sitemap Quick Links -->
        <div class="space-y-4">
          <h4 class="font-utility text-sm font-bold tracking-wider text-brand-gold uppercase">Tautan Pintas</h4>
          <ul class="space-y-2 text-sm text-brand-stone/80">
            <li>
              <Link :href="route('profil')" class="hover:text-brand-gold transition-colors duration-200">Profil & Visi Misi</Link>
            </li>
            <li>
              <Link :href="route('program.index')" class="hover:text-brand-gold transition-colors duration-200">Program & Kegiatan</Link>
            </li>
            <li>
              <Link :href="route('berita.index')" class="hover:text-brand-gold transition-colors duration-200">Berita & Artikel</Link>
            </li>
            <li>
              <Link :href="route('donasi.index')" class="hover:text-brand-gold transition-colors duration-200">Donasi & Laporan</Link>
            </li>
          </ul>
        </div>

        <!-- Contact Information -->
        <div class="space-y-4">
          <h4 class="font-utility text-sm font-bold tracking-wider text-brand-gold uppercase">Kontak Kami</h4>
          <address class="not-italic space-y-3 text-sm text-brand-stone/80">
            <div class="flex items-start space-x-2">
              <!-- Location Icon -->
              <svg class="h-5 w-5 text-brand-gold shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              <span>Jl. Raya Al Manar No. 12, Kota Tangerang, Banten</span>
            </div>
            <div class="flex items-center space-x-2">
              <!-- Phone Icon -->
              <svg class="h-5 w-5 text-brand-gold shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
              </svg>
              <span>+62 812-3456-7890</span>
            </div>
            <div class="flex items-center space-x-2">
              <!-- Email Icon -->
              <svg class="h-5 w-5 text-brand-gold shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
              <span>info@almanar.or.id</span>
            </div>
          </address>
        </div>
      </div>

      <!-- Footer Bottom -->
      <div class="mt-16 pt-8 border-t border-brand-sage/20 flex flex-col md:flex-row justify-between items-center text-xs text-brand-stone/60 space-y-4 md:space-y-0">
        <div>
          &copy; {{ new Date().getFullYear() }} Al Manar. All rights reserved.
        </div>
        <div class="flex space-x-6">
          <a href="#" class="hover:text-brand-gold transition-colors duration-200">Syarat & Ketentuan</a>
          <a href="#" class="hover:text-brand-gold transition-colors duration-200">Kebijakan Privasi</a>
          <Link :href="route('login')" class="hover:text-brand-gold transition-colors duration-200">Panel Admin</Link>
        </div>
      </div>
    </div>
  </footer>
</template>
