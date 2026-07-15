<script setup>
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import BaseButton from '@/Components/BaseButton.vue';

const props = defineProps({
  donationReports: {
    type: Array,
    required: true,
  },
  donationPrograms: {
    type: Array,
    required: true,
  },
});

const isModalOpen = ref(false);
const isEditing = ref(false);
const editingId = ref(null);

const form = useForm({
  _method: 'POST',
  donation_program_id: props.donationPrograms[0]?.id || '',
  description: '',
  amount: 0,
  date: new Date().toISOString().substring(0, 10),
});

const formatCurrency = (val) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(val);
};

const formatDate = (val) => {
  if (!val) return '-';
  const d = new Date(val);
  if (isNaN(d)) return val;
  return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
};

const openCreateModal = () => {
  isEditing.value = false;
  editingId.value = null;
  form.reset();
  form.clearErrors();
  if (props.donationPrograms.length > 0) {
    form.donation_program_id = props.donationPrograms[0].id;
  }
  form.date = new Date().toISOString().substring(0, 10);
  form._method = 'POST';
  isModalOpen.value = true;
};

const openEditModal = (report) => {
  isEditing.value = true;
  editingId.value = report.id;
  form.clearErrors();
  form._method = 'PUT';
  form.donation_program_id = report.donation_program_id;
  form.description = report.description;
  form.amount = report.amount;
  form.date = report.date ? report.date.substring(0, 10) : '';
  isModalOpen.value = true;
};

const submitForm = () => {
  if (isEditing.value) {
    form.post(route('admin.donation-reports.update', editingId.value), {
      onSuccess: () => {
        isModalOpen.value = false;
        form.reset();
      },
    });
  } else {
    form.post(route('admin.donation-reports.store'), {
      onSuccess: () => {
        isModalOpen.value = false;
        form.reset();
      },
    });
  }
};

const deleteReport = (id) => {
  if (confirm('Apakah Anda yakin ingin menghapus laporan transaksi ini?')) {
    router.delete(route('admin.donation-reports.destroy', id));
  }
};
</script>

<template>
  <AdminLayout title="Kelola Laporan Transparansi Dana">
    <Head title="CMS - Laporan Donasi" />

    <div class="space-y-6 font-sans">
      <!-- Action Banner -->
      <div class="flex justify-between items-center bg-white p-4 rounded-lg border border-brand-slate shadow-sm">
        <span class="text-xs text-brand-navy/60">Catat realisasi/pengeluaran dana donasi untuk menjamin asas transparansi umat.</span>
        <BaseButton @click="openCreateModal" variant="primary" class="!py-2 !px-4" :disabled="donationPrograms.length === 0">
          Tambah Transaksi Pengeluaran
        </BaseButton>
      </div>

      <!-- Data Table -->
      <div class="bg-white rounded-lg border border-brand-slate shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-brand-slate text-left text-sm">
            <thead class="bg-brand-stone/50 font-utility text-xs text-brand-moss uppercase tracking-wider">
              <tr>
                <th scope="col" class="px-6 py-4">Tanggal</th>
                <th scope="col" class="px-6 py-4">Program Hubungan</th>
                <th scope="col" class="px-6 py-4">Deskripsi Penggunaan</th>
                <th scope="col" class="px-6 py-4">Jumlah Dana Keluar</th>
                <th scope="col" class="px-6 py-4">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-brand-slate">
              <tr v-for="report in donationReports" :key="report.id" class="hover:bg-brand-stone/20 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap text-brand-navy/80 text-xs">
                  {{ formatDate(report.date) }}
                </td>
                <td class="px-6 py-4">
                  <div class="font-semibold text-brand-teal line-clamp-1">
                    {{ report.donation_program?.title || 'Program Dihapus' }}
                  </div>
                </td>
                <td class="px-6 py-4">
                  <div class="text-brand-navy/80 line-clamp-2 max-w-sm leading-relaxed">{{ report.description }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-red-700 font-bold font-mono text-xs">
                  - {{ formatCurrency(report.amount) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-xs font-semibold space-x-3">
                  <button @click="openEditModal(report)" class="text-brand-sage hover:text-brand-teal transition-colors cursor-pointer">
                    Edit
                  </button>
                  <button @click="deleteReport(report.id)" class="text-red-700 hover:text-red-900 transition-colors cursor-pointer">
                    Hapus
                  </button>
                </td>
              </tr>
              <tr v-if="donationReports.length === 0">
                <td colspan="5" class="px-6 py-12 text-center text-brand-navy/40 italic">
                  Belum ada laporan pengeluaran dana. Klik "Tambah Transaksi" untuk mencatat.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div v-show="isModalOpen" class="fixed inset-0 z-50 overflow-y-auto bg-brand-teal/50 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="bg-white rounded-xl max-w-2xl w-full border border-brand-slate shadow-2xl flex flex-col max-h-[90vh]">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-brand-slate flex justify-between items-center bg-brand-stone/30">
          <h3 class="font-display text-lg text-brand-teal uppercase tracking-wider font-bold">
            {{ isEditing ? 'Edit Laporan Pengeluaran' : 'Catat Laporan Pengeluaran Baru' }}
          </h3>
          <button @click="isModalOpen = false" class="p-1 rounded hover:bg-brand-slate text-brand-navy focus:outline-none">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Form Body -->
        <form @submit.prevent="submitForm" class="flex-1 overflow-y-auto p-6 space-y-4 text-left">
          <!-- Donation Program Relation selection -->
          <div class="space-y-1">
            <label class="block text-xs font-utility font-bold text-brand-moss uppercase tracking-wider">Pilih Program Donasi Terkait</label>
            <select
              v-model="form.donation_program_id"
              required
              class="w-full rounded border border-brand-slate px-3 py-2 text-sm focus:outline-none focus:border-brand-sage focus:ring-1 focus:ring-brand-sage"
            >
              <option v-for="prog in donationPrograms" :key="prog.id" :value="prog.id">
                {{ prog.title }}
              </option>
            </select>
            <span v-if="form.errors.donation_program_id" class="text-xs text-red-600 block">{{ form.errors.donation_program_id }}</span>
          </div>

          <!-- Description -->
          <div class="space-y-1">
            <label class="block text-xs font-utility font-bold text-brand-moss uppercase tracking-wider">Keterangan Transaksi</label>
            <textarea
              v-model="form.description"
              rows="3"
              required
              placeholder="Contoh: Pembelian semen 50 sak untuk renovasi pilar masjid."
              class="w-full rounded border border-brand-slate px-3 py-2 text-sm focus:outline-none focus:border-brand-sage focus:ring-1 focus:ring-brand-sage"
            ></textarea>
            <span v-if="form.errors.description" class="text-xs text-red-600 block">{{ form.errors.description }}</span>
          </div>

          <!-- Amount -->
          <div class="space-y-1">
            <label class="block text-xs font-utility font-bold text-brand-moss uppercase tracking-wider">Jumlah Pengeluaran (IDR)</label>
            <input
              v-model="form.amount"
              type="number"
              min="1"
              required
              class="w-full rounded border border-brand-slate px-3 py-2 text-sm focus:outline-none focus:border-brand-sage focus:ring-1 focus:ring-brand-sage"
            />
            <span v-if="form.errors.amount" class="text-xs text-red-600 block">{{ form.errors.amount }}</span>
          </div>

          <!-- Date -->
          <div class="space-y-1">
            <label class="block text-xs font-utility font-bold text-brand-moss uppercase tracking-wider">Tanggal Transaksi</label>
            <input
              v-model="form.date"
              type="date"
              required
              class="w-full rounded border border-brand-slate px-3 py-2 text-sm focus:outline-none focus:border-brand-sage focus:ring-1 focus:ring-brand-sage"
            />
            <span v-if="form.errors.date" class="text-xs text-red-600 block">{{ form.errors.date }}</span>
          </div>
        </form>

        <!-- Footer -->
        <div class="px-6 py-4 border-t border-brand-slate bg-brand-stone/30 flex justify-end space-x-3">
          <BaseButton @click="isModalOpen = false" variant="secondary" class="!py-2 !px-4">
            Batal
          </BaseButton>
          <BaseButton @click="submitForm" variant="primary" class="!py-2 !px-4" :disabled="form.processing">
            {{ form.processing ? 'Menyimpan...' : (isEditing ? 'Simpan Perubahan' : 'Catat Pengeluaran') }}
          </BaseButton>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
