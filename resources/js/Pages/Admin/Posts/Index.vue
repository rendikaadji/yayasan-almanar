<script setup>
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import BaseButton from '@/Components/BaseButton.vue';

const props = defineProps({
  posts: {
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
  content: '',
  published_at: '',
  thumbnail: null,
});

const generateSlug = () => {
  form.slug = form.title
    .toLowerCase()
    .replace(/[^a-z0-9 -]/g, '') // remove invalid chars
    .replace(/\s+/g, '-') // collapse whitespace and replace by -
    .replace(/-+/g, '-'); // collapse dashes
};

const openCreateModal = () => {
  isEditing.value = false;
  editingId.value = null;
  form.reset();
  form.clearErrors();
  form._method = 'POST';
  isModalOpen.value = true;
};

const openEditModal = (post) => {
  isEditing.value = true;
  editingId.value = post.id;
  form.clearErrors();
  form._method = 'PUT';
  form.title = post.title;
  form.slug = post.slug;
  form.content = post.content;
  form.published_at = post.published_at ? post.published_at.substring(0, 10) : '';
  form.thumbnail = null; // file is optional on update
  isModalOpen.value = true;
};

const submitForm = () => {
  if (isEditing.value) {
    // Submit as POST with _method = PUT for file upload support in Laravel PUT requests
    form.post(route('admin.posts.update', editingId.value), {
      onSuccess: () => {
        isModalOpen.value = false;
        form.reset();
      },
    });
  } else {
    form.post(route('admin.posts.store'), {
      onSuccess: () => {
        isModalOpen.value = false;
        form.reset();
      },
    });
  }
};

const deletePost = (id) => {
  if (confirm('Apakah Anda yakin ingin menghapus berita ini?')) {
    router.delete(route('admin.posts.destroy', id));
  }
};
</script>

<template>
  <AdminLayout title="Kelola Berita & Artikel">
    <Head title="CMS - Berita" />

    <div class="space-y-6 font-sans">
      <!-- Action Banner -->
      <div class="flex justify-between items-center bg-white p-4 rounded-lg border border-brand-slate shadow-sm">
        <span class="text-xs text-brand-navy/60">Daftar berita kajian, info masjid, dan dokumentasi sosial.</span>
        <BaseButton @click="openCreateModal" variant="primary" class="!py-2 !px-4">
          Tulis Berita
        </BaseButton>
      </div>

      <!-- Data Table -->
      <div class="bg-white rounded-lg border border-brand-slate shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-brand-slate text-left text-sm">
            <thead class="bg-brand-stone/50 font-utility text-xs text-brand-moss uppercase tracking-wider">
              <tr>
                <th scope="col" class="px-6 py-4">Thumbnail</th>
                <th scope="col" class="px-6 py-4">Judul</th>
                <th scope="col" class="px-6 py-4">Penulis</th>
                <th scope="col" class="px-6 py-4">Status Rilis</th>
                <th scope="col" class="px-6 py-4">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-brand-slate">
              <tr v-for="post in posts" :key="post.id" class="hover:bg-brand-stone/20 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap">
                  <img
                    v-if="post.thumbnail"
                    :src="post.thumbnail"
                    alt="Thumbnail"
                    class="w-16 h-10 object-cover rounded border border-brand-slate"
                  />
                  <span v-else class="text-xs text-brand-navy/40 italic">No Image</span>
                </td>
                <td class="px-6 py-4">
                  <div class="font-semibold text-brand-teal line-clamp-1">{{ post.title }}</div>
                  <div class="text-xs text-brand-navy/50 font-mono">{{ post.slug }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-brand-navy/80">
                  {{ post.author?.name || 'Admin' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    v-if="post.published_at"
                    class="px-2 py-0.5 rounded text-[10px] font-utility font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200"
                  >
                    Diterbitkan
                  </span>
                  <span
                    v-else
                    class="px-2 py-0.5 rounded text-[10px] font-utility font-bold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200"
                  >
                    Draft / Arsip
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-xs font-semibold space-x-3">
                  <button @click="openEditModal(post)" class="text-brand-sage hover:text-brand-teal transition-colors cursor-pointer">
                    Edit
                  </button>
                  <button @click="deletePost(post.id)" class="text-red-700 hover:text-red-900 transition-colors cursor-pointer">
                    Hapus
                  </button>
                </td>
              </tr>
              <tr v-if="posts.length === 0">
                <td colspan="5" class="px-6 py-12 text-center text-brand-navy/40 italic">
                  Belum ada berita yang ditulis. Klik "Tulis Berita" untuk memulai.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal overlay -->
    <div v-show="isModalOpen" class="fixed inset-0 z-50 overflow-y-auto bg-brand-teal/50 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="bg-white rounded-xl max-w-2xl w-full border border-brand-slate shadow-2xl flex flex-col max-h-[90vh]">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-brand-slate flex justify-between items-center bg-brand-stone/30">
          <h3 class="font-display text-lg text-brand-teal uppercase tracking-wider font-bold">
            {{ isEditing ? 'Edit Artikel Berita' : 'Tulis Artikel Baru' }}
          </h3>
          <button @click="isModalOpen = false" class="p-1 rounded hover:bg-brand-slate text-brand-navy focus:outline-none">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Modal Body Form -->
        <form @submit.prevent="submitForm" class="flex-1 overflow-y-auto p-6 space-y-4 text-left">
          <!-- Title -->
          <div class="space-y-1">
            <label class="block text-xs font-utility font-bold text-brand-moss uppercase tracking-wider">Judul Berita</label>
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
            <label class="block text-xs font-utility font-bold text-brand-moss uppercase tracking-wider">Slug URL (Dibuat otomatis)</label>
            <input
              v-model="form.slug"
              type="text"
              required
              class="w-full rounded border border-brand-slate px-3 py-2 text-sm bg-brand-stone/40 font-mono text-brand-navy/70 focus:outline-none"
            />
            <span v-if="form.errors.slug" class="text-xs text-red-600 block">{{ form.errors.slug }}</span>
          </div>

          <!-- Content -->
          <div class="space-y-1">
            <label class="block text-xs font-utility font-bold text-brand-moss uppercase tracking-wider">Isi Berita / Konten</label>
            <textarea
              v-model="form.content"
              rows="6"
              required
              class="w-full rounded border border-brand-slate px-3 py-2 text-sm focus:outline-none focus:border-brand-sage focus:ring-1 focus:ring-brand-sage"
            ></textarea>
            <span v-if="form.errors.content" class="text-xs text-red-600 block">{{ form.errors.content }}</span>
          </div>

          <!-- Thumbnail Image -->
          <div class="space-y-1">
            <label class="block text-xs font-utility font-bold text-brand-moss uppercase tracking-wider">Gambar Thumbnail</label>
            <input
              @input="form.thumbnail = $event.target.files[0]"
              type="file"
              accept="image/*"
              class="w-full text-sm text-brand-navy/70 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-brand-sage/10 file:text-brand-sage hover:file:bg-brand-sage/20 cursor-pointer"
            />
            <span v-if="form.errors.thumbnail" class="text-xs text-red-600 block">{{ form.errors.thumbnail }}</span>
            <span v-if="isEditing" class="text-[10px] text-brand-navy/50 italic block">Biarkan kosong jika tidak ingin mengubah gambar thumbnail.</span>
          </div>

          <!-- Published At Date -->
          <div class="space-y-1">
            <label class="block text-xs font-utility font-bold text-brand-moss uppercase tracking-wider">Tanggal Terbit (Kosongkan jika draft)</label>
            <input
              v-model="form.published_at"
              type="date"
              class="w-full rounded border border-brand-slate px-3 py-2 text-sm focus:outline-none focus:border-brand-sage focus:ring-1 focus:ring-brand-sage"
            />
            <span v-if="form.errors.published_at" class="text-xs text-red-600 block">{{ form.errors.published_at }}</span>
          </div>
        </form>

        <!-- Modal Footer -->
        <div class="px-6 py-4 border-t border-brand-slate bg-brand-stone/30 flex justify-end space-x-3">
          <BaseButton @click="isModalOpen = false" variant="secondary" class="!py-2 !px-4">
            Batal
          </BaseButton>
          <BaseButton @click="submitForm" variant="primary" class="!py-2 !px-4" :disabled="form.processing">
            {{ form.processing ? 'Menyimpan...' : (isEditing ? 'Simpan Perubahan' : 'Terbitkan Berita') }}
          </BaseButton>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
