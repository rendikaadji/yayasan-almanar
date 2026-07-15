<script setup>
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import BaseButton from '@/Components/BaseButton.vue';

const props = defineProps({
  galleries: {
    type: Array,
    required: true,
  },
});

const isModalOpen = ref(false);
const isEditing = ref(false);
const editingId = ref(null);

const form = useForm({
  _method: 'POST',
  album_name: '',
  cover_image: null,
  images: null, // multiple files array
});

const openCreateModal = () => {
  isEditing.value = false;
  editingId.value = null;
  form.reset();
  form.clearErrors();
  form._method = 'POST';
  isModalOpen.value = true;
};

const openEditModal = (gallery) => {
  isEditing.value = true;
  editingId.value = gallery.id;
  form.clearErrors();
  form._method = 'PUT';
  form.album_name = gallery.album_name;
  form.cover_image = null;
  form.images = null;
  isModalOpen.value = true;
};

const handleImagesInput = (event) => {
  form.images = Array.from(event.target.files);
};

const submitForm = () => {
  if (isEditing.value) {
    form.post(route('admin.galleries.update', editingId.value), {
      onSuccess: () => {
        isModalOpen.value = false;
        form.reset();
      },
    });
  } else {
    form.post(route('admin.galleries.store'), {
      onSuccess: () => {
        isModalOpen.value = false;
        form.reset();
      },
    });
  }
};

const deleteGallery = (id) => {
  if (confirm('Apakah Anda yakin ingin menghapus album ini beserta seluruh foto di dalamnya?')) {
    router.delete(route('admin.galleries.destroy', id));
  }
};
</script>

<template>
  <AdminLayout title="Kelola Galeri Album">
    <Head title="CMS - Galeri" />

    <div class="space-y-6 font-sans">
      <!-- Action Banner -->
      <div class="flex justify-between items-center bg-white p-4 rounded-lg border border-brand-slate shadow-sm">
        <span class="text-xs text-brand-navy/60">Kelompokkan foto/video kegiatan dakwah dan kemanusiaan ke dalam album.</span>
        <BaseButton @click="openCreateModal" variant="primary" class="!py-2 !px-4">
          Buat Album Baru
        </BaseButton>
      </div>

      <!-- Data Table -->
      <div class="bg-white rounded-lg border border-brand-slate shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-brand-slate text-left text-sm">
            <thead class="bg-brand-stone/50 font-utility text-xs text-brand-moss uppercase tracking-wider">
              <tr>
                <th scope="col" class="px-6 py-4">Cover Album</th>
                <th scope="col" class="px-6 py-4">Nama Album</th>
                <th scope="col" class="px-6 py-4">Jumlah Foto</th>
                <th scope="col" class="px-6 py-4">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-brand-slate">
              <tr v-for="gallery in galleries" :key="gallery.id" class="hover:bg-brand-stone/20 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap">
                  <img
                    v-if="gallery.cover_image"
                    :src="gallery.cover_image"
                    alt="Cover"
                    class="w-16 h-10 object-cover rounded border border-brand-slate"
                  />
                  <span v-else class="text-xs text-brand-navy/40 italic">No Cover</span>
                </td>
                <td class="px-6 py-4">
                  <div class="font-semibold text-brand-teal">{{ gallery.album_name }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-brand-navy/80">
                  {{ gallery.items?.length || 0 }} file
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-xs font-semibold space-x-3">
                  <button @click="openEditModal(gallery)" class="text-brand-sage hover:text-brand-teal transition-colors cursor-pointer">
                    Edit / Upload Foto
                  </button>
                  <button @click="deleteGallery(gallery.id)" class="text-red-700 hover:text-red-900 transition-colors cursor-pointer">
                    Hapus
                  </button>
                </td>
              </tr>
              <tr v-if="galleries.length === 0">
                <td colspan="4" class="px-6 py-12 text-center text-brand-navy/40 italic">
                  Belum ada album galeri. Klik "Buat Album Baru" untuk memulai.
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
            {{ isEditing ? 'Edit Album & Upload Foto' : 'Buat Album Galeri Baru' }}
          </h3>
          <button @click="isModalOpen = false" class="p-1 rounded hover:bg-brand-slate text-brand-navy focus:outline-none">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Form Body -->
        <form @submit.prevent="submitForm" class="flex-1 overflow-y-auto p-6 space-y-4 text-left">
          <!-- Album Name -->
          <div class="space-y-1">
            <label class="block text-xs font-utility font-bold text-brand-moss uppercase tracking-wider">Nama Album / Kegiatan</label>
            <input
              v-model="form.album_name"
              type="text"
              required
              placeholder="Contoh: Tabligh Akbar Ramadhan 1447H"
              class="w-full rounded border border-brand-slate px-3 py-2 text-sm focus:outline-none focus:border-brand-sage focus:ring-1 focus:ring-brand-sage"
            />
            <span v-if="form.errors.album_name" class="text-xs text-red-600 block">{{ form.errors.album_name }}</span>
          </div>

          <!-- Cover Image -->
          <div class="space-y-1">
            <label class="block text-xs font-utility font-bold text-brand-moss uppercase tracking-wider">Foto Cover Album</label>
            <input
              @input="form.cover_image = $event.target.files[0]"
              type="file"
              accept="image/*"
              class="w-full text-sm text-brand-navy/70 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-brand-sage/10 file:text-brand-sage hover:file:bg-brand-sage/20 cursor-pointer"
            />
            <span v-if="form.errors.cover_image" class="text-xs text-red-600 block">{{ form.errors.cover_image }}</span>
            <span v-if="isEditing" class="text-[10px] text-brand-navy/50 italic block">Biarkan kosong jika tidak ingin mengubah cover.</span>
          </div>

          <!-- Batch upload photos -->
          <div class="space-y-1">
            <label class="block text-xs font-utility font-bold text-brand-moss uppercase tracking-wider">
              {{ isEditing ? 'Tambah Foto ke Album' : 'Unggah Foto-foto Album (Bisa pilih banyak)' }}
            </label>
            <input
              @change="handleImagesInput"
              type="file"
              multiple
              accept="image/*"
              class="w-full text-sm text-brand-navy/70 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-brand-sage/10 file:text-brand-sage hover:file:bg-brand-sage/20 cursor-pointer"
            />
            <span v-if="form.errors.images" class="text-xs text-red-600 block">{{ form.errors.images }}</span>
          </div>
        </form>

        <!-- Footer -->
        <div class="px-6 py-4 border-t border-brand-slate bg-brand-stone/30 flex justify-end space-x-3">
          <BaseButton @click="isModalOpen = false" variant="secondary" class="!py-2 !px-4">
            Batal
          </BaseButton>
          <BaseButton @click="submitForm" variant="primary" class="!py-2 !px-4" :disabled="form.processing">
            {{ form.processing ? 'Menyimpan...' : (isEditing ? 'Simpan & Unggah' : 'Buat Album') }}
          </BaseButton>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
