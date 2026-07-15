<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import BaseButton from '@/Components/BaseButton.vue';

const isMobileMenuOpen = ref(false);

const navItems = [
  { name: 'Home', href: route('home'), route: 'home' },
  { name: 'Profil', href: route('profil'), route: 'profil' },
  { name: 'Program', href: route('program.index'), route: 'program.*' },
  { name: 'Berita', href: route('berita.index'), route: 'berita.*' },
  { name: 'Galeri', href: route('galeri'), route: 'galeri' },
  { name: 'Testimoni', href: route('testimoni'), route: 'testimoni' },
  { name: 'Kontak', href: route('kontak'), route: 'kontak' },
];

const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value;
};
</script>

<template>
  <nav class="bg-brand-stone border-b border-brand-slate sticky top-0 z-50 transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-20">
        <!-- Logo / Brand Name -->
        <div class="flex items-center">
          <Link :href="route('home')" class="flex flex-col group focus:outline-none">
            <span class="font-display text-2xl tracking-wider text-brand-sage group-hover:text-brand-teal transition-colors duration-300">
              AL MANAR
            </span>
            <span class="font-utility text-[9px] uppercase tracking-[0.25em] text-brand-moss -mt-1">
              Masjid & Lembaga Dakwah
            </span>
          </Link>
        </div>

        <!-- Desktop Menu -->
        <div class="hidden md:flex items-center space-x-1">
          <Link
            v-for="item in navItems"
            :key="item.name"
            :href="item.href"
            class="px-4 py-2 text-sm font-medium transition-all duration-300 rounded focus:outline-none focus:ring-1 focus:ring-brand-gold"
            :class="[
              route().current(item.route)
                ? 'text-brand-sage font-semibold border-b-2 border-brand-gold rounded-b-none'
                : 'text-brand-navy/80 hover:text-brand-sage hover:bg-brand-slate/40'
            ]"
          >
            {{ item.name }}
          </Link>
          
          <div class="pl-4">
            <BaseButton :href="route('donasi.index')" variant="primary">
              Donasi
            </BaseButton>
          </div>
        </div>

        <!-- Mobile Menu Button -->
        <div class="flex items-center md:hidden">
          <button
            @click="toggleMobileMenu"
            type="button"
            class="inline-flex items-center justify-center p-2 rounded text-brand-navy hover:text-brand-sage hover:bg-brand-slate focus:outline-none focus:ring-2 focus:ring-inset focus:ring-brand-gold transition-colors duration-300"
            aria-controls="mobile-menu"
            :aria-expanded="isMobileMenuOpen.toString()"
          >
            <span class="sr-only">Open main menu</span>
            <!-- Icon Open -->
            <svg
              v-if="!isMobileMenuOpen"
              class="block h-6 w-6"
              fill="none"
              viewBox="0 0 24 24"
              stroke-width="1.5"
              stroke="currentColor"
              aria-hidden="true"
            >
              <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
            <!-- Icon Close -->
            <svg
              v-else
              class="block h-6 w-6"
              fill="none"
              viewBox="0 0 24 24"
              stroke-width="1.5"
              stroke="currentColor"
              aria-hidden="true"
            >
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile Menu Drawer -->
    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0 -translate-y-2"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 -translate-y-2"
    >
      <div v-show="isMobileMenuOpen" class="md:hidden bg-brand-stone border-t border-brand-slate px-4 pt-2 pb-6 space-y-2" id="mobile-menu">
        <Link
          v-for="item in navItems"
          :key="item.name"
          :href="item.href"
          @click="isMobileMenuOpen = false"
          class="block px-4 py-2.5 rounded text-base font-medium transition-all duration-300"
          :class="[
            route().current(item.route)
              ? 'bg-brand-slate text-brand-sage font-semibold border-l-4 border-brand-gold'
              : 'text-brand-navy/80 hover:bg-brand-slate/50 hover:text-brand-sage'
          ]"
        >
          {{ item.name }}
        </Link>
        <div class="pt-4 px-4">
          <BaseButton :href="route('donasi.index')" variant="primary" class="w-full">
            Donasi
          </BaseButton>
        </div>
      </div>
    </Transition>
  </nav>
</template>
