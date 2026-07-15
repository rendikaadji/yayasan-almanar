<script setup>
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import BaseButton from '@/Components/BaseButton.vue';

const props = defineProps({
  programs: {
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
  category: 'Kajian',
  schedule: '',
  location: '',
  description: '',
  image: null,
});

const generateSlug = () => {
  form.slug = form.title
    .toLowerCase()
    .replace(/[^a-z0-9 -]/g, '')
    .replace(/\s+/g, '-')
    .replace(/-+/g, '-');
};

const openCreateModal = () => {
  isEditing.value = false;
  editingId.value = null;
  form.reset();
  form.clearErrors();
  form._method = 'POST';
  isModalOpen.value = true;
};

const openEditModal = (program) => {
  isEditing.value = true;
  editingId.value = program.id;
  form.clearErrors();
  form._method = 'PUT';
  form.title = program.title;
  form.slug = program.slug;
  form.category = program.category;
  form.schedule = program.schedule;
  form.location = program.location;
  form.description = program.description;
  form.image = null;
  isModalOpen.value = true;
};

const submitForm = () => {
  if (isEditing.value) {
    form.post(route('admin.programs.update', editingId.value), {
      onSuccess: () => {
        isModalOpen.value = false;
        form.reset();
      },
    });
  } else {
    form.post(route('admin.programs.store'), {
      onSuccess: () => {
        isModalOpen.value = false;
        form.reset();
      },
    });
  }
};

const deleteProgram = (id) => {
  if (confirm('Apakah Anda yakin ingin menghapus program ini?')) {
    router.delete(route('admin.programs.destroy', id));
  }
};
</script>

<template>
  <AdminLayout title="Kelola Program & Kegiatan">
    <Head title="CMS - Program" />

    <div class="space-y-6 font-sans">
      <!-- Action Banner -->
      <div class="flex justify-between items-center bg-white p-4 rounded-lg border border-brand-slate shadow-sm">
        <span class="text-xs text-brand-navy/60">Daftar kajian rutin, unit pendidikan TPQ/Tahfidz, dan aksi sosial.</span>
        <BaseButton @click="openCreateModal" variant="primary" class="!py-2 !px-4">
          Tambah Program
        </BaseButton>
      </div>

      <!-- Data Table -->
      <div class="bg-white rounded-lg border border-brand-slate shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-brand-slate text-left text-sm">
            <thead class="bg-brand-stone/50 font-utility text-xs text-brand-moss uppercase tracking-wider">
              <tr>
                <th scope="col" class="px-6 py-4">Gambar</th>
                <th scope="col" class="px-6 py-4">Nama Program</th>
                <th scope="col" class="px-6 py-4">Kategori</th>
                <th scope="col" class="px-6 py-4">Jadwal / Waktu</th>
                <th scope="col" class="px-6 py-4">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-brand-slate">
              <tr v-for="program in programs" :key="program.id" class="hover:bg-brand-stone/20 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap">
                  <img
                    v-if="program.image"
                    :src="program.image"
                    alt="Image"
                    class="w-16 h-10 object-cover rounded border border-brand-slate"
                  />
                  <span v-else class="text-xs text-brand-navy/40 italic">No Image</span>
                </td>
                <td class="px-6 py-4">
                  <div class="font-semibold text-brand-teal line-clamp-1">{{ program.title }}</div>
                  <div class="text-xs text-brand-navy/50 font-mono">{{ program.slug }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    class="px-2 py-0.5 rounded text-[10px] font-utility font-bold uppercase tracking-wider"
                    :class="[
                      program.category === 'Kajian' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : '',
                      program.category === 'Pendidikan' ? 'bg-amber-50 text-amber-700 border border-amber-200' : '',
                      program.category === 'Sosial' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : '',
                    ]"
                  >
                    {{ program.category }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-brand-navy/80">
                  {{ program.schedule }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-xs font-semibold space-x-3">
                  <button @click="openEditModal(program)" class="text-brand-sage hover:text-brand-teal transition-colors cursor-pointer">
                    Edit
                  </button>
                  <button @click="deleteProgram(program.id)" class="text-red-700 hover:text-red-900 transition-colors cursor-pointer">
                    Hapus
                  </button>
                </td>
              </tr>
              <tr v-if="programs.length === 0">
                <td colspan="5" class="px-6 py-12 text-center text-brand-navy/40 italic">
                  Belum ada program. Klik "Tambah Program" untuk memulai.
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
            {{ isEditing ? 'Edit Program & Kegiatan' : 'Tambah Program Baru' }}
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
            <label class="block text-xs font-utility font-bold text-brand-moss uppercase tracking-wider">Nama Program</label>
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
              Fungsi: Digunakan sebagai alamat URL unik halaman detail program ini di website (contoh: /program/<strong>slug-url-ini</strong>). Dibuat otomatis saat Anda mengisi Nama Program.
            </p>
          </div>

          <!-- Category -->
          <div class="space-y-1">
            <label class="block text-xs font-utility font-bold text-brand-moss uppercase tracking-wider">Kategori Program</label>
            <select
              v-model="form.category"
              class="w-full rounded border border-brand-slate px-3 py-2 text-sm focus:outline-none focus:border-brand-sage focus:ring-1 focus:ring-brand-sage"
            >
              <option value="Kajian">Kajian Dakwah</option>
              <option value="Pendidikan">Pendidikan Al-Qur'an</option>
              <option value="Sosial">Sosial Kemanusiaan</option>
            </select>
            <span v-if="form.errors.category" class="text-xs text-red-600 block">{{ form.errors.category }}</span>
          </div>

          <!-- Schedule -->
          <div class="space-y-1">
            <label class="block text-xs font-utility font-bold text-brand-moss uppercase tracking-wider">Jadwal / Waktu Pelaksanaan</label>
            <input
              v-model="form.schedule"
              type="text"
              placeholder="Contoh: Setiap Ahad, Jam 09.00 - Selesai"
              required
              class="w-full rounded border border-brand-slate px-3 py-2 text-sm focus:outline-none focus:border-brand-sage focus:ring-1 focus:ring-brand-sage"
            />
            <span v-if="form.errors.schedule" class="text-xs text-red-600 block">{{ form.errors.schedule }}</span>
          </div>

          <!-- Location -->
          <div class="space-y-1">
            <label class="block text-xs font-utility font-bold text-brand-moss uppercase tracking-wider">Lokasi Kegiatan</label>
            <input
              v-model="form.location"
              type="text"
              placeholder="Contoh: Ruang Utama Masjid Al Manar"
              required
              class="w-full rounded border border-brand-slate px-3 py-2 text-sm focus:outline-none focus:border-brand-sage focus:ring-1 focus:ring-brand-sage"
            />
            <span v-if="form.errors.location" class="text-xs text-red-600 block">{{ form.errors.location }}</span>
          </div>

          <!-- Description -->
          <div class="space-y-1">
            <label class="block text-xs font-utility font-bold text-brand-moss uppercase tracking-wider">Deskripsi Lengkap</label>
            <textarea
              v-model="form.description"
              rows="5"
              required
              class="w-full rounded border border-brand-slate px-3 py-2 text-sm focus:outline-none focus:border-brand-sage focus:ring-1 focus:ring-brand-sage"
            ></textarea>
            <span v-if="form.errors.description" class="text-xs text-red-600 block">{{ form.errors.description }}</span>
          </div>

          <!-- Image -->
          <div class="space-y-1">
            <label class="block text-xs font-utility font-bold text-brand-moss uppercase tracking-wider">Foto / Cover Program</label>
            <input
              @input="form.image = $event.target.files[0]"
              type="file"
              accept="image/*"
              class="w-full text-sm text-brand-navy/70 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-brand-sage/10 file:text-brand-sage hover:file:bg-brand-sage/20 cursor-pointer"
            />
            <span v-if="form.errors.image" class="text-xs text-red-600 block">{{ form.errors.image }}</span>
            <span v-if="isEditing" class="text-[10px] text-brand-navy/50 italic block">Biarkan kosong jika tidak ingin mengubah gambar.</span>
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
