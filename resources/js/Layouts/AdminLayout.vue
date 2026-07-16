<script setup>
import { ref, computed } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

const props = defineProps({
  title: {
    type: String,
    default: 'Dashboard',
  },
});

const isSidebarOpen = ref(false);

const page = usePage();
const user = computed(() => page.props.auth.user);

const dynamicNavItems = computed(() => {
  const items = [];

  if (!user.value) return items;

  if (user.value.role === 'admin') {
    // CMS Items
    items.push(
      { name: 'Dashboard', href: route('admin.dashboard'), route: 'admin.dashboard', icon: 'dashboard' },
      { name: 'Berita & Artikel', href: route('admin.posts.index'), route: 'admin.posts.*', icon: 'news' },
      { name: 'Program & Kegiatan', href: route('admin.programs.index'), route: 'admin.programs.*', icon: 'program' },
      { name: 'Galeri Foto/Video', href: route('admin.galleries.index'), route: 'admin.galleries.*', icon: 'gallery' },
      { name: 'Testimoni', href: route('admin.testimonials.index'), route: 'admin.testimonials.*', icon: 'testimonial' },
      { name: 'Program Donasi', href: route('admin.donation-programs.index'), route: 'admin.donation-programs.*', icon: 'donation' },
      { name: 'Laporan Donasi', href: route('admin.donation-reports.index'), route: 'admin.donation-reports.*', icon: 'report' },
      { name: 'Struktur Organisasi', href: route('admin.organization-structures.index'), route: 'admin.organization-structures.*', icon: 'structure' },
      { name: 'Pesan Masuk', href: route('admin.contact-messages.index'), route: 'admin.contact-messages.*', icon: 'message' }
    );

    // Keuangan
    items.push({ header: 'Sistem Keuangan' });
    items.push(
      { name: 'Kelola Sub-Kategori', href: route('admin.finance.sub-kategori.index'), route: 'admin.finance.sub-kategori.*', icon: 'settings' },
      { name: 'Laporan Bidang', href: route('admin.finance.periods.index'), route: 'admin.finance.periods.*', icon: 'periods' },
      { name: 'Rekap Konsolidasi', href: route('admin.finance.rekap.index'), route: 'admin.finance.rekap.*', icon: 'rekap' }
    );
  } else if (user.value.role === 'bendahara_umum') {
    items.push(
      { name: 'Verifikasi Laporan Bidang', href: route('admin.finance.rekap.index'), route: 'admin.finance.rekap.*', icon: 'rekap' },
      { name: 'Laporan Periode Bidang', href: route('admin.finance.periods.index'), route: 'admin.finance.periods.*', icon: 'periods' }
    );
  } else if (user.value.role === 'bendahara_bidang') {
    items.push(
      { name: 'Laporan Keuangan Bidang', href: route('admin.finance.periods.index'), route: 'admin.finance.periods.*', icon: 'periods' }
    );
  }

  return items;
});

const logoutForm = useForm({});
const handleLogout = () => {
  logoutForm.post(route('logout'));
};
</script>

<template>
  <div class="min-h-screen flex bg-brand-stone text-brand-navy font-sans antialiased">
    <!-- Desktop Sidebar -->
    <aside class="hidden lg:flex lg:flex-shrink-0 w-64 bg-brand-teal text-brand-stone flex-col border-r border-brand-sage/20 shadow-xl">
      <div class="h-20 flex items-center px-6 border-b border-brand-sage/20">
        <Link :href="route('home')" class="flex flex-col focus:outline-none">
          <span class="font-display text-xl tracking-widest text-brand-stone">AL MANAR</span>
          <span class="font-utility text-[9px] uppercase tracking-[0.25em] text-brand-gold -mt-1">CMS Control Panel</span>
        </Link>
      </div>

      <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
        <template v-for="item in dynamicNavItems" :key="item.name || item.header">
          <div v-if="item.header" class="px-4 py-2 text-[10px] font-bold uppercase tracking-wider text-brand-stone/40 mt-4">
            {{ item.header }}
          </div>
          <Link
            v-else
            :href="item.href"
            class="flex items-center px-4 py-3 text-sm font-medium rounded transition-all duration-300 focus:outline-none focus:ring-1 focus:ring-brand-gold group"
            :class="[
              route().current(item.route)
                ? 'bg-brand-sage text-brand-stone font-bold shadow-md shadow-brand-teal/30 border-l-4 border-brand-gold'
                : 'text-brand-stone/75 hover:bg-brand-sage/35 hover:text-brand-stone'
            ]"
          >
            <!-- Premium Outline SVG Icon -->
            <svg 
              class="w-5 h-5 mr-3 shrink-0 transition-colors duration-300" 
              :class="[route().current(item.route) ? 'text-brand-gold' : 'text-brand-stone/60 group-hover:text-brand-stone']" 
              fill="none" 
              viewBox="0 0 24 24" 
              stroke="currentColor" 
              stroke-width="1.5"
            >
              <path v-if="item.icon === 'dashboard'" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2z" />
              <path v-else-if="item.icon === 'news'" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a1 1 0 00-.293-.707l-2-2A1 1 0 0018 6.5V7m0 13a2 2 0 01-2-2V7M9 8h4m-4 4h4m-4 4h2" />
              <path v-else-if="item.icon === 'program'" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.168.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
              <path v-else-if="item.icon === 'gallery'" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
              <path v-else-if="item.icon === 'testimonial'" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
              <path v-else-if="item.icon === 'donation'" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
              <path v-else-if="item.icon === 'report'" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              <path v-else-if="item.icon === 'structure'" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              <path v-else-if="item.icon === 'message'" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              <g v-else-if="item.icon === 'settings'">
                <path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              </g>
              <path v-else-if="item.icon === 'periods'" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              <path v-else-if="item.icon === 'rekap'" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            {{ item.name }}
          </Link>
        </template>
      </nav>

      <div class="p-4 border-t border-brand-sage/20 bg-brand-teal/90 flex flex-col space-y-3">
        <div class="flex items-center space-x-3">
          <div class="w-9 h-9 rounded-full bg-brand-sage flex items-center justify-center font-bold text-sm text-brand-gold uppercase border border-brand-gold/30">
            {{ $page.props.auth.user.name.charAt(0) }}
          </div>
          <div class="flex flex-col min-w-0">
            <span class="text-xs font-semibold text-brand-stone truncate">{{ $page.props.auth.user.name }}</span>
            <span class="text-[10px] text-brand-stone/60 truncate">{{ $page.props.auth.user.email }}</span>
          </div>
        </div>
        <button
          @click="handleLogout"
          class="w-full text-left px-4 py-2 text-xs font-semibold rounded bg-brand-sage/30 hover:bg-red-950/20 text-brand-stone/85 hover:text-red-200 border border-brand-sage/40 hover:border-red-900/30 transition-all duration-300 cursor-pointer"
        >
          Keluar Sistem
        </button>
      </div>
    </aside>

    <!-- Mobile Sidebar Drawer Overlay -->
    <div v-show="isSidebarOpen" @click="isSidebarOpen = false" class="fixed inset-0 z-40 bg-brand-teal/60 backdrop-blur-sm lg:hidden transition-opacity duration-300"></div>

    <!-- Mobile Sidebar Drawer -->
    <aside
      class="fixed inset-y-0 left-0 z-50 w-64 bg-brand-teal text-brand-stone flex flex-col transform lg:hidden transition-transform duration-300 shadow-2xl"
      :class="[isSidebarOpen ? 'translate-x-0' : '-translate-x-full']"
    >
      <div class="h-20 flex items-center justify-between px-6 border-b border-brand-sage/20">
        <div class="flex flex-col">
          <span class="font-display text-xl tracking-widest text-brand-stone">AL MANAR</span>
          <span class="font-utility text-[9px] uppercase tracking-[0.25em] text-brand-gold -mt-1">CMS Control Panel</span>
        </div>
        <button @click="isSidebarOpen = false" class="lg:hidden p-1 rounded hover:bg-brand-sage/30 text-brand-stone focus:outline-none">
          <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
        <template v-for="item in dynamicNavItems" :key="item.name || item.header">
          <div v-if="item.header" class="px-4 py-2 text-[10px] font-bold uppercase tracking-wider text-brand-stone/40 mt-4">
            {{ item.header }}
          </div>
          <Link
            v-else
            :href="item.href"
            @click="isSidebarOpen = false"
            class="flex items-center px-4 py-3 text-sm font-medium rounded transition-all duration-300 group"
            :class="[
              route().current(item.route)
                ? 'bg-brand-sage text-brand-stone font-bold border-l-4 border-brand-gold'
                : 'text-brand-stone/75 hover:bg-brand-sage/35 hover:text-brand-stone'
            ]"
          >
            <svg 
              class="w-5 h-5 mr-3 shrink-0 transition-colors duration-300" 
              :class="[route().current(item.route) ? 'text-brand-gold' : 'text-brand-stone/60 group-hover:text-brand-stone']" 
              fill="none" 
              viewBox="0 0 24 24" 
              stroke="currentColor" 
              stroke-width="1.5"
            >
              <path v-if="item.icon === 'dashboard'" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2z" />
              <path v-else-if="item.icon === 'news'" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a1 1 0 00-.293-.707l-2-2A1 1 0 0018 6.5V7m0 13a2 2 0 01-2-2V7M9 8h4m-4 4h4m-4 4h2" />
              <path v-else-if="item.icon === 'program'" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.168.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
              <path v-else-if="item.icon === 'gallery'" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
              <path v-else-if="item.icon === 'testimonial'" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
              <path v-else-if="item.icon === 'donation'" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
              <path v-else-if="item.icon === 'report'" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              <path v-else-if="item.icon === 'structure'" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              <path v-else-if="item.icon === 'message'" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              <g v-else-if="item.icon === 'settings'">
                <path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              </g>
              <path v-else-if="item.icon === 'periods'" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2V7m16 0v12a2 2 0 01-2 2H5a2 2 0 01-2 2V7" />
              <path v-else-if="item.icon === 'rekap'" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            {{ item.name }}
          </Link>
        </template>
      </nav>

      <div class="p-4 border-t border-brand-sage/20 bg-brand-teal/90 flex flex-col space-y-3">
        <div class="flex items-center space-x-3">
          <div class="w-9 h-9 rounded-full bg-brand-sage flex items-center justify-center font-bold text-sm text-brand-gold uppercase border border-brand-gold/30">
            {{ $page.props.auth.user.name.charAt(0) }}
          </div>
          <div class="flex flex-col min-w-0">
            <span class="text-xs font-semibold text-brand-stone truncate">{{ $page.props.auth.user.name }}</span>
            <span class="text-[10px] text-brand-stone/60 truncate">{{ $page.props.auth.user.email }}</span>
          </div>
        </div>
        <button
          @click="handleLogout"
          class="w-full text-left px-4 py-2 text-xs font-semibold rounded bg-brand-sage/30 text-brand-stone border border-brand-sage/40 transition-all duration-300"
        >
          Keluar Sistem
        </button>
      </div>
    </aside>

    <!-- Main Panel Page Area -->
    <div class="flex-1 flex flex-col min-w-0">
      <!-- Navbar / Top Header -->
      <header class="h-20 bg-white border-b border-brand-slate flex items-center justify-between px-6 sticky top-0 z-30 shadow-sm transition-colors duration-300">
        <div class="flex items-center space-x-4">
          <!-- Toggle sidebar button (Mobile only) -->
          <button
            @click="isSidebarOpen = true"
            type="button"
            class="lg:hidden p-2 rounded text-brand-navy hover:text-brand-sage hover:bg-brand-slate focus:outline-none transition-colors duration-300"
          >
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
          
          <h2 class="text-xl font-display text-brand-teal uppercase tracking-wider font-bold">
            {{ title }}
          </h2>
        </div>

        <div class="flex items-center space-x-4">
          <Link :href="route('home')" class="text-xs font-semibold text-brand-sage hover:text-brand-teal transition-colors flex items-center">
            Lihat Situs Publik &rarr;
          </Link>
        </div>
      </header>

      <!-- Flash Notification Banners -->
      <div v-if="$page.props.flash && $page.props.flash.success" class="bg-emerald-50 border-b border-emerald-200 text-emerald-800 px-6 py-3 text-sm flex justify-between items-center">
        <span>{{ $page.props.flash.success }}</span>
      </div>

      <!-- Main Contents Slot -->
      <main class="flex-1 p-6 md:p-8 overflow-y-auto">
        <slot />
      </main>
    </div>
  </div>
</template>
