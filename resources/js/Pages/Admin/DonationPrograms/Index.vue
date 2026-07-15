<script setup>
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import BaseButton from '@/Components/BaseButton.vue';

const props = defineProps({
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
  title: '',
  slug: '',
  description: '',
  target_amount: 0,
  collected_amount: 0,
  deadline: '',
  image: null,
});

const generateSlug = () => {
  form.slug = form.title
    .toLowerCase()
    .replace(/[^a-z0-9 -]/g, '')
    .replace(/\s+/g, '-')
    .replace(/-+/g, '-');
};

const formatCurrency = (val) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(val);
};

const openCreateModal = () => {
  isEditing.value = false;
  editingId.value = null;
  form.reset();
  form.clearErrors();
  form._method = 'POST';
  isModalOpen.value = true;
};

const openEditModal = (prog) => {
  isEditing.value = true;
  editingId.value = prog.id;
  form.clearErrors();
  form._method = 'PUT';
  form.title = prog.title;
  form.slug = prog.slug;
  form.description = prog.description;
  form.target_amount = prog.target_amount;
  form.collected_amount = prog.collected_amount;
  form.deadline = prog.deadline ? prog.deadline.substring(0, 10) : '';
  form.image = null;
  isModalOpen.value = true;
};

const submitForm = () => {
  if (isEditing.value) {
    form.post(route('admin.donation-programs.update', editingId.value), {
      onSuccess: () => {
        isModalOpen.value = false;
        form.reset();
      },
    });
  } else {
    form.post(route('admin.donation-programs.store'), {
      onSuccess: () => {
        isModalOpen.value = false;
        form.reset();
      },
    });
  }
};

const deleteProgram = (id) => {
  if (confirm('Apakah Anda yakin ingin menghapus program donasi ini?')) {
    router.delete(route('admin.donation-programs.destroy', id));
  }
};
</script>

<template>
  <AdminLayout title="Kelola Program Donasi">
    <Head title="CMS - Donasi" />

    <div class="space-y-6 font-sans">
      <!-- Action Banner -->
      <div class="flex justify-between items-center bg-white p-4 rounded-lg border border-brand-slate shadow-sm">
        <span class="text-xs text-brand-navy/60">Kelola target pendanaan donasi dakwah, pembangunan masjid, dan bantuan darurat.</span>
        <BaseButton @click="openCreateModal" variant="primary" class="!py-2 !px-4">
          Tambah Program Donasi
        </BaseButton>
      </div>

      <!-- Data Table -->
      <div class="bg-white rounded-lg border border-brand-slate shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-brand-slate text-left text-sm">
            <thead class="bg-brand-stone/50 font-utility text-xs text-brand-moss uppercase tracking-wider">
              <tr>
                <th scope="col" class="px-6 py-4">Gambar</th>
                <th scope="col" class="px-6 py-4">Program Donasi</th>
                <th scope="col" class="px-6 py-4">Target Dana</th>
                <th scope="col" class="px-6 py-4">Terkumpul</th>
                <th scope="col" class="px-6 py-4">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-brand-slate">
              <tr v-for="prog in donationPrograms" :key="prog.id" class="hover:bg-brand-stone/20 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap">
                  <img
                    v-if="prog.image"
                    :src="prog.image"
                    alt="Image"
                    class="w-16 h-10 object-cover rounded border border-brand-slate"
                  />
                  <span v-else class="text-xs text-brand-navy/40 italic">No Image</span>
                </td>
                <td class="px-6 py-4">
                  <div class="font-semibold text-brand-teal line-clamp-1">{{ prog.title }}</div>
                  <div class="text-xs text-brand-navy/50 font-mono">{{ prog.slug }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-brand-navy/80 font-mono text-xs">
                  {{ formatCurrency(prog.target_amount) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="font-mono text-xs text-brand-sage font-bold">{{ formatCurrency(prog.collected_amount) }}</div>
                  <!-- Progress bar mini -->
                  <div class="w-24 h-1.5 bg-brand-slate rounded-full overflow-hidden mt-1">
                    <div
                      class="h-full bg-brand-gold"
                      :style="`width: ${Math.min((prog.collected_amount / (prog.target_amount || 1)) * 100, 100)}%`"
                    ></div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-xs font-semibold space-x-3">
                  <button @click="openEditModal(prog)" class="text-brand-sage hover:text-brand-teal transition-colors cursor-pointer">
                    Edit
                  </button>
                  <button @click="deleteProgram(prog.id)" class="text-red-700 hover:text-red-900 transition-colors cursor-pointer">
                    Hapus
                  </button>
                </td>
              </tr>
              <tr v-if="donationPrograms.length === 0">
                <td colspan="5" class="px-6 py-12 text-center text-brand-navy/40 italic">
                  Belum ada program donasi. Klik "Tambah Program Donasi" untuk memulai.
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
            {{ isEditing ? 'Edit Program Donasi' : 'Tambah Program Donasi Baru' }}
          </h3>
          <button @click="isModalOpen = false" class="p-1 rounded hover:bg-brand-slate text-brand-navy focus:outline-none">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Form Body -->
        <form @submit.prevent="submitForm" class="flex-1 overflow-y-auto p-6 space-y-4 text-left">
          <!-- Title -->
          <div class="space-y-1">
            <label class="block text-xs font-utility font-bold text-brand-moss uppercase tracking-wider">Nama Program Donasi</label>
            <input
              v-model="form.title"
              @input="generateSlug"
              type="text"
              required
              class="w-full rounded border border-brand-slate px-3 py-2 text-sm focus:outline-none focus:border-brand-sage focus:ring-1 focus:ring-brand-sage"
            />
            <span v-if="form.errors.title" class="text-xs text-red-600 block">{{ form.errors.title }}</span>
          </div>

          <!-- Slug -->
          <div class="space-y-1">
            <label class="block text-xs font-utility font-bold text-brand-moss uppercase tracking-wider">Slug URL</label>
            <input
              v-model="form.slug"
              type="text"
              required
              class="w-full rounded border border-brand-slate px-3 py-2 text-sm bg-brand-stone/40 font-mono text-brand-navy/70 focus:outline-none"
            />
            <span v-if="form.errors.slug" class="text-xs text-red-600 block">{{ form.errors.slug }}</span>
            <p class="text-[11px] text-brand-navy/50 leading-relaxed mt-1">
              Fungsi: Digunakan sebagai alamat URL unik halaman detail donasi ini di website (contoh: /donasi/<strong>slug-url-ini</strong>). Dibuat otomatis saat Anda mengisi Nama Program Donasi.
            </p>
          </div>

          <!-- Description -->
          <div class="space-y-1">
            <label class="block text-xs font-utility font-bold text-brand-moss uppercase tracking-wider">Deskripsi & Ajakan Donasi</label>
            <textarea
              v-model="form.description"
              rows="4"
              required
              class="w-full rounded border border-brand-slate px-3 py-2 text-sm focus:outline-none focus:border-brand-sage focus:ring-1 focus:ring-brand-sage"
            ></textarea>
            <span v-if="form.errors.description" class="text-xs text-red-600 block">{{ form.errors.description }}</span>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Target Amount -->
            <div class="space-y-1">
              <label class="block text-xs font-utility font-bold text-brand-moss uppercase tracking-wider">Target Dana (IDR)</label>
              <input
                v-model="form.target_amount"
                type="number"
                min="0"
                required
                class="w-full rounded border border-brand-slate px-3 py-2 text-sm focus:outline-none focus:border-brand-sage focus:ring-1 focus:ring-brand-sage"
              />
              <span v-if="form.errors.target_amount" class="text-xs text-red-600 block">{{ form.errors.target_amount }}</span>
            </div>

            <!-- Collected Amount -->
            <div class="space-y-1">
              <label class="block text-xs font-utility font-bold text-brand-moss uppercase tracking-wider">Dana Terkumpul Saat Ini (IDR)</label>
              <input
                v-model="form.collected_amount"
                type="number"
                min="0"
                class="w-full rounded border border-brand-slate px-3 py-2 text-sm focus:outline-none focus:border-brand-sage focus:ring-1 focus:ring-brand-sage"
              />
              <span v-if="form.errors.collected_amount" class="text-xs text-red-600 block">{{ form.errors.collected_amount }}</span>
            </div>
          </div>

          <!-- Deadline -->
          <div class="space-y-1">
            <label class="block text-xs font-utility font-bold text-brand-moss uppercase tracking-wider">Batas Waktu / Deadline (Optional)</label>
            <input
              v-model="form.deadline"
              type="date"
              class="w-full rounded border border-brand-slate px-3 py-2 text-sm focus:outline-none focus:border-brand-sage focus:ring-1 focus:ring-brand-sage"
            />
            <span v-if="form.errors.deadline" class="text-xs text-red-600 block">{{ form.errors.deadline }}</span>
          </div>

          <!-- Image -->
          <div class="space-y-1">
            <label class="block text-xs font-utility font-bold text-brand-moss uppercase tracking-wider">Foto Pendukung Program</label>
            <input
              @input="form.image = $event.target.files[0]"
              type="file"
              accept="image/*"
              class="w-full text-sm text-brand-navy/70 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-brand-sage/10 file:text-brand-sage hover:file:bg-brand-sage/20 cursor-pointer"
            />
            <span v-if="form.errors.image" class="text-xs text-red-600 block">{{ form.errors.image }}</span>
            <span v-if="isEditing" class="text-[10px] text-brand-navy/50 italic block">Biarkan kosong jika tidak ingin mengubah foto.</span>
          </div>
        </form>

        <!-- Footer -->
        <div class="px-6 py-4 border-t border-brand-slate bg-brand-stone/30 flex justify-end space-x-3">
          <BaseButton @click="isModalOpen = false" variant="secondary" class="!py-2 !px-4">
            Batal
          </BaseButton>
          <BaseButton @click="submitForm" variant="primary" class="!py-2 !px-4" :disabled="form.processing">
            {{ form.processing ? 'Menyimpan...' : (isEditing ? 'Simpan Perubahan' : 'Tambah Program') }}
          </BaseButton>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
