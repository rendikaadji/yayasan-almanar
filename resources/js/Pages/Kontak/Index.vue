<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import SectionContainer from '@/Components/SectionContainer.vue';
import BaseButton from '@/Components/BaseButton.vue';

const form = useForm({
  name: '',
  email: '',
  phone: '',
  message: '',
});

const submitForm = () => {
  form.post(route('kontak.store'), {
    onSuccess: () => {
      form.reset();
      alert('Pesan Anda berhasil dikirim. Terima kasih atas dukungannya.');
    },
  });
};
</script>

<template>
  <PublicLayout>
    <Head>
      <title>Hubungi Kami - Al Manar</title>
      <meta name="description" content="Kirimkan saran, kritik, atau pertanyaan Anda lewat form kontak kami, atau temukan alamat peta masjid Al Manar di sini." />
      <meta name="keywords" content="Kontak Al Manar, Lokasi Al Manar, Peta Masjid, Nomor Telepon DKM" />
    </Head>

    <!-- Header Banner -->
    <SectionContainer bg="slate" :compact="true" class="border-b border-brand-moss/10">
      <div class="text-center py-6">
        <span class="font-utility text-xs uppercase tracking-[0.25em] text-brand-gold font-bold">Hubungi Kami</span>
        <h1 class="font-display text-3xl sm:text-4xl lg:text-5xl text-brand-teal uppercase tracking-wider mt-2">Kontak Al Manar</h1>
      </div>
    </SectionContainer>

    <!-- Contact & Map Section -->
    <SectionContainer bg="stone">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
        <!-- Contact Information & Map -->
        <div class="lg:col-span-6 space-y-8 text-left">
          <div class="space-y-4">
            <h2 class="font-display text-2xl text-brand-teal uppercase tracking-wider">Informasi Kontak</h2>
            <p class="text-brand-navy/70 text-sm leading-relaxed">
              Anda dapat menghubungi kami secara langsung melalui kontak di bawah ini atau berkunjung langsung ke alamat kami.
            </p>
          </div>

          <address class="not-italic space-y-4 text-sm text-brand-navy/80">
            <div class="flex items-start space-x-3">
              <svg class="h-5 w-5 text-brand-gold mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              <div>
                <span class="block font-semibold text-brand-teal">Alamat Lengkap</span>
                <span>Jl. Raya Al Manar No. 12, Kota Tangerang, Banten</span>
              </div>
            </div>

            <div class="flex items-start space-x-3">
              <svg class="h-5 w-5 text-brand-gold mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
              </svg>
              <div>
                <span class="block font-semibold text-brand-teal">Nomor WhatsApp & Telepon</span>
                <span>+62 812-3456-7890</span>
              </div>
            </div>

            <div class="flex items-start space-x-3">
              <svg class="h-5 w-5 text-brand-gold mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
              <div>
                <span class="block font-semibold text-brand-teal">E-mail Resmi</span>
                <span>info@almanar.or.id</span>
              </div>
            </div>
          </address>

          <!-- Maps Embed -->
          <div class="w-full h-64 rounded-lg overflow-hidden border border-brand-slate shadow-sm bg-brand-slate">
            <!-- Leaflet or static Maps embed simulation -->
            <iframe 
              src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15866.275727926116!2d106.63462985!3d-6.1883726!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f8c6e265c71b%3A0xc3457a17726cbffb!2sTangerang%20City!5e0!3m2!1sid!2sid!4v1689234000000!5m2!1sid!2sid" 
              class="w-full h-full border-0" 
              allowfullscreen="" 
              loading="lazy" 
              referrerpolicy="no-referrer-when-downgrade"
              title="Google Maps Al Manar"
            ></iframe>
          </div>
        </div>

        <!-- Contact Message Form -->
        <div class="lg:col-span-6 bg-white p-8 rounded-xl border border-brand-slate shadow-sm space-y-6 text-left">
          <div class="space-y-1">
            <h3 class="font-display text-2xl text-brand-teal uppercase tracking-wider">Kirim Pesan</h3>
            <p class="text-xs text-brand-navy/60">Gunakan form ini untuk mengirim pertanyaan atau saran kepada pihak DKM.</p>
          </div>

          <form @submit.prevent="submitForm" class="space-y-4">
            <!-- Name -->
            <div class="space-y-1">
              <label class="block text-xs font-utility font-bold text-brand-moss uppercase tracking-wider">Nama Lengkap</label>
              <input
                v-model="form.name"
                type="text"
                required
                class="w-full rounded border border-brand-slate px-3 py-2 text-sm focus:outline-none focus:border-brand-sage focus:ring-1 focus:ring-brand-sage"
              />
              <span v-if="form.errors.name" class="text-xs text-red-600 block">{{ form.errors.name }}</span>
            </div>

            <!-- Email -->
            <div class="space-y-1">
              <label class="block text-xs font-utility font-bold text-brand-moss uppercase tracking-wider">Alamat Email</label>
              <input
                v-model="form.email"
                type="email"
                required
                class="w-full rounded border border-brand-slate px-3 py-2 text-sm focus:outline-none focus:border-brand-sage focus:ring-1 focus:ring-brand-sage"
              />
              <span v-if="form.errors.email" class="text-xs text-red-600 block">{{ form.errors.email }}</span>
            </div>

            <!-- Phone -->
            <div class="space-y-1">
              <label class="block text-xs font-utility font-bold text-brand-moss uppercase tracking-wider">No. WhatsApp/Telepon</label>
              <input
                v-model="form.phone"
                type="text"
                placeholder="Contoh: 081234567890"
                class="w-full rounded border border-brand-slate px-3 py-2 text-sm focus:outline-none focus:border-brand-sage focus:ring-1 focus:ring-brand-sage"
              />
              <span v-if="form.errors.phone" class="text-xs text-red-600 block">{{ form.errors.phone }}</span>
            </div>

            <!-- Message -->
            <div class="space-y-1">
              <label class="block text-xs font-utility font-bold text-brand-moss uppercase tracking-wider">Isi Pesan Anda</label>
              <textarea
                v-model="form.message"
                rows="4"
                required
                class="w-full rounded border border-brand-slate px-3 py-2 text-sm focus:outline-none focus:border-brand-sage focus:ring-1 focus:ring-brand-sage"
              ></textarea>
              <span v-if="form.errors.message" class="text-xs text-red-600 block">{{ form.errors.message }}</span>
            </div>

            <div class="pt-2">
              <BaseButton type="submit" variant="primary" class="w-full" :disabled="form.processing">
                {{ form.processing ? 'Mengirim...' : 'Kirim Pesan' }}
              </BaseButton>
            </div>
          </form>
        </div>
      </div>
    </SectionContainer>
  </PublicLayout>
</template>
