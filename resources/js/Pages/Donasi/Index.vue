<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import SectionContainer from '@/Components/SectionContainer.vue';
import Card from '@/Components/Card.vue';
import Divider from '@/Components/Divider.vue';

const props = defineProps({
  donationPrograms: {
    type: Array,
    default: () => [],
  },
  donationReports: {
    type: Array,
    default: () => [],
  },
});

const defaultDonationPrograms = [
  { id: 1, title: '[ISI: Program Renovasi Kubah Masjid]', slug: 'donasi-1', description: '[ISI: Deskripsi singkat ajakan berdonasi untuk mendukung perbaikan kubah masjid.]', target_amount: 50000000, collected_amount: 32400000, deadline: '[ISI: Tanggal]', image: null },
  { id: 2, title: '[ISI: Program Beasiswa Tahfidz Al Manar]', slug: 'donasi-2', description: '[ISI: Beasiswa bulanan operasional untuk santri yatim penghafal Quran.]', target_amount: 20000000, collected_amount: 8750000, deadline: '[ISI: Tanggal]', image: null },
];

const defaultReports = [
  { id: 1, date: '[ISI: Tanggal]', description: '[ISI: Pembelian pasir dan semen untuk kubah]', amount: 4500000, donation_program: { title: '[ISI: Renovasi Kubah]' } },
  { id: 2, date: '[ISI: Tanggal]', description: '[ISI: Penyaluran beasiswa santri Dhuafa]', amount: 1500000, donation_program: { title: '[ISI: Beasiswa Tahfidz]' } },
];

const displayDonations = computed(() => {
  return props.donationPrograms && props.donationPrograms.length > 0 ? props.donationPrograms : defaultDonationPrograms;
});

const displayReports = computed(() => {
  return props.donationReports && props.donationReports.length > 0 ? props.donationReports : defaultReports;
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
      <title>Kanal Donasi & Laporan Transparansi - Al Manar</title>
      <meta name="description" content="Salurkan sedekah terbaik Anda lewat program donasi pembangunan masjid, bantuan sosial kemanusiaan, serta tinjau laporan keuangan secara transparan." />
      <meta name="keywords" content="Donasi Online, Sedekah Masjid, Laporan Keuangan Masjid, Beasiswa Tahfidz, Transparansi Dana" />
    </Head>

    <!-- Header Banner -->
    <SectionContainer bg="slate" :compact="true" class="border-b border-brand-moss/10">
      <div class="text-center py-6">
        <span class="font-utility text-xs uppercase tracking-[0.25em] text-brand-gold font-bold">Amal Jariyah</span>
        <h1 class="font-display text-3xl sm:text-4xl lg:text-5xl text-brand-teal uppercase tracking-wider mt-2">Kanal Donasi</h1>
      </div>
    </SectionContainer>

    <!-- Active Donation Campaigns -->
    <SectionContainer bg="stone">
      <div class="text-center max-w-2xl mx-auto mb-12 space-y-2">
        <h2 class="font-display text-2xl sm:text-3xl text-brand-teal uppercase tracking-wider">Program Donasi Aktif</h2>
        <p class="text-brand-navy/70 text-sm">
          Salurkan kepedulian Anda untuk mewujudkan berbagai program pembangunan fisik dan pemberdayaan umat.
        </p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-5xl mx-auto">
        <Card v-for="prog in displayDonations" :key="prog.id" :hoverable="true" bg="white">
          <template #image>
            <div class="aspect-video bg-brand-slate flex items-center justify-center text-xs text-brand-moss select-none overflow-hidden relative">
              <img
                v-if="prog.image"
                :src="prog.image"
                :alt="prog.title"
                class="w-full h-full object-cover"
                loading="lazy"
              />
              <span v-else>[ISI: Foto {{ prog.title }}]</span>
            </div>
          </template>
          <template #title>
            <Link :href="route('donasi.show', prog.slug)">
              <h3 class="font-display text-xl text-brand-teal font-semibold hover:text-brand-sage transition-colors duration-300">
                {{ prog.title }}
              </h3>
            </Link>
          </template>
          <p class="text-sm line-clamp-3 mb-6 text-brand-navy/80">{{ prog.description }}</p>
          
          <!-- Progress Info -->
          <div class="space-y-2 bg-brand-stone/40 p-4 rounded border border-brand-slate text-xs font-utility text-left">
            <div class="flex justify-between font-bold">
              <span class="text-brand-moss">TERKUMPUL</span>
              <span class="text-brand-gold">{{ Math.round((prog.collected_amount / (prog.target_amount || 1)) * 100) }}%</span>
            </div>
            <div class="h-2 w-full bg-brand-slate rounded-full overflow-hidden">
              <div class="h-full bg-brand-gold rounded-full" :style="`width: ${Math.min((prog.collected_amount / (prog.target_amount || 1)) * 100, 100)}%`"></div>
            </div>
            <div class="flex justify-between font-mono text-[10px] text-brand-navy/70 pt-1">
              <span>{{ formatCurrency(prog.collected_amount) }}</span>
              <span>Target: {{ formatCurrency(prog.target_amount) }}</span>
            </div>
          </div>

          <template #footer>
            <span class="text-xs text-brand-navy/50 font-utility">{{ prog.deadline ? prog.deadline.substring(0, 10) : '' }}</span>
            <Link :href="route('donasi.show', prog.slug)" class="text-xs font-bold text-brand-sage hover:text-brand-teal">Donasi Sekarang &rarr;</Link>
          </template>
        </Card>
      </div>
    </SectionContainer>

    <Divider />

    <!-- Financial Reports / Transparency Logs -->
    <SectionContainer bg="stone">
      <div class="max-w-4xl mx-auto space-y-8">
        <div class="text-center space-y-2">
          <h2 class="font-display text-2xl sm:text-3xl text-brand-teal uppercase tracking-wider">Laporan Penggunaan Dana</h2>
          <p class="text-brand-navy/70 text-sm max-w-xl mx-auto">
            Bentuk keterbukaan pelaporan dana umat. Seluruh catatan pengeluaran operasional dan program donasi dicatat secara terperinci.
          </p>
        </div>

        <div class="bg-white rounded-lg border border-brand-slate shadow-sm overflow-hidden">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-brand-slate text-left text-sm">
              <thead class="bg-brand-stone/50 font-utility text-xs text-brand-moss uppercase tracking-wider">
                <tr>
                  <th scope="col" class="px-6 py-4">Tanggal</th>
                  <th scope="col" class="px-6 py-4">Program</th>
                  <th scope="col" class="px-6 py-4">Keterangan</th>
                  <th scope="col" class="px-6 py-4">Nominal</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-brand-slate">
                <tr v-for="report in displayReports" :key="report.id" class="hover:bg-brand-stone/20 transition-colors">
                  <td class="px-6 py-4 whitespace-nowrap text-brand-navy/60 font-mono text-xs">{{ report.date }}</td>
                  <td class="px-6 py-4 font-semibold text-brand-teal whitespace-nowrap">{{ report.donation_program?.title || 'Program' }}</td>
                  <td class="px-6 py-4 text-brand-navy/80">{{ report.description }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-red-700 font-bold font-mono text-xs">- {{ formatCurrency(report.amount) }}</td>
                </tr>
                <tr v-if="displayReports.length === 0">
                  <td colspan="4" class="px-6 py-12 text-center text-brand-navy/40 italic">
                    Belum ada laporan pengeluaran dana tercatat.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </SectionContainer>
  </PublicLayout>
</template>
