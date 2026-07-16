<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import BaseButton from '@/Components/BaseButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
  bidangs: {
    type: Array,
    required: true,
  },
  subKategoris: {
    type: Array,
    required: true,
  },
});

const isModalOpen = ref(false);
const modalMode = ref('create'); // 'create' or 'edit'
const editingId = ref(null);

const form = useForm({
  bidang_id: '',
  kategori: 'pemasukan',
  nama_sub_kategori: '',
  is_active: true,
});

const openCreateModal = () => {
  modalMode.value = 'create';
  editingId.value = null;
  form.reset();
  form.clearErrors();
  form.bidang_id = props.bidangs[0]?.id || '';
  isModalOpen.value = true;
};

const openEditModal = (item) => {
  modalMode.value = 'edit';
  editingId.value = item.id;
  form.clearErrors();
  form.bidang_id = item.bidang_id;
  form.kategori = item.kategori;
  form.nama_sub_kategori = item.nama_sub_kategori;
  form.is_active = item.is_active;
  isModalOpen.value = true;
};

const submitForm = () => {
  if (modalMode.value === 'create') {
    form.post(route('admin.finance.sub-kategori.store'), {
      onSuccess: () => {
        isModalOpen.value = false;
        form.reset();
      },
    });
  } else {
    form.patch(route('admin.finance.sub-kategori.update', editingId.value), {
      onSuccess: () => {
        isModalOpen.value = false;
        form.reset();
      },
    });
  }
};

const deleteForm = useForm({});
const deleteItem = (id) => {
  if (confirm('Apakah Anda yakin ingin menghapus sub-kategori ini?')) {
    deleteForm.delete(route('admin.finance.sub-kategori.destroy', id));
  }
};
</script>

<template>
  <AdminLayout title="Kelola Sub-Kategori Transaksi">
    <Head title="Kelola Sub-Kategori" />

    <div class="space-y-6">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h3 class="text-lg font-bold text-brand-teal">Master Data Sub-Kategori</h3>
          <p class="text-xs text-brand-navy/60">Konfigurasi sub-kategori pemasukan dan pengeluaran untuk masing-masing bidang keuangan.</p>
        </div>
        <div>
          <BaseButton @click="openCreateModal" variant="primary" class="!py-2 !px-4 cursor-pointer">
            Tambah Sub-Kategori
          </BaseButton>
        </div>
      </div>

      <!-- Error / Flash messages -->
      <div v-if="$page.props.flash && $page.props.flash.error" class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
        {{ $page.props.flash.error }}
      </div>

      <!-- Desktop List Table -->
      <div class="bg-white rounded-xl border border-brand-slate shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse font-sans text-sm">
            <thead>
              <tr class="bg-brand-slate/40 text-brand-teal border-b border-brand-slate font-semibold">
                <th class="py-3 px-4">Nama Bidang</th>
                <th class="py-3 px-4">Jenis</th>
                <th class="py-3 px-4">Nama Sub-Kategori</th>
                <th class="py-3 px-4">Status</th>
                <th class="py-3 px-4 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-brand-slate/40">
              <tr v-for="item in subKategoris" :key="item.id" class="hover:bg-brand-stone/30 transition-colors">
                <td class="py-3.5 px-4 font-medium text-brand-navy">{{ item.bidang?.nama_bidang }}</td>
                <td class="py-3.5 px-4">
                  <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold"
                    :class="[
                      item.kategori === 'pemasukan' 
                        ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' 
                        : 'bg-red-50 text-red-700 border border-red-200'
                    ]"
                  >
                    {{ item.kategori === 'pemasukan' ? 'Pemasukan' : 'Pengeluaran' }}
                  </span>
                </td>
                <td class="py-3.5 px-4 font-medium text-brand-navy">{{ item.nama_sub_kategori }}</td>
                <td class="py-3.5 px-4">
                  <span class="inline-flex items-center gap-1.5" :class="[item.is_active ? 'text-emerald-600' : 'text-gray-400']">
                    <span class="w-2 h-2 rounded-full" :class="[item.is_active ? 'bg-emerald-500' : 'bg-gray-300']"></span>
                    {{ item.is_active ? 'Aktif' : 'Non-aktif' }}
                  </span>
                </td>
                <td class="py-3.5 px-4 text-right space-x-3">
                  <button @click="openEditModal(item)" class="text-brand-sage hover:text-brand-teal font-semibold text-xs cursor-pointer">
                    Edit
                  </button>
                  <button @click="deleteItem(item.id)" class="text-red-600 hover:text-red-800 font-semibold text-xs cursor-pointer">
                    Hapus
                  </button>
                </td>
              </tr>
              <tr v-if="subKategoris.length === 0">
                <td colspan="5" class="py-8 text-center text-brand-navy/55 italic">
                  Belum ada data sub-kategori. Silakan tambahkan baru.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div v-show="isModalOpen" class="fixed inset-0 z-50 overflow-y-auto bg-brand-teal/50 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="bg-white rounded-xl max-w-md w-full shadow-2xl border border-brand-slate overflow-hidden transform transition-all">
        <!-- Modal Header -->
        <div class="bg-brand-teal text-brand-stone px-6 py-4 flex items-center justify-between border-b border-brand-sage/20">
          <h4 class="font-display text-lg uppercase tracking-wider text-brand-gold">
            {{ modalMode === 'create' ? 'Tambah Sub-Kategori' : 'Edit Sub-Kategori' }}
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
            <!-- Bidang Selection -->
            <div>
              <InputLabel for="bidang_id" value="Bidang Keuangan" />
              <select
                id="bidang_id"
                v-model="form.bidang_id"
                class="mt-1 block w-full rounded-md border-brand-slate text-brand-navy shadow-sm focus:border-brand-sage focus:ring focus:ring-brand-sage/20 font-sans text-sm"
                :disabled="modalMode === 'edit'"
                required
              >
                <option v-for="b in bidangs" :key="b.id" :value="b.id">
                  {{ b.nama_bidang }}
                </option>
              </select>
              <InputError :message="form.errors.bidang_id" class="mt-1" />
            </div>

            <!-- Kategori / Jenis -->
            <div>
              <InputLabel value="Jenis Transaksi" />
              <div class="mt-2 flex items-center space-x-6">
                <label class="inline-flex items-center text-sm text-brand-navy cursor-pointer">
                  <input
                    type="radio"
                    v-model="form.kategori"
                    value="pemasukan"
                    class="text-brand-sage focus:ring-brand-sage border-brand-slate"
                    :disabled="modalMode === 'edit'"
                  />
                  <span class="ml-2 font-medium">Pemasukan</span>
                </label>
                <label class="inline-flex items-center text-sm text-brand-navy cursor-pointer">
                  <input
                    type="radio"
                    v-model="form.kategori"
                    value="pengeluaran"
                    class="text-brand-sage focus:ring-brand-sage border-brand-slate"
                    :disabled="modalMode === 'edit'"
                  />
                  <span class="ml-2 font-medium">Pengeluaran</span>
                </label>
              </div>
              <InputError :message="form.errors.kategori" class="mt-1" />
            </div>

            <!-- Nama Sub Kategori -->
            <div>
              <InputLabel for="nama_sub_kategori" value="Nama Sub-Kategori" />
              <TextInput
                id="nama_sub_kategori"
                type="text"
                v-model="form.nama_sub_kategori"
                class="mt-1 block w-full text-sm"
                placeholder="Contoh: Kotak Infaq Jumat, Gaji Karyawan"
                required
              />
              <InputError :message="form.errors.nama_sub_kategori" class="mt-1" />
            </div>

            <!-- Status Aktif (Only in Edit mode) -->
            <div v-if="modalMode === 'edit'" class="flex items-center mt-2">
              <input
                id="is_active"
                type="checkbox"
                v-model="form.is_active"
                class="rounded border-brand-slate text-brand-sage focus:ring-brand-sage h-4 w-4"
              />
              <label for="is_active" class="ml-2 block text-sm font-medium text-brand-navy cursor-pointer">
                Aktifkan Sub-Kategori
              </label>
            </div>
          </div>

          <!-- Modal Footer -->
          <div class="bg-brand-stone/50 px-6 py-4 flex items-center justify-end gap-3 border-t border-brand-slate">
            <BaseButton @click="isModalOpen = false" type="button" variant="secondary" class="!py-2 !px-4 cursor-pointer">
              Batal
            </BaseButton>
            <BaseButton type="submit" variant="primary" class="!py-2 !px-4 cursor-pointer" :disabled="form.processing">
              Simpan
            </BaseButton>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>
