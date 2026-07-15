<script setup>
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import BaseButton from '@/Components/BaseButton.vue';

const props = defineProps({
  structureItems: {
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
  position: '',
  order: 0,
  photo: null,
});

const openCreateModal = () => {
  isEditing.value = false;
  editingId.value = null;
  form.reset();
  form.clearErrors();
  form.order = props.structureItems.length > 0 ? Math.max(...props.structureItems.map(i => i.order)) + 1 : 0;
  form._method = 'POST';
  isModalOpen.value = true;
};

const openEditModal = (member) => {
  isEditing.value = true;
  editingId.value = member.id;
  form.clearErrors();
  form._method = 'PUT';
  form.name = member.name;
  form.position = member.position;
  form.order = member.order;
  form.photo = null;
  isModalOpen.value = true;
};

const submitForm = () => {
  if (isEditing.value) {
    form.post(route('admin.organization-structures.update', editingId.value), {
      onSuccess: () => {
        isModalOpen.value = false;
        form.reset();
      },
    });
  } else {
    form.post(route('admin.organization-structures.store'), {
      onSuccess: () => {
        isModalOpen.value = false;
        form.reset();
      },
    });
  }
};

const deleteMember = (id) => {
  if (confirm('Apakah Anda yakin ingin menghapus data pengurus ini?')) {
    router.delete(route('admin.organization-structures.destroy', id));
  }
};
</script>

<template>
  <AdminLayout title="Kelola Struktur Pengurus">
    <Head title="CMS - Pengurus" />

    <div class="space-y-6 font-sans">
      <!-- Action Banner -->
      <div class="flex justify-between items-center bg-white p-4 rounded-lg border border-brand-slate shadow-sm">
        <span class="text-xs text-brand-navy/60">Kelola jajaran DKM, penasihat, sekretaris, dan tim kepengurusan Al Manar.</span>
        <BaseButton @click="openCreateModal" variant="primary" class="!py-2 !px-4">
          Tambah Pengurus
        </BaseButton>
      </div>

      <!-- Data Table -->
      <div class="bg-white rounded-lg border border-brand-slate shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-brand-slate text-left text-sm">
            <thead class="bg-brand-stone/50 font-utility text-xs text-brand-moss uppercase tracking-wider">
              <tr>
                <th scope="col" class="px-6 py-4">Foto</th>
                <th scope="col" class="px-6 py-4">Nama Lengkap</th>
                <th scope="col" class="px-6 py-4">Jabatan</th>
                <th scope="col" class="px-6 py-4">Urutan (Sorting)</th>
                <th scope="col" class="px-6 py-4">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-brand-slate">
              <tr v-for="member in structureItems" :key="member.id" class="hover:bg-brand-stone/20 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap">
                  <img
                    v-if="member.photo"
                    :src="member.photo"
                    alt="Photo"
                    class="w-10 h-10 object-cover rounded-full border border-brand-slate"
                  />
                  <span v-else class="text-xs text-brand-navy/40 italic">No Photo</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap font-semibold text-brand-teal">
                  {{ member.name }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-brand-navy/80">
                  {{ member.position }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-brand-navy/60 font-mono text-xs">
                  {{ member.order }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-xs font-semibold space-x-3">
                  <button @click="openEditModal(member)" class="text-brand-sage hover:text-brand-teal transition-colors cursor-pointer">
                    Edit
                  </button>
                  <button @click="deleteMember(member.id)" class="text-red-700 hover:text-red-900 transition-colors cursor-pointer">
                    Hapus
                  </button>
                </td>
              </tr>
              <tr v-if="structureItems.length === 0">
                <td colspan="5" class="px-6 py-12 text-center text-brand-navy/40 italic">
                  Belum ada data pengurus. Klik "Tambah Pengurus" untuk menambahkan.
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
            {{ isEditing ? 'Edit Data Pengurus' : 'Tambah Anggota Pengurus Baru' }}
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
            <label class="block text-xs font-utility font-bold text-brand-moss uppercase tracking-wider">Nama Lengkap & Gelar</label>
            <input
              v-model="form.name"
              type="text"
              required
              placeholder="Contoh: H. Ahmad Fauzi, Lc."
              class="w-full rounded border border-brand-slate px-3 py-2 text-sm focus:outline-none focus:border-brand-sage focus:ring-1 focus:ring-brand-sage"
            />
            <span v-if="form.errors.name" class="text-xs text-red-600 block">{{ form.errors.name }}</span>
          </div>

          <!-- Position -->
          <div class="space-y-1">
            <label class="block text-xs font-utility font-bold text-brand-moss uppercase tracking-wider">Jabatan / Posisi</label>
            <input
              v-model="form.position"
              type="text"
              required
              placeholder="Contoh: Ketua DKM Al Manar"
              class="w-full rounded border border-brand-slate px-3 py-2 text-sm focus:outline-none focus:border-brand-sage focus:ring-1 focus:ring-brand-sage"
            />
            <span v-if="form.errors.position" class="text-xs text-red-600 block">{{ form.errors.position }}</span>
          </div>

          <!-- Order -->
          <div class="space-y-1">
            <label class="block text-xs font-utility font-bold text-brand-moss uppercase tracking-wider">Urutan Tampilan (Angka Kecil Diatas)</label>
            <input
              v-model="form.order"
              type="number"
              min="0"
              required
              class="w-full rounded border border-brand-slate px-3 py-2 text-sm focus:outline-none focus:border-brand-sage focus:ring-1 focus:ring-brand-sage"
            />
            <span v-if="form.errors.order" class="text-xs text-red-600 block">{{ form.errors.order }}</span>
          </div>

          <!-- Photo -->
          <div class="space-y-1">
            <label class="block text-xs font-utility font-bold text-brand-moss uppercase tracking-wider">Foto Resmi (Resolusi Kotak/Potret)</label>
            <input
              @input="form.photo = $event.target.files[0]"
              type="file"
              accept="image/*"
              class="w-full text-sm text-brand-navy/70 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-brand-sage/10 file:text-brand-sage hover:file:bg-brand-sage/20 cursor-pointer"
            />
            <span v-if="form.errors.photo" class="text-xs text-red-600 block">{{ form.errors.photo }}</span>
            <span v-if="isEditing" class="text-[10px] text-brand-navy/50 italic block">Biarkan kosong jika tidak ingin mengubah foto.</span>
          </div>
        </form>

        <!-- Footer -->
        <div class="px-6 py-4 border-t border-brand-slate bg-brand-stone/30 flex justify-end space-x-3">
          <BaseButton @click="isModalOpen = false" variant="secondary" class="!py-2 !px-4">
            Batal
          </BaseButton>
          <BaseButton @click="submitForm" variant="primary" class="!py-2 !px-4" :disabled="form.processing">
            {{ form.processing ? 'Menyimpan...' : (isEditing ? 'Simpan Perubahan' : 'Tambah Anggota') }}
          </BaseButton>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
