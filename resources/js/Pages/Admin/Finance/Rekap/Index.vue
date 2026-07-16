<script setup>
import { computed } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import BaseButton from '@/Components/BaseButton.vue';

const props = defineProps({
  reports: {
    type: Array,
    required: true,
  },
  rekap: {
    type: Object,
    default: null,
  },
  selectedBulan: {
    type: Number,
    required: true,
  },
  selectedTahun: {
    type: Number,
    required: true,
  },
  canGenerate: {
    type: Boolean,
    required: true,
  },
});

const months = [
  { val: 1, name: 'Januari' },
  { val: 2, name: 'Februari' },
  { val: 3, name: 'Maret' },
  { val: 4, name: 'April' },
  { val: 5, name: 'Mei' },
  { val: 6, name: 'Juni' },
  { val: 7, name: 'Juli' },
  { val: 8, name: 'Agustus' },
  { val: 9, name: 'September' },
  { val: 10, name: 'Oktober' },
  { val: 11, name: 'November' },
  { val: 12, name: 'Desember' },
];

const years = [2025, 2026, 2027];

const getMonthName = (val) => {
  return months.find(m => m.val === val)?.name || val;
};

const formatCurrency = (val) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(val);
};

const changePeriod = (e) => {
  const name = e.target.name;
  const value = e.target.value;
  
  const query = {
    bulan: name === 'bulan' ? value : props.selectedBulan,
    tahun: name === 'tahun' ? value : props.selectedTahun,
  };

  router.get(route('admin.finance.rekap.index'), query);
};

const generateForm = useForm({
  bulan: props.selectedBulan,
  tahun: props.selectedTahun,
});

const handleGenerateRekap = () => {
  if (confirm('Apakah Anda yakin ingin menggabungkan seluruh laporan bidang menjadi Rekap Konsolidasi Yayasan bulanan? Tindakan ini akan mengunci seluruh laporan bidang selamanya.')) {
    generateForm.post(route('admin.finance.rekap.generate'));
  }
};

const getStatusBadgeClass = (status) => {
  switch (status) {
    case 'belum_buka':
      return 'bg-red-50 text-red-600 border border-red-150';
    case 'draft':
      return 'bg-amber-50 text-amber-600 border border-amber-150';
    case 'diajukan':
      return 'bg-blue-50 text-blue-700 border border-blue-200 animate-pulse';
    case 'diverifikasi':
      return 'bg-emerald-50 text-emerald-700 border border-emerald-150';
    case 'dikonsolidasi':
      return 'bg-brand-teal/10 text-brand-teal border border-brand-teal/20';
    default:
      return 'bg-gray-50 text-gray-700 border border-gray-150';
  }
};

const translateStatus = (status) => {
  switch (status) {
    case 'belum_buka': return 'Belum Dibuka';
    case 'draft': return 'Draft (Input)';
    case 'diajukan': return 'Menunggu Review';
    case 'diverifikasi': return 'Diverifikasi';
    case 'dikonsolidasi': return 'Telah Dikonsolidasi';
    default: return status;
  }
};
</script>

<template>
  <AdminLayout title="Verifikasi &amp; Konsolidasi Laporan">
    <Head title="Verifikasi Keuangan" />

    <div class="space-y-6 font-sans">
      <!-- Header with month/year filter -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white rounded-xl border border-brand-slate p-6 shadow-sm">
        <div>
          <h3 class="text-lg font-bold text-brand-teal">Verifikasi &amp; Konsolidasi Bulanan</h3>
          <p class="text-xs text-brand-navy/60 mt-1">Filter laporan keuangan untuk verifikasi per bidang dan penerbitan Rekap Konsolidasi.</p>
        </div>
        <div class="flex items-center gap-3">
          <div>
            <select
              name="bulan"
              :value="selectedBulan"
              @change="changePeriod"
              class="rounded-md border-brand-slate text-brand-navy shadow-sm focus:border-brand-sage focus:ring focus:ring-brand-sage/20 text-xs font-semibold cursor-pointer"
            >
              <option v-for="m in months" :key="m.val" :value="m.val">
                {{ m.name }}
              </option>
            </select>
          </div>
          <div>
            <select
              name="tahun"
              :value="selectedTahun"
              @change="changePeriod"
              class="rounded-md border-brand-slate text-brand-navy shadow-sm focus:border-brand-sage focus:ring focus:ring-brand-sage/20 text-xs font-semibold cursor-pointer"
            >
              <option v-for="y in years" :key="y" :value="y">
                {{ y }}
              </option>
            </select>
          </div>
        </div>
      </div>

      <!-- Generate Rekap trigger banner / status -->
      <div class="bg-white rounded-xl border border-brand-slate p-6 shadow-sm flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="flex items-start gap-4">
          <span class="p-3 bg-brand-teal/5 text-brand-teal rounded-lg shrink-0">
            <!-- Document report icon -->
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </span>
          <div class="space-y-1">
            <h4 class="font-bold text-brand-teal">Rekap Konsolidasi Yayasan - {{ getMonthName(selectedBulan) }} {{ selectedTahun }}</h4>
            <p class="text-xs text-brand-navy/60 max-w-xl leading-relaxed">
              Apabila seluruh laporan dari 6 bidang bendahara telah berstatus <strong>Diverifikasi</strong>, Anda dapat menekan tombol generate di sebelah kanan untuk menggabungkan seluruh kas menjadi satu laporan besar.
            </p>
          </div>
        </div>
        
        <div>
          <!-- Show details button if already generated -->
          <Link 
            v-if="rekap"
            :href="route('admin.finance.rekap.show', { bulan: selectedBulan, tahun: selectedTahun })"
            class="inline-flex items-center px-5 py-2.5 bg-brand-teal text-brand-stone font-semibold text-xs rounded hover:bg-brand-sage hover:text-brand-stone transition-all duration-300 shadow-md shadow-brand-teal/10 cursor-pointer"
          >
            Buka Rekap Konsolidasi &rarr;
          </Link>
          
          <!-- Generate trigger button -->
          <BaseButton 
            v-else
            @click="handleGenerateRekap"
            variant="primary"
            class="!py-2.5 !px-5 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
            :disabled="!canGenerate || generateForm.processing"
          >
            Generate Rekap Konsolidasi
          </BaseButton>
        </div>
      </div>

      <!-- Grid Matrix of Fields -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div 
          v-for="r in reports" 
          :key="r.bidang.id" 
          class="bg-white rounded-xl border border-brand-slate p-6 shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow duration-300"
        >
          <div>
            <!-- Bidang Name & Badge -->
            <div class="flex items-start justify-between gap-4">
              <span class="font-bold text-sm text-brand-teal tracking-wide leading-tight">{{ r.bidang.nama_bidang }}</span>
              <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold" :class="getStatusBadgeClass(r.status)">
                {{ translateStatus(r.status) }}
              </span>
            </div>
            
            <div class="w-full bg-brand-slate/20 h-px my-4"></div>
            
            <!-- Cash Summary -->
            <div class="space-y-2">
              <div class="flex items-center justify-between text-xs">
                <span class="text-brand-navy/60 font-medium">Pemasukan (+)</span>
                <span class="font-semibold text-emerald-600">{{ formatCurrency(r.total_pemasukan) }}</span>
              </div>
              <div class="flex items-center justify-between text-xs">
                <span class="text-brand-navy/60 font-medium">Pengeluaran (-)</span>
                <span class="font-semibold text-red-600">{{ formatCurrency(r.total_pengeluaran) }}</span>
              </div>
              <div class="flex items-center justify-between text-xs pt-2 border-t border-brand-slate/40">
                <span class="text-brand-navy/80 font-bold">Saldo Akhir</span>
                <span class="font-bold text-brand-teal">{{ formatCurrency(r.saldo_akhir) }}</span>
              </div>
            </div>
          </div>
          
          <!-- Bottom Action link -->
          <div class="mt-6 pt-4 border-t border-brand-slate">
            <template v-if="r.status === 'belum_buka'">
              <span class="text-xs text-brand-navy/35 italic block text-center">Buku kas belum dibuka</span>
            </template>
            <template v-else>
              <Link 
                :href="route('admin.finance.periods.show', r.period.id)"
                class="w-full text-center text-xs font-semibold rounded bg-brand-sage/10 text-brand-sage hover:bg-brand-sage hover:text-brand-stone py-2 block transition-all duration-300 cursor-pointer"
              >
                {{ r.status === 'diajukan' ? 'Review &amp; Verifikasi Laporan' : 'Buka Laporan Lanjutan' }}
              </Link>
            </template>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
