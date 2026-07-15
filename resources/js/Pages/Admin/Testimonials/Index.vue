<script setup>
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import BaseButton from '@/Components/BaseButton.vue';

const props = defineProps({
  testimonials: {
    type: Array,
    required: true,
  },
});

const isModalOpen = ref(false);
const isEditing = ref(false);
const editingId = ref(null);

const form = useForm({
  _method: 'POST',
  name: '',
  message: '',
  is_featured: false,
  photo: null,
});

const openCreateModal = () => {
  isEditing.value = false;
  editingId.value = null;
  form.reset();
  form.clearErrors();
  form._method = 'POST';
  isModalOpen.value = true;
};

const openEditModal = (testi) => {
  isEditing.value = true;
  editingId.value = testi.id;
  form.clearErrors();
  form._method = 'PUT';
  form.name = testi.name;
  form.message = testi.message;
  form.is_featured = !!testi.is_featured;
  form.photo = null;
  isModalOpen.value = true;
};

const submitForm = () => {
  if (isEditing.value) {
    form.post(route('admin.testimonials.update', editingId.value), {
      onSuccess: () => {
        isModalOpen.value = false;
        form.reset();
      },
    });
  } else {
    form.post(route('admin.testimonials.store'), {
      onSuccess: () => {
        isModalOpen.value = false;
        form.reset();
      },
    });
  }
};

const deleteTesti = (id) => {
  if (confirm('Apakah Anda yakin ingin menghapus testimoni ini?')) {
    router.delete(route('admin.testimonials.destroy', id));
  }
};
</script>

<template>
  <AdminLayout title="Kelola Testimoni">
    <Head title="CMS - Testimoni" />

    <div class="space-y-6 font-sans">
      <!-- Action Banner -->
      <div class="flex justify-between items-center bg-white p-4 rounded-lg border border-brand-slate shadow-sm">
        <span class="text-xs text-brand-navy/60">Daftar testimoni jamaah dan donatur. Tandai "Unggulan" untuk tampil di halaman utama.</span>
        <BaseButton @click="openCreateModal" variant="primary" class="!py-2 !px-4">
          Tambah Testimoni
        </BaseButton>
      </div>

      <!-- Data Table -->
      <div class="bg-white rounded-lg border border-brand-slate shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-brand-slate text-left text-sm">
            <thead class="bg-brand-stone/50 font-utility text-xs text-brand-moss uppercase tracking-wider">
              <tr>
                <th scope="col" class="px-6 py-4">Foto</th>
                <th scope="col" class="px-6 py-4">Nama</th>
                <th scope="col" class="px-6 py-4">Isi Ulasan</th>
                <th scope="col" class="px-6 py-4">Unggulan?</th>
                <th scope="col" class="px-6 py-4">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-brand-slate">
              <tr v-for="testi in testimonials" :key="testi.id" class="hover:bg-brand-stone/20 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap">
                  <img
                    v-if="testi.photo"
                    :src="testi.photo"
                    alt="Photo"
                    class="w-10 h-10 object-cover rounded-full border border-brand-slate"
                  />
                  <span v-else class="text-xs text-brand-navy/40 italic">No Photo</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap font-semibold text-brand-teal">
                  {{ testi.name }}
                </td>
                <td class="px-6 py-4">
                  <div class="text-brand-navy/80 line-clamp-2 leading-relaxed max-w-md">{{ testi.message }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    v-if="testi.is_featured"
                    class="px-2 py-0.5 rounded text-[10px] font-utility font-bold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200"
                  >
                    Unggulan
                  </span>
                  <span v-else class="text-xs text-brand-navy/40">-</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-xs font-semibold space-x-3">
                  <button @click="openEditModal(testi)" class="text-brand-sage hover:text-brand-teal transition-colors cursor-pointer">
                    Edit
                  </button>
                  <button @click="deleteTesti(testi.id)" class="text-red-700 hover:text-red-900 transition-colors cursor-pointer">
                    Hapus
                  </button>
                </td>
              </tr>
              <tr v-if="testimonials.length === 0">
                <td colspan="5" class="px-6 py-12 text-center text-brand-navy/40 italic">
                  Belum ada testimoni. Klik "Tambah Testimoni" untuk memulai.
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
            {{ isEditing ? 'Edit Testimoni' : 'Tambah Testimoni Baru' }}
          </h3>
          <button @click="isModalOpen = false" class="p-1 rounded hover:bg-brand-slate text-brand-navy focus:outline-none">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Form Body -->
        <form @submit.prevent="submitForm" class="flex-1 overflow-y-auto p-6 space-y-4 text-left">
          <!-- Name -->
          <div class="space-y-1">
            <label class="block text-xs font-utility font-bold text-brand-moss uppercase tracking-wider">Nama Donatur / Jamaah</label>
            <input
              v-model="form.name"
              type="text"
              required
              class="w-full rounded border border-brand-slate px-3 py-2 text-sm focus:outline-none focus:border-brand-sage focus:ring-1 focus:ring-brand-sage"
            />
            <span v-if="form.errors.name" class="text-xs text-red-600 block">{{ form.errors.name }}</span>
          </div>

          <!-- Message -->
          <div class="space-y-1">
            <label class="block text-xs font-utility font-bold text-brand-moss uppercase tracking-wider">Isi Testimoni</label>
            <textarea
              v-model="form.message"
              rows="4"
              required
              class="w-full rounded border border-brand-slate px-3 py-2 text-sm focus:outline-none focus:border-brand-sage focus:ring-1 focus:ring-brand-sage"
            ></textarea>
            <span v-if="form.errors.message" class="text-xs text-red-600 block">{{ form.errors.message }}</span>
          </div>

          <!-- Photo -->
          <div class="space-y-1">
            <label class="block text-xs font-utility font-bold text-brand-moss uppercase tracking-wider">Foto Profil (Optional)</label>
            <input
              @input="form.photo = $event.target.files[0]"
              type="file"
              accept="image/*"
              class="w-full text-sm text-brand-navy/70 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-brand-sage/10 file:text-brand-sage hover:file:bg-brand-sage/20 cursor-pointer"
            />
            <span v-if="form.errors.photo" class="text-xs text-red-600 block">{{ form.errors.photo }}</span>
          </div>

          <!-- Is Featured -->
          <div class="flex items-center space-x-2 pt-2">
            <input
              v-model="form.is_featured"
              type="checkbox"
              id="is_featured"
              class="rounded border-brand-slate text-brand-sage focus:ring-brand-sage"
            />
            <label for="is_featured" class="text-xs font-utility font-bold text-brand-moss uppercase tracking-wider cursor-pointer">
              Tampilkan Sebagai Testimoni Unggulan di Homepage
            </label>
          </div>
        </form>

        <!-- Footer -->
        <div class="px-6 py-4 border-t border-brand-slate bg-brand-stone/30 flex justify-end space-x-3">
          <BaseButton @click="isModalOpen = false" variant="secondary" class="!py-2 !px-4">
            Batal
          </BaseButton>
          <BaseButton @click="submitForm" variant="primary" class="!py-2 !px-4" :disabled="form.processing">
            {{ form.processing ? 'Menyimpan...' : (isEditing ? 'Simpan Perubahan' : 'Tambah Testimoni') }}
          </BaseButton>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
