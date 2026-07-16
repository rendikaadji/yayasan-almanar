<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import BaseButton from '@/Components/BaseButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
  periods: {
    type: Array,
    required: true,
  },
  bidangs: {
    type: Array,
    required: true,
  },
});

const page = usePage();
const currentUser = computed(() => page.props.auth.user);

const isModalOpen = ref(false);

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

const form = useForm({
  bidang_id: props.bidangs[0]?.id || '',
  bulan: new Date().getMonth() + 1,
  tahun: new Date().getFullYear(),
  saldo_awal: 0,
});

// Check if selected bidang has any existing periods
const isFirstPeriod = computed(() => {
  const selectedBidangId = parseInt(form.bidang_id);
  if (!selectedBidangId) return true;
  return !props.periods.some(p => parseInt(p.bidang_id) === selectedBidangId);
});

const openCreateModal = () => {
  form.reset();
  form.clearErrors();
  form.bidang_id = props.bidangs[0]?.id || '';
  form.bulan = new Date().getMonth() + 1;
  form.tahun = new Date().getFullYear();
  form.saldo_awal = 0;
  isModalOpen.value = true;
};

const submitForm = () => {
  form.post(route('admin.finance.periods.store'), {
    onSuccess: () => {
      isModalOpen.value = false;
    },
  });
};

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

const getStatusBadgeClass = (status) => {
  switch (status) {
    case 'draft':
      return 'bg-amber-50 text-amber-700 border border-amber-200';
    case 'diajukan':
      return 'bg-blue-50 text-blue-700 border border-blue-200 animate-pulse';
    case 'diverifikasi':
      return 'bg-emerald-50 text-emerald-700 border border-emerald-200';
    case 'dikonsolidasi':
      return 'bg-brand-teal/10 text-brand-teal border border-brand-teal/20';
    default:
      return 'bg-gray-50 text-gray-700 border border-gray-200';
  }
};

const translateStatus = (status) => {
  switch (status) {
    case 'draft': return 'Draft';
    case 'diajukan': return 'Menunggu Verifikasi';
    case 'diverifikasi': return 'Diverifikasi';
    case 'dikonsolidasi': return 'Dikonsolidasi (Selesai)';
    default: return status;
  }
};
</script>

<template>
  <AdminLayout title="Laporan Keuangan Bidang">
    <Head title="Periode Laporan" />

    <div class="space-y-6">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h3 class="text-lg font-bold text-brand-teal">Daftar Periode Laporan Keuangan</h3>
          <p class="text-xs text-brand-navy/60">
            {{ currentUser.role === 'bendahara_bidang' 
                ? 'Kelola transaksi dan ajukan laporan bulanan untuk bidang Anda.' 
                : 'Melihat histori dan status laporan keuangan dari seluruh bidang.' }}
          </p>
        </div>
        <div v-if="currentUser.role !== 'admin'">
          <BaseButton @click="openCreateModal" variant="primary" class="!py-2 !px-4 cursor-pointer">
            Buka Periode Baru
          </BaseButton>
        </div>
      </div>

      <!-- Error / Success notifications -->
      <div v-if="$page.props.flash && $page.props.flash.error" class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
        {{ $page.props.flash.error }}
      </div>

      <!-- Periods Grid/List -->
      <div class="bg-white rounded-xl border border-brand-slate shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse font-sans text-sm">
            <thead>
              <tr class="bg-brand-slate/40 text-brand-teal border-b border-brand-slate font-semibold">
                <th class="py-3 px-4" v-if="currentUser.role !== 'bendahara_bidang'">Bidang</th>
                <th class="py-3 px-4">Bulan / Tahun</th>
                <th class="py-3 px-4">Saldo Awal</th>
                <th class="py-3 px-4">Saldo Akhir</th>
                <th class="py-3 px-4">Status</th>
                <th class="py-3 px-4 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-brand-slate/40">
              <tr v-for="p in periods" :key="p.id" class="hover:bg-brand-stone/30 transition-colors">
                <td class="py-3.5 px-4 font-medium text-brand-navy" v-if="currentUser.role !== 'bendahara_bidang'">
                  {{ p.bidang?.nama_bidang }}
                </td>
                <td class="py-3.5 px-4 font-medium text-brand-navy">
                  {{ getMonthName(p.bulan) }} {{ p.tahun }}
                </td>
                <td class="py-3.5 px-4 text-brand-navy/80">
                  {{ formatCurrency(p.saldo_awal) }}
                </td>
                <td class="py-3.5 px-4 font-semibold text-brand-navy">
                  {{ formatCurrency(p.saldo_akhir) }}
                </td>
                <td class="py-3.5 px-4">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold" :class="getStatusBadgeClass(p.status)">
                    {{ translateStatus(p.status) }}
                  </span>
                </td>
                <td class="py-3.5 px-4 text-right">
                  <Link 
                    :href="route('admin.finance.periods.show', p.id)" 
                    class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded bg-brand-sage/10 text-brand-sage hover:bg-brand-sage hover:text-brand-stone transition-all duration-300 cursor-pointer"
                  >
                    {{ p.status === 'draft' && currentUser.role === 'bendahara_bidang' ? 'Input Transaksi' : 'Lihat Detail' }}
                  </Link>
                </td>
              </tr>
              <tr v-if="periods.length === 0">
                <td colspan="6" class="py-12 text-center text-brand-navy/55 italic">
                  Belum ada periode laporan keuangan yang dibuka.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Create Period Modal -->
    <div v-show="isModalOpen" class="fixed inset-0 z-50 overflow-y-auto bg-brand-teal/50 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="bg-white rounded-xl max-w-md w-full shadow-2xl border border-brand-slate overflow-hidden transform transition-all">
        <!-- Modal Header -->
        <div class="bg-brand-teal text-brand-stone px-6 py-4 flex items-center justify-between border-b border-brand-sage/20">
          <h4 class="font-display text-lg uppercase tracking-wider text-brand-gold">
            Buka Periode Laporan Baru
          </h4>
          <button @click="isModalOpen = false" class="p-1 rounded hover:bg-brand-sage/30 text-brand-stone focus:outline-none cursor-pointer">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <form @submit.prevent="submitForm">
          <!-- Modal Body -->
          <div class="p-6 space-y-4">
            <!-- Bidang Selection (only if not bendahara bidang) -->
            <div v-if="currentUser.role !== 'bendahara_bidang'">
              <InputLabel for="modal_bidang_id" value="Pilih Bidang Keuangan" />
              <select
                id="modal_bidang_id"
                v-model="form.bidang_id"
                class="mt-1 block w-full rounded-md border-brand-slate text-brand-navy shadow-sm focus:border-brand-sage focus:ring focus:ring-brand-sage/20 font-sans text-sm"
                required
              >
                <option v-for="b in bidangs" :key="b.id" :value="b.id">
                  {{ b.nama_bidang }}
                </option>
              </select>
              <InputError :message="form.errors.bidang_id" class="mt-1" />
            </div>

            <div class="grid grid-cols-2 gap-4">
              <!-- Month -->
              <div>
                <InputLabel for="modal_bulan" value="Bulan" />
                <select
                  id="modal_bulan"
                  v-model="form.bulan"
                  class="mt-1 block w-full rounded-md border-brand-slate text-brand-navy shadow-sm focus:border-brand-sage focus:ring focus:ring-brand-sage/20 font-sans text-sm"
                  required
                >
                  <option v-for="m in months" :key="m.val" :value="m.val">
                    {{ m.name }}
                  </option>
                </select>
                <InputError :message="form.errors.bulan" class="mt-1" />
              </div>

              <!-- Year -->
              <div>
                <InputLabel for="modal_tahun" value="Tahun" />
                <select
                  id="modal_tahun"
                  v-model="form.tahun"
                  class="mt-1 block w-full rounded-md border-brand-slate text-brand-navy shadow-sm focus:border-brand-sage focus:ring focus:ring-brand-sage/20 font-sans text-sm"
                  required
                >
                  <option v-for="y in years" :key="y" :value="y">
                    {{ y }}
                  </option>
                </select>
                <InputError :message="form.errors.tahun" class="mt-1" />
              </div>
            </div>

            <!-- Saldo Awal (Only shown for first period of this bidang) -->
            <div v-if="isFirstPeriod">
              <div class="bg-amber-50 border border-amber-200 text-amber-900 rounded-lg p-3 text-xs mb-3 leading-relaxed">
                <strong>Catatan:</strong> Ini adalah periode pertama bidang ini. Masukkan saldo awal kas awal secara manual. Periode selanjutnya akan otomatis terhubung.
              </div>
              <InputLabel for="modal_saldo_awal" value="Saldo Awal Kas (IDR)" />
              <TextInput
                id="modal_saldo_awal"
                type="number"
                v-model="form.saldo_awal"
                class="mt-1 block w-full text-sm"
                placeholder="Contoh: 5000000"
                min="0"
                required
              />
              <InputError :message="form.errors.saldo_awal" class="mt-1" />
            </div>
            <div v-else class="bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-lg p-3 text-xs leading-relaxed">
              <strong>Info:</strong> Saldo awal bulan ini akan secara otomatis diambil dari saldo akhir periode bulan sebelumnya yang terdaftar di database.
            </div>
          </div>

          <!-- Modal Footer -->
          <div class="bg-brand-stone/50 px-6 py-4 flex items-center justify-end gap-3 border-t border-brand-slate font-sans">
            <BaseButton @click="isModalOpen = false" type="button" variant="secondary" class="!py-2 !px-4 cursor-pointer">
              Batal
            </BaseButton>
            <BaseButton type="submit" variant="primary" class="!py-2 !px-4 cursor-pointer" :disabled="form.processing">
              Buka Periode
            </BaseButton>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>
