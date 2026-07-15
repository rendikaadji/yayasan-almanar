<script setup>
import AppNavbar from '@/Components/AppNavbar.vue';
import AppFooter from '@/Components/AppFooter.vue';
</script>

<template>
  <div class="min-h-screen flex flex-col bg-brand-stone text-brand-navy selection:bg-brand-gold/30 selection:text-brand-teal">
    <!-- Navbar Navigation -->
    <AppNavbar />

    <!-- Main Content Area -->
    <main class="flex-1 w-full">
      <slot />
    </main>

    <!-- Footer Area -->
    <AppFooter />
  </div>
</template>
