<script setup>
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import SectionContainer from '@/Components/SectionContainer.vue';
import Card from '@/Components/Card.vue';

const props = defineProps({
  testimonials: {
    type: Array,
    default: () => [
      { id: 1, name: '[ISI: Nama Tokoh/Jamaah 1]', message: '[ISI: Kutipan testimoni mengenai jalannya program, kepuasan terhadap bimbingan ibadah, dan kemudahan akses informasi.]', photo: null, is_featured: true },
      { id: 2, name: '[ISI: Nama Tokoh/Jamaah 2]', message: '[ISI: Kutipan testimoni mengenai transparansi donasi Al Manar yang tersalurkan tepat sasaran kepada dhuafa.]', photo: null, is_featured: false },
    ],
  },
});
</script>

<template>
  <PublicLayout>
    <Head>
      <title>Ulasan & Testimoni - Al Manar</title>
      <meta name="description" content="Baca ulasan langsung dari para donatur tetap, jamaah kajian, dan relawan kemanusiaan mengenai program kebermanfaatan Al Manar." />
      <meta name="keywords" content="Testimoni Al Manar, Ulasan Donatur, Suara Jamaah, Penilaian Umat" />
    </Head>

    <!-- Header Banner -->
    <SectionContainer bg="slate" :compact="true" class="border-b border-brand-moss/10">
      <div class="text-center py-6">
        <span class="font-utility text-xs uppercase tracking-[0.25em] text-brand-gold font-bold">Suara Umat</span>
        <h1 class="font-display text-3xl sm:text-4xl lg:text-5xl text-brand-teal uppercase tracking-wider mt-2">Testimoni</h1>
      </div>
    </SectionContainer>

    <!-- Testimonials Listing -->
    <SectionContainer bg="stone">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
        <Card v-for="testi in testimonials" :key="testi.id" :hoverable="true" bg="white" class="relative overflow-hidden">
          <span class="absolute -top-4 -left-2 text-8xl text-brand-gold/10 font-display select-none pointer-events-none">&ldquo;</span>
          <div class="relative space-y-4 pt-4">
            <blockquote class="text-brand-navy/85 italic text-sm leading-relaxed">
              {{ testi.message }}
            </blockquote>
            <div class="flex items-center space-x-3 pt-2">
              <div class="w-10 h-10 rounded-full bg-brand-slate shrink-0 flex items-center justify-center text-[10px] text-brand-moss select-none font-bold">
                <img
                  v-if="testi.photo"
                  :src="testi.photo"
                  alt="Profile"
                  class="w-full h-full object-cover rounded-full"
                  loading="lazy"
                />
                <span v-else>Foto</span>
              </div>
              <div class="flex flex-col">
                <span class="font-display font-bold text-brand-teal">{{ testi.name }}</span>
                <span class="text-[10px] font-utility text-brand-moss uppercase tracking-wider font-semibold">Jamaah Binaan</span>
              </div>
            </div>
          </div>
        </Card>
      </div>

      <div v-if="testimonials.length === 0" class="text-center py-12 text-brand-navy/40 italic">
        Belum ada testimoni.
      </div>
    </SectionContainer>
  </PublicLayout>
</template>
