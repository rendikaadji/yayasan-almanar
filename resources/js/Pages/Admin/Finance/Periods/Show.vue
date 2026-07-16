<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import BaseButton from '@/Components/BaseButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
  periode: {
    type: Object,
    required: true,
  },
  transactions: {
    type: Array,
    required: true,
  },
  subKategoris: {
    type: Array,
    required: true,
  },
  otherBidangs: {
    type: Array,
    required: true,
  },
  summary: {
    type: Object,
    required: true,
  },
  riwayat: {
    type: Array,
    required: true,
  },
});

const page = usePage();
const currentUser = computed(() => page.props.auth.user);

// Modals state
const isTxModalOpen = ref(false);
const txModalMode = ref('create'); // 'create' or 'edit'
const editingTxId = ref(null);

const isRejectModalOpen = ref(false);
const rejectForm = useForm({
  catatan: '',
});

const isReceiptModalOpen = ref(false);
const activeReceiptUrl = ref('');

// Form for transaction CRUD
const txForm = useForm({
  periode_laporan_id: props.periode.id,
  tanggal: new Date().toISOString().split('T')[0],
  kategori: 'pemasukan',
  sub_kategori_id: '',
  uraian: '',
  jumlah: '',
  pic: '',
  bukti_transaksi: null,
  bukti_transaksi_file: null, // used for editing file upload
  bidang_tujuan_id: '',
});

// Filter sub-categories based on selected category in form
const filteredSubKategoris = computed(() => {
  return props.subKategoris.filter(sk => sk.kategori === txForm.kategori);
});

// Check if the selected subcategory is a transfer out
const isTransferOut = computed(() => {
  if (txForm.kategori !== 'pengeluaran' || !txForm.sub_kategori_id) return false;
  const sub = props.subKategoris.find(sk => sk.id === parseInt(txForm.sub_kategori_id));
  return sub && (sub.nama_sub_kategori.toLowerCase().includes('pemindahbukuan') || sub.nama_sub_kategori.toLowerCase().includes('transfer'));
});

// Access & Lock Permissions
const isEditable = computed(() => {
  const status = props.periode.status;
  // Completely locked
  if (status === 'diverifikasi' || status === 'dikonsolidasi') {
    return false;
  }
  // Draft: editable by bidang treasurer
  if (status === 'draft' && currentUser.value.role === 'bendahara_bidang') {
    return true;
  }
  // Diajukan: editable/correctable by general treasurer or admin
  if (status === 'diajukan' && (currentUser.value.role === 'bendahara_umum' || currentUser.value.role === 'admin')) {
    return true;
  }
  return false;
});

const canSubmit = computed(() => {
  return props.periode.status === 'draft' && 
         currentUser.value.role === 'bendahara_bidang' && 
         (props.transactions.length > 0 || parseFloat(props.periode.saldo_awal) > 0);
});

const canVerify = computed(() => {
  return props.periode.status === 'diajukan' && 
         (currentUser.value.role === 'bendahara_umum' || currentUser.value.role === 'admin');
});

// Open modal functions
const openCreateTxModal = () => {
  txModalMode.value = 'create';
  editingTxId.value = null;
  txForm.reset();
  txForm.clearErrors();
  txForm.periode_laporan_id = props.periode.id;
  txForm.tanggal = new Date().toISOString().split('T')[0];
  txForm.kategori = 'pemasukan';
  txForm.sub_kategori_id = filteredSubKategoris.value[0]?.id || '';
  isTxModalOpen.value = true;
};

const openEditTxModal = (tx) => {
  txModalMode.value = 'edit';
  editingTxId.value = tx.id;
  txForm.clearErrors();
  txForm.periode_laporan_id = props.periode.id;
  txForm.tanggal = tx.tanggal.split('T')[0];
  txForm.kategori = tx.kategori;
  txForm.sub_kategori_id = tx.sub_kategori_id;
  txForm.uraian = tx.uraian;
  txForm.jumlah = tx.jumlah;
  txForm.pic = tx.pic || '';
  txForm.bidang_tujuan_id = tx.bidang_tujuan_id || '';
  txForm.bukti_transaksi = tx.bukti_transaksi;
  txForm.bukti_transaksi_file = null;
  isTxModalOpen.value = true;
};

// Handle file input upload
const onFileChange = (e) => {
  const file = e.target.files[0];
  if (file) {
    if (txModalMode.value === 'create') {
      txForm.bukti_transaksi = file;
    } else {
      txForm.bukti_transaksi_file = file;
    }
  }
};

const submitTxForm = () => {
  if (txModalMode.value === 'create') {
    txForm.post(route('admin.finance.transactions.store'), {
      onSuccess: () => {
        isTxModalOpen.value = false;
        txForm.reset();
      },
    });
  } else {
    // Laravel handles multi-part updates (file upload) best via POST method overriding PATCH
    // Since Inertia handles post with method override automatically when using form data:
    txForm.post(route('admin.finance.transactions.update', editingTxId.value), {
      _method: 'PATCH',
      onSuccess: () => {
        isTxModalOpen.value = false;
        txForm.reset();
      },
    });
  }
};

const deleteForm = useForm({});
const deleteTx = (id) => {
  if (confirm('Apakah Anda yakin ingin menghapus transaksi ini?')) {
    deleteForm.delete(route('admin.finance.transactions.destroy', id));
  }
};

// Report Status actions
const submitPeriodForm = useForm({});
const submitPeriod = () => {
  if (confirm('Apakah Anda yakin ingin mengajukan laporan keuangan bulan ini? Laporan akan dikunci untuk verifikasi.')) {
    submitPeriodForm.post(route('admin.finance.periods.submit', props.periode.id));
  }
};

const verifyPeriodForm = useForm({});
const verifyPeriod = () => {
  if (confirm('Apakah Anda yakin menyetujui dan memverifikasi laporan ini?')) {
    verifyPeriodForm.post(route('admin.finance.rekap.verifikasi', props.periode.id));
  }
};

const rejectPeriod = () => {
  rejectForm.post(route('admin.finance.rekap.tolak', props.periode.id), {
    onSuccess: () => {
      isRejectModalOpen.value = false;
      rejectForm.reset();
    }
  });
};

const openReceipt = (url) => {
  activeReceiptUrl.value = url;
  isReceiptModalOpen.value = true;
};

// Currency format helpers
const formatCurrency = (val) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(val);
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  });
};

const translateStatus = (status) => {
  switch (status) {
    case 'draft': return 'Draft';
    case 'diajukan': return 'Diajukan (Menunggu Review)';
    case 'diverifikasi': return 'Diverifikasi';
    case 'dikonsolidasi': return 'Dikonsolidasi';
    default: return status;
  }
};

const getHistoryBadgeClass = (status) => {
  switch (status) {
    case 'diajukan': return 'bg-blue-100 text-blue-800';
    case 'ditolak': return 'bg-red-100 text-red-800';
    case 'diverifikasi': return 'bg-emerald-100 text-emerald-800';
    case 'dikoreksi': return 'bg-amber-100 text-amber-800 border border-amber-300';
    default: return 'bg-gray-100 text-gray-800';
  }
};
</script>

<template>
  <AdminLayout :title="'Buku Kas — ' + (periode.bidang?.nama_bidang || 'Detail Laporan')">
    <Head title="Detail Buku Kas" />

    <div class="space-y-6 font-sans">
      <!-- Back Link & Action Bar -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <Link :href="route('admin.finance.periods.index')" class="text-xs font-semibold text-brand-sage hover:text-brand-teal transition-colors flex items-center">
            &larr; Kembali ke Daftar Periode
          </Link>
          <div class="flex items-center gap-3 mt-1">
            <h3 class="text-xl font-bold text-brand-teal uppercase tracking-wide">
              Periode: {{ props.periode.bulan }} / {{ props.periode.tahun }}
            </h3>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-brand-slate text-brand-teal border border-brand-slate">
              Status: {{ translateStatus(periode.status) }}
            </span>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center flex-wrap gap-3">
          <!-- Bidang Treasurer Actions -->
          <BaseButton 
            v-if="currentUser.role === 'bendahara_bidang' && canSubmit" 
            @click="submitPeriod" 
            variant="primary" 
            class="!py-2 !px-4 cursor-pointer"
          >
            Ajukan Laporan Bulan Ini
          </BaseButton>

          <!-- General Treasurer Actions -->
          <template v-if="canVerify">
            <BaseButton 
              @click="verifyPeriod" 
              variant="primary" 
              class="!py-2 !px-4 bg-emerald-600 hover:bg-emerald-700 cursor-pointer"
            >
              Setujui &amp; Verifikasi
            </BaseButton>
            <BaseButton 
              @click="isRejectModalOpen = true" 
              variant="danger" 
              class="!py-2 !px-4 cursor-pointer"
            >
              Tolak &amp; Revisi
            </BaseButton>
          </template>

          <BaseButton 
            v-if="isEditable" 
            @click="openCreateTxModal" 
            variant="primary" 
            class="!py-2 !px-4 cursor-pointer"
          >
            Tambah Transaksi
          </BaseButton>
        </div>
      </div>

      <!-- Financial Metrics Summary -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Saldo Awal -->
        <div class="bg-white rounded-xl border border-brand-slate p-5 shadow-sm flex flex-col justify-between">
          <span class="text-xs font-bold text-brand-moss uppercase tracking-wider">Saldo Awal Kas</span>
          <span class="text-2xl font-bold text-brand-teal mt-2">{{ formatCurrency(summary.saldo_awal) }}</span>
        </div>

        <!-- Pemasukan -->
        <div class="bg-white rounded-xl border border-brand-slate p-5 shadow-sm flex flex-col justify-between">
          <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Total Pemasukan (+)</span>
          <span class="text-2xl font-bold text-emerald-700 mt-2">{{ formatCurrency(summary.total_pemasukan) }}</span>
        </div>

        <!-- Pengeluaran -->
        <div class="bg-white rounded-xl border border-brand-slate p-5 shadow-sm flex flex-col justify-between">
          <span class="text-xs font-bold text-red-600 uppercase tracking-wider">Total Pengeluaran (-)</span>
          <span class="text-2xl font-bold text-red-700 mt-2">{{ formatCurrency(summary.total_pengeluaran) }}</span>
        </div>

        <!-- Saldo Akhir -->
        <div class="bg-white rounded-xl border border-brand-sage/20 bg-brand-teal/5 p-5 shadow-sm flex flex-col justify-between">
          <span class="text-xs font-bold text-brand-teal uppercase tracking-wider">Saldo Akhir Berjalan</span>
          <span class="text-2xl font-bold text-brand-teal mt-2">{{ formatCurrency(summary.saldo_akhir) }}</span>
        </div>
      </div>

      <!-- Corrections Warning banner if general treasurer is modifying submitted report -->
      <div 
        v-if="periode.status === 'diajukan' && (currentUser.role === 'bendahara_umum' || currentUser.role === 'admin')" 
        class="bg-amber-50 border border-amber-200 text-amber-950 rounded-xl p-4 text-xs font-sans leading-relaxed"
      >
        <strong>Mode Koreksi Bendahara Umum Aktif:</strong> Laporan ini sedang diajukan. Sebagai Bendahara Umum, Anda dapat langsung menambah, mengubah, atau menghapus transaksi untuk melakukan koreksi kecil tanpa harus melakukan penolakan penuh. Setiap tindakan koreksi akan dicatat pada log audit.
      </div>

      <!-- Transaction List Table -->
      <div class="bg-white rounded-xl border border-brand-slate shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse text-sm">
            <thead>
              <tr class="bg-brand-slate/40 text-brand-teal border-b border-brand-slate font-semibold text-xs uppercase tracking-wide">
                <th class="py-3.5 px-4 text-center w-12">No</th>
                <th class="py-3.5 px-4 w-28">Tanggal</th>
                <th class="py-3.5 px-4 w-28">Jenis</th>
                <th class="py-3.5 px-4 w-32">Kategori</th>
                <th class="py-3.5 px-4">Uraian / Deskripsi</th>
                <th class="py-3.5 px-4 w-32 text-right">Jumlah (IDR)</th>
                <th class="py-3.5 px-4 w-24 text-center">Bukti</th>
                <th class="py-3.5 px-4 text-right w-36" v-if="isEditable">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-brand-slate/40">
              <tr v-for="(tx, index) in transactions" :key="tx.id" class="hover:bg-brand-stone/30 transition-colors">
                <td class="py-3 px-4 text-center text-brand-navy/60">{{ index + 1 }}</td>
                <td class="py-3 px-4 text-brand-navy/85 font-medium">{{ formatDate(tx.tanggal) }}</td>
                <td class="py-3 px-4">
                  <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold"
                    :class="[
                      tx.kategori === 'pemasukan' 
                        ? 'bg-emerald-50 text-emerald-700 border border-emerald-150' 
                        : 'bg-red-50 text-red-700 border border-red-150'
                    ]"
                  >
                    {{ tx.kategori === 'pemasukan' ? 'Masuk' : 'Keluar' }}
                  </span>
                </td>
                <td class="py-3 px-4 text-brand-navy/75 font-semibold text-xs">
                  {{ tx.sub_kategori?.nama_sub_kategori }}
                </td>
                <td class="py-3 px-4">
                  <div class="text-brand-navy font-medium">{{ tx.uraian }}</div>
                  <div v-if="tx.pic" class="text-[10px] text-brand-navy/55">PIC: {{ tx.pic }}</div>
                  <div v-if="tx.bidang_asal" class="text-[10px] text-brand-sage font-medium">Asal: {{ tx.bidang_asal.nama_bidang }}</div>
                  <div v-if="tx.bidang_tujuan" class="text-[10px] text-red-600 font-medium">Tujuan Transfer: {{ tx.bidang_tujuan.nama_bidang }}</div>
                </td>
                <td class="py-3 px-4 text-right font-bold" :class="[tx.kategori === 'pemasukan' ? 'text-emerald-600' : 'text-red-600']">
                  {{ tx.kategori === 'pemasukan' ? '+' : '-' }} {{ formatCurrency(tx.jumlah) }}
                </td>
                <td class="py-3 px-4 text-center">
                  <button 
                    v-if="tx.bukti_transaksi" 
                    @click="openReceipt(tx.bukti_transaksi)" 
                    class="text-brand-sage hover:text-brand-teal inline-flex items-center justify-center p-1 rounded hover:bg-brand-slate transition-colors cursor-pointer"
                    title="Lihat Bukti Foto"
                  >
                    <!-- Receipt Outline icon -->
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                  </button>
                  <span v-else class="text-xs text-brand-navy/35 italic">Tidak ada</span>
                </td>
                <td class="py-3 px-4 text-right space-x-3" v-if="isEditable">
                  <button @click="openEditTxModal(tx)" class="text-brand-sage hover:text-brand-teal font-semibold text-xs cursor-pointer">
                    Edit
                  </button>
                  <button @click="deleteTx(tx.id)" class="text-red-600 hover:text-red-800 font-semibold text-xs cursor-pointer">
                    Hapus
                  </button>
                </td>
              </tr>
              <tr v-if="transactions.length === 0">
                <td colspan="8" class="py-12 text-center text-brand-navy/55 italic">
                  Belum ada transaksi pada periode ini.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Verification Audit Trail Timeline Section -->
      <div class="bg-white rounded-xl border border-brand-slate p-6 shadow-sm">
        <h4 class="font-display text-lg text-brand-teal uppercase tracking-wide border-b border-brand-slate pb-3 mb-4">
          Histori &amp; Log Audit Laporan
        </h4>
        <div class="space-y-6 font-sans">
          <div v-for="h in riwayat" :key="h.id" class="flex gap-4">
            <!-- Timeline dot -->
            <div class="flex flex-col items-center">
              <span class="w-3.5 h-3.5 rounded-full mt-1.5 border-2 flex items-center justify-center"
                :class="[
                  h.status === 'diajukan' ? 'bg-blue-500 border-blue-200' :
                  h.status === 'diverifikasi' ? 'bg-emerald-500 border-emerald-200' :
                  h.status === 'dikoreksi' ? 'bg-amber-500 border-amber-200' :
                  'bg-red-500 border-red-200'
                ]"
              ></span>
              <div class="w-0.5 bg-brand-slate flex-1 my-1"></div>
            </div>
            
            <!-- Audit Details -->
            <div class="flex-1 pb-4">
              <div class="flex items-center gap-3">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold" :class="getHistoryBadgeClass(h.status)">
                  {{ h.status === 'diajukan' ? 'Diajukan' :
                     h.status === 'diverifikasi' ? 'Diverifikasi' :
                     h.status === 'dikoreksi' ? 'Koreksi Kas' : 'Revisi/Ditolak' }}
                </span>
                <span class="text-xs text-brand-navy/60">{{ formatDate(h.created_at) }}</span>
                <span class="text-xs text-brand-navy/65">oleh {{ h.user?.name }}</span>
              </div>
              <p class="text-sm font-medium text-brand-navy mt-1.5 border border-brand-slate bg-brand-stone/30 p-2.5 rounded-lg leading-relaxed">
                {{ h.catatan || 'Tidak ada catatan.' }}
              </p>
            </div>
          </div>
          
          <div v-if="riwayat.length === 0" class="text-sm text-brand-navy/60 italic text-center py-4">
            Belum ada histori status untuk laporan ini. Laporan masih berstatus Draft.
          </div>
        </div>
      </div>
    </div>

    <!-- 1. Transaction Add/Edit Modal -->
    <div v-show="isTxModalOpen" class="fixed inset-0 z-50 overflow-y-auto bg-brand-teal/50 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="bg-white rounded-xl max-w-lg w-full shadow-2xl border border-brand-slate overflow-hidden transform transition-all font-sans text-sm">
        <div class="bg-brand-teal text-brand-stone px-6 py-4 flex items-center justify-between border-b border-brand-sage/20">
          <h4 class="font-display text-lg uppercase tracking-wider text-brand-gold">
            {{ txModalMode === 'create' ? 'Input Transaksi Baru' : 'Edit Transaksi' }}
          </h4>
          <button @click="isTxModalOpen = false" class="p-1 rounded hover:bg-brand-sage/30 text-brand-stone focus:outline-none cursor-pointer">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <form @submit.prevent="submitTxForm">
          <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
            <div class="grid grid-cols-2 gap-4">
              <!-- Type (Pemasukan/Pengeluaran) -->
              <div>
                <InputLabel for="tx_kategori" value="Jenis Transaksi" />
                <select
                  id="tx_kategori"
                  v-model="txForm.kategori"
                  class="mt-1 block w-full rounded-md border-brand-slate text-brand-navy shadow-sm focus:border-brand-sage focus:ring focus:ring-brand-sage/20 text-sm"
                  :disabled="txModalMode === 'edit'"
                  required
                >
                  <option value="pemasukan">Pemasukan</option>
                  <option value="pengeluaran">Pengeluaran</option>
                </select>
                <InputError :message="txForm.errors.kategori" class="mt-1" />
              </div>

              <!-- Sub Category -->
              <div>
                <InputLabel for="tx_sub_kategori" value="Sub-Kategori" />
                <select
                  id="tx_sub_kategori"
                  v-model="txForm.sub_kategori_id"
                  class="mt-1 block w-full rounded-md border-brand-slate text-brand-navy shadow-sm focus:border-brand-sage focus:ring focus:ring-brand-sage/20 text-sm"
                  required
                >
                  <option v-for="sk in filteredSubKategoris" :key="sk.id" :value="sk.id">
                    {{ sk.nama_sub_kategori }}
                  </option>
                </select>
                <InputError :message="txForm.errors.sub_kategori_id" class="mt-1" />
              </div>
            </div>

            <!-- Inter-Bidang Transfer Options -->
            <div v-show="isTransferOut">
              <div class="bg-red-50 border border-red-200 text-red-900 rounded-lg p-3 text-xs mb-3 leading-relaxed">
                <strong>Pemindahbukuan Antar-Bidang:</strong> Memilih bidang tujuan di bawah ini akan secara otomatis membuat transaksi pemasukan yang setara pada bidang penerima tersebut, memastikan kas seimbang.
              </div>
              <InputLabel for="tx_bidang_tujuan" value="Tujuan Transfer (Bidang)" />
              <select
                id="tx_bidang_tujuan"
                v-model="txForm.bidang_tujuan_id"
                class="mt-1 block w-full rounded-md border-brand-slate text-brand-navy shadow-sm focus:border-brand-sage focus:ring focus:ring-brand-sage/20 text-sm"
                :required="isTransferOut"
              >
                <option value="">-- Pilih Bidang Tujuan --</option>
                <option v-for="b in otherBidangs" :key="b.id" :value="b.id">
                  {{ b.nama_bidang }}
                </option>
              </select>
              <InputError :message="txForm.errors.bidang_tujuan_id" class="mt-1" />
            </div>

            <div class="grid grid-cols-2 gap-4">
              <!-- Date -->
              <div>
                <InputLabel for="tx_tanggal" value="Tanggal Transaksi" />
                <TextInput
                  id="tx_tanggal"
                  type="date"
                  v-model="txForm.tanggal"
                  class="mt-1 block w-full text-sm"
                  required
                />
                <InputError :message="txForm.errors.tanggal" class="mt-1" />
              </div>

              <!-- Amount -->
              <div>
                <InputLabel for="tx_jumlah" value="Jumlah Nominal (IDR)" />
                <TextInput
                  id="tx_jumlah"
                  type="number"
                  v-model="txForm.jumlah"
                  class="mt-1 block w-full text-sm"
                  placeholder="Contoh: 150000"
                  step="0.01"
                  min="0.01"
                  required
                />
                <InputError :message="txForm.errors.jumlah" class="mt-1" />
              </div>
            </div>

            <!-- Description -->
            <div>
              <InputLabel for="tx_uraian" value="Uraian / Keterangan Transaksi" />
              <textarea
                id="tx_uraian"
                v-model="txForm.uraian"
                rows="2"
                class="mt-1 block w-full rounded-md border-brand-slate text-brand-navy shadow-sm focus:border-brand-sage focus:ring focus:ring-brand-sage/20 text-sm font-sans"
                placeholder="Tulis uraian pemakaian atau pemasukan dana..."
                required
              ></textarea>
              <InputError :message="txForm.errors.uraian" class="mt-1" />
            </div>

            <!-- PIC -->
            <div>
              <InputLabel for="tx_pic" value="PIC (Penanggung Jawab / Penerima)" />
              <TextInput
                id="tx_pic"
                type="text"
                v-model="txForm.pic"
                class="mt-1 block w-full text-sm"
                placeholder="Masukkan nama pelaksana/penanggung jawab kegiatan..."
              />
              <InputError :message="txForm.errors.pic" class="mt-1" />
            </div>

            <!-- Receipt Photo Upload -->
            <div>
              <InputLabel for="tx_bukti" value="Upload Nota / Bukti Transaksi (Foto/Gambar)" />
              <input
                id="tx_bukti"
                type="file"
                accept="image/*"
                @change="onFileChange"
                class="mt-2 block w-full text-xs text-brand-navy/60 font-sans file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-brand-sage/10 file:text-brand-sage hover:file:bg-brand-sage/25 cursor-pointer file:cursor-pointer"
              />
              <InputError :message="txForm.errors.bukti_transaksi" class="mt-1" />
              
              <!-- Display preview or link if editing -->
              <div v-if="txForm.bukti_transaksi && typeof txForm.bukti_transaksi === 'string'" class="mt-3">
                <span class="text-xs text-brand-navy/60 block mb-1">Nota yang sudah ada:</span>
                <img :src="txForm.bukti_transaksi" class="h-20 w-auto rounded border border-brand-slate shadow-sm" alt="Bukti Transaksi" />
              </div>
            </div>
          </div>

          <div class="bg-brand-stone/50 px-6 py-4 flex items-center justify-end gap-3 border-t border-brand-slate font-sans">
            <BaseButton @click="isTxModalOpen = false" type="button" variant="secondary" class="!py-2 !px-4 cursor-pointer">
              Batal
            </BaseButton>
            <BaseButton type="submit" variant="primary" class="!py-2 !px-4 cursor-pointer" :disabled="txForm.processing">
              Simpan Transaksi
            </BaseButton>
          </div>
        </form>
      </div>
    </div>

    <!-- 2. Reject Report Feedback Modal -->
    <div v-show="isRejectModalOpen" class="fixed inset-0 z-50 overflow-y-auto bg-brand-teal/50 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="bg-white rounded-xl max-w-md w-full shadow-2xl border border-brand-slate overflow-hidden transform transition-all font-sans text-sm">
        <div class="bg-red-800 text-brand-stone px-6 py-4 flex items-center justify-between border-b border-red-900/20">
          <h4 class="font-display text-lg uppercase tracking-wider text-brand-gold">
            Tolak Laporan &amp; Minta Revisi
          </h4>
          <button @click="isRejectModalOpen = false" class="p-1 rounded hover:bg-red-900/30 text-brand-stone focus:outline-none cursor-pointer">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <form @submit.prevent="rejectPeriod">
          <div class="p-6 space-y-4">
            <div class="bg-red-50 border border-red-200 text-red-950 rounded-lg p-3 text-xs leading-relaxed">
              Silakan tuliskan catatan koreksi atau alasan penolakan di bawah ini. Status laporan akan kembali menjadi <strong>Draft</strong>, dan bendahara bidang terkait akan diberitahu agar merevisi laporannya sesuai catatan Anda.
            </div>
            <div>
              <InputLabel for="reject_catatan" value="Catatan Alasan Penolakan / Revisi" />
              <textarea
                id="reject_catatan"
                v-model="rejectForm.catatan"
                rows="4"
                class="mt-1 block w-full rounded-md border-brand-slate text-brand-navy shadow-sm focus:border-red-500 focus:ring focus:ring-red-200 text-sm font-sans"
                placeholder="Tuliskan alasan penolakan secara jelas. Contoh: Uraian kas masuk DKM tanggal 12 tidak memiliki lampiran kuitansi..."
                required
              ></textarea>
              <InputError :message="rejectForm.errors.catatan" class="mt-1" />
            </div>
          </div>

          <div class="bg-brand-stone/50 px-6 py-4 flex items-center justify-end gap-3 border-t border-brand-slate font-sans">
            <BaseButton @click="isRejectModalOpen = false" type="button" variant="secondary" class="!py-2 !px-4 cursor-pointer">
              Batal
            </BaseButton>
            <BaseButton type="submit" variant="danger" class="!py-2 !px-4 bg-red-700 hover:bg-red-800 cursor-pointer" :disabled="rejectForm.processing">
              Tolak &amp; Kembalikan
            </BaseButton>
          </div>
        </form>
      </div>
    </div>

    <!-- 3. Receipt Image Lightbox Modal -->
    <div v-show="isReceiptModalOpen" @click="isReceiptModalOpen = false" class="fixed inset-0 z-50 bg-brand-teal/80 backdrop-blur-md flex items-center justify-center p-4">
      <div class="relative max-w-3xl w-full flex flex-col items-center">
        <button @click="isReceiptModalOpen = false" class="absolute -top-12 right-0 p-2 text-white bg-brand-teal rounded-full hover:bg-brand-sage transition-colors focus:outline-none cursor-pointer">
          <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
        <img :src="activeReceiptUrl" class="max-h-[80vh] max-w-full rounded-lg shadow-2xl border-4 border-white object-contain" alt="Kuitansi Penerimaan" @click.stop />
      </div>
    </div>
  </AdminLayout>
</template>
