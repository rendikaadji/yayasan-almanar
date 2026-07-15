<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import BaseButton from '@/Components/BaseButton.vue';

const props = defineProps({
  contactMessages: {
    type: Array,
    required: true,
  },
});

const isModalOpen = ref(false);
const activeMessage = ref(null);

const viewMessage = (message) => {
  activeMessage.value = message;
  isModalOpen.value = true;
  
  if (!message.is_read) {
    // Automatically mark as read when viewed
    router.patch(route('admin.contact-messages.read', message.id), {}, {
      preserveScroll: true,
      onSuccess: () => {
        // Update local object status to match server state
        message.is_read = true;
      }
    });
  }
};

const markAsReadDirect = (id) => {
  router.patch(route('admin.contact-messages.read', id), {}, { preserveScroll: true });
};

const deleteMessage = (id) => {
  if (confirm('Apakah Anda yakin ingin menghapus pesan masuk ini?')) {
    router.delete(route('admin.contact-messages.destroy', id), {
      onSuccess: () => {
        if (activeMessage.value && activeMessage.value.id === id) {
          isModalOpen.value = false;
        }
      }
    });
  }
};
</script>

<template>
  <AdminLayout title="Pesan Masuk / Inbox">
    <Head title="CMS - Inbox" />

    <div class="space-y-6 font-sans">
      <!-- Info Header -->
      <div class="bg-white p-4 rounded-lg border border-brand-slate shadow-sm flex items-center">
        <span class="text-xs text-brand-navy/60">Lihat kritik, saran, permohonan kerjasama, atau pertanyaan dari jamaah umum.</span>
      </div>

      <!-- Data Table -->
      <div class="bg-white rounded-lg border border-brand-slate shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-brand-slate text-left text-sm">
            <thead class="bg-brand-stone/50 font-utility text-xs text-brand-moss uppercase tracking-wider">
              <tr>
                <th scope="col" class="px-6 py-4">Status</th>
                <th scope="col" class="px-6 py-4">Pengirim</th>
                <th scope="col" class="px-6 py-4">Subjek/Pesan</th>
                <th scope="col" class="px-6 py-4">Tanggal Masuk</th>
                <th scope="col" class="px-6 py-4">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-brand-slate">
              <tr 
                v-for="msg in contactMessages" 
                :key="msg.id" 
                class="hover:bg-brand-stone/20 transition-colors"
                :class="[!msg.is_read ? 'font-semibold bg-brand-sage/5' : 'text-brand-navy/80']"
              >
                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    v-if="!msg.is_read"
                    class="px-2 py-0.5 rounded text-[9px] font-utility font-bold uppercase tracking-wider bg-red-50 text-red-700 border border-red-200 animate-pulse"
                  >
                    Baru
                  </span>
                  <span
                    v-else
                    class="px-2 py-0.5 rounded text-[9px] font-utility font-bold uppercase tracking-wider bg-brand-slate text-brand-moss border border-brand-moss/20"
                  >
                    Dibaca
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-brand-teal font-semibold">{{ msg.name }}</div>
                  <div class="text-xs text-brand-navy/55">{{ msg.email }}</div>
                </td>
                <td class="px-6 py-4">
                  <div class="line-clamp-1 max-w-sm text-sm" :class="[!msg.is_read ? 'text-brand-navy' : 'text-brand-navy/70']">
                    {{ msg.message }}
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-xs text-brand-navy/60 font-mono">
                  {{ new Date(msg.created_at).toLocaleString('id-ID', { dateStyle: 'short', timeStyle: 'short' }) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-xs font-semibold space-x-3">
                  <button @click="viewMessage(msg)" class="text-brand-sage hover:text-brand-teal transition-colors cursor-pointer">
                    Buka Pesan
                  </button>
                  <button 
                    v-if="!msg.is_read" 
                    @click="markAsReadDirect(msg.id)" 
                    class="text-brand-moss hover:text-brand-navy transition-colors cursor-pointer"
                  >
                    Tandai Dibaca
                  </button>
                  <button @click="deleteMessage(msg.id)" class="text-red-700 hover:text-red-900 transition-colors cursor-pointer">
                    Hapus
                  </button>
                </td>
              </tr>
              <tr v-if="contactMessages.length === 0">
                <td colspan="5" class="px-6 py-12 text-center text-brand-navy/40 italic">
                  Kotak masuk kosong. Belum ada pesan masuk dari form kontak.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Read-Only Message View Modal -->
    <div v-show="isModalOpen" class="fixed inset-0 z-50 overflow-y-auto bg-brand-teal/50 backdrop-blur-sm flex items-center justify-center p-4">
      <div v-if="activeMessage" class="bg-white rounded-xl max-w-lg w-full border border-brand-slate shadow-2xl flex flex-col">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-brand-slate flex justify-between items-center bg-brand-stone/30">
          <h3 class="font-display text-lg text-brand-teal uppercase tracking-wider font-bold">
            Detail Pesan Masuk
          </h3>
          <button @click="isModalOpen = false" class="p-1 rounded hover:bg-brand-slate text-brand-navy focus:outline-none">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Body -->
        <div class="p-6 space-y-6 text-left overflow-y-auto max-h-[70vh]">
          <!-- Meta Grid -->
          <div class="grid grid-cols-2 gap-4 bg-brand-slate/50 p-4 rounded border border-brand-moss/10 text-xs">
            <div>
              <span class="block font-utility font-bold text-brand-moss uppercase tracking-wider mb-1">Pengirim</span>
              <span class="font-semibold text-brand-teal text-sm">{{ activeMessage.name }}</span>
            </div>
            <div>
              <span class="block font-utility font-bold text-brand-moss uppercase tracking-wider mb-1">Tanggal Masuk</span>
              <span class="font-mono text-brand-navy/80">{{ new Date(activeMessage.created_at).toLocaleString('id-ID') }}</span>
            </div>
            <div>
              <span class="block font-utility font-bold text-brand-moss uppercase tracking-wider mb-1">Email</span>
              <a :href="`mailto:${activeMessage.email}`" class="text-brand-sage hover:underline">{{ activeMessage.email }}</a>
            </div>
            <div>
              <span class="block font-utility font-bold text-brand-moss uppercase tracking-wider mb-1">No. WhatsApp/Telepon</span>
              <a 
                v-if="activeMessage.phone" 
                :href="`https://wa.me/${activeMessage.phone.replace(/[^0-9]/g, '')}`" 
                target="_blank" 
                class="text-brand-sage hover:underline"
              >
                {{ activeMessage.phone }}
              </a>
              <span v-else class="text-brand-navy/40 italic">-</span>
            </div>
          </div>

          <!-- Message Content -->
          <div class="space-y-1">
            <span class="block text-xs font-utility font-bold text-brand-moss uppercase tracking-wider">Isi Pesan</span>
            <div class="bg-brand-stone/20 rounded p-4 border border-brand-slate text-sm text-brand-navy/90 whitespace-pre-wrap leading-relaxed">
              {{ activeMessage.message }}
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 border-t border-brand-slate bg-brand-stone/30 flex justify-between space-x-3">
          <BaseButton @click="deleteMessage(activeMessage.id)" variant="ghost" class="!text-red-700 hover:!bg-red-50 !py-2 !px-4">
            Hapus Pesan
          </BaseButton>
          <BaseButton @click="isModalOpen = false" variant="primary" class="!py-2 !px-4">
            Tutup
          </BaseButton>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
