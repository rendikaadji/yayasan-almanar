<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import BaseButton from '@/Components/BaseButton.vue';

const props = defineProps({
  rekap: {
    type: Object,
    required: true,
  },
  bidangData: {
    type: Array,
    required: true,
  },
  pemasukan: {
    type: Array,
    required: true,
  },
  pengeluaran: {
    type: Array,
    required: true,
  },
  selectedBulan: {
    type: Number,
    required: true,
  },
  selectedTahun: {
    type: Number,
    required: true,
  },
});

const months = [
  { val: 1, name: 'Januari' },
  { val: 2, name: 'Februari' },
  { val: 3, name: 'Maret' },
  { val: 4, name: 'April' },
  { val: 5, name: 'Mei' },
  { val: 6, name: 'Juni' },
  { val: 7, name: 'Juli' },
  { val: 8, name: 'Agustus' },
  { val: 9, name: 'September' },
  { val: 10, name: 'Oktober' },
  { val: 11, name: 'November' },
  { val: 12, name: 'Desember' },
];

const getMonthName = (val) => {
  return months.find(m => m.val === val)?.name || val;
};

const formatCurrency = (val) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(val);
};

// Summary totals
const totalSaldoAwal = computed(() => props.bidangData.reduce((acc, b) => acc + parseFloat(b.saldo_awal), 0));
const totalPemasukan = computed(() => props.bidangData.reduce((acc, b) => acc + parseFloat(b.total_pemasukan), 0));
const totalPengeluaran = computed(() => props.bidangData.reduce((acc, b) => acc + parseFloat(b.total_pengeluaran), 0));
const totalSaldoAkhir = computed(() => props.bidangData.reduce((acc, b) => acc + parseFloat(b.saldo_akhir), 0));

const handlePrint = () => {
  window.print();
};
</script>

<template>
  <AdminLayout title="Laporan Konsolidasi Bulanan">
    <Head title="Rekap Konsolidasi" />

    <div class="space-y-6 font-sans print:space-y-0 print:p-0">
      <!-- Back Link & Action Bar (Hidden during printing) -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 print:hidden">
        <div>
          <Link :href="route('admin.finance.rekap.index')" class="text-xs font-semibold text-brand-sage hover:text-brand-teal transition-colors flex items-center">
            &larr; Kembali ke Dashboard Verifikasi
          </Link>
          <h3 class="text-lg font-bold text-brand-teal mt-1">
            Rekap Laporan Keuangan Konsolidasi
          </h3>
        </div>

        <div class="flex items-center gap-3">
          <!-- Excel Export -->
          <a 
            :href="route('admin.finance.rekap.export.excel', { bulan: selectedBulan, tahun: selectedTahun })"
            class="inline-flex items-center px-4 py-2 border border-brand-sage text-brand-sage font-semibold text-xs rounded hover:bg-brand-sage hover:text-brand-stone transition-all duration-300 cursor-pointer"
          >
            Ekspor Excel (.xlsx)
          </a>
          
          <!-- Server PDF Download -->
          <a 
            :href="route('admin.finance.rekap.export.pdf', { bulan: selectedBulan, tahun: selectedTahun })"
            class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-emerald-600 text-brand-stone font-semibold text-xs rounded hover:bg-emerald-700 transition-all duration-300 cursor-pointer"
          >
            Unduh PDF Resmi
          </a>

          <!-- Print Preview -->
          <BaseButton 
            @click="handlePrint" 
            variant="primary" 
            class="!py-2 !px-4 cursor-pointer"
          >
            Cetak Cetak Layar
          </BaseButton>
        </div>
      </div>

      <!-- Report Sheet Container -->
      <div class="bg-white rounded-xl border border-brand-slate shadow-sm p-8 max-w-5xl mx-auto print:border-0 print:shadow-none print:p-0 print:max-w-none">
        
        <!-- YAYASAN KOP SURAT (Letterhead) -->
        <div class="text-center border-b-4 border-double border-brand-teal pb-4 mb-8">
          <h1 class="font-display text-3xl font-bold text-brand-teal uppercase tracking-widest leading-none">
            YAYASAN AL MANAR PESANGGRAHAN
          </h1>
          <h2 class="text-sm font-semibold text-brand-sage uppercase tracking-wider mt-1">
            Laporan Keuangan Konsolidasi Bulanan
          </h2>
          <div class="text-xs font-medium text-brand-navy/70 mt-1 italic">
            Bulan: {{ getMonthName(selectedBulan) }} {{ selectedTahun }}
          </div>
          <p class="text-[10px] text-brand-navy/50 mt-1 max-w-2xl mx-auto">
            Jl. Swadarma Raya No. 1, RT 01 / RW 08, Kel. Pesanggrahan, Kec. Pesanggrahan, Jakarta Selatan
          </p>
        </div>

        <!-- SECTION I: SUMMARY -->
        <div class="space-y-3">
          <h3 class="font-semibold text-sm text-brand-teal uppercase tracking-wider border-b border-brand-slate pb-1">
            I. RINGKASAN NERACA LAPORAN BIDANG
          </h3>
          
          <table class="w-full text-left border-collapse text-xs border border-brand-slate">
            <thead>
              <tr class="bg-brand-teal text-brand-stone font-bold uppercase tracking-wide">
                <th class="py-2.5 px-3 border border-brand-slate text-center w-12">No</th>
                <th class="py-2.5 px-3 border border-brand-slate">Nama Bidang Laporan</th>
                <th class="py-2.5 px-3 border border-brand-slate text-right w-36">Saldo Awal</th>
                <th class="py-2.5 px-3 border border-brand-slate text-right w-36">Pemasukan (+)</th>
                <th class="py-2.5 px-3 border border-brand-slate text-right w-36">Pengeluaran (-)</th>
                <th class="py-2.5 px-3 border border-brand-slate text-right w-36">Saldo Akhir</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(b, idx) in bidangData" :key="b.nama_bidang" class="hover:bg-brand-stone/20 transition-colors">
                <td class="py-2 px-3 border border-brand-slate text-center text-brand-navy/60">{{ idx + 1 }}</td>
                <td class="py-2 px-3 border border-brand-slate font-medium text-brand-navy">{{ b.nama_bidang }}</td>
                <td class="py-2 px-3 border border-brand-slate text-right font-medium">{{ formatCurrency(b.saldo_awal) }}</td>
                <td class="py-2 px-3 border border-brand-slate text-right font-medium text-emerald-600">{{ formatCurrency(b.total_pemasukan) }}</td>
                <td class="py-2 px-3 border border-brand-slate text-right font-medium text-red-600">{{ formatCurrency(b.total_pengeluaran) }}</td>
                <td class="py-2 px-3 border border-brand-slate text-right font-bold text-brand-teal">{{ formatCurrency(b.saldo_akhir) }}</td>
              </tr>
              <!-- Totals row -->
              <tr class="bg-brand-slate/40 font-bold border-t-2 border-brand-teal">
                <td colspan="2" class="py-2.5 px-3 border border-brand-slate text-center">TOTAL GABUNGAN</td>
                <td class="py-2.5 px-3 border border-brand-slate text-right">{{ formatCurrency(totalSaldoAwal) }}</td>
                <td class="py-2.5 px-3 border border-brand-slate text-right text-emerald-700">{{ formatCurrency(totalPemasukan) }}</td>
                <td class="py-2.5 px-3 border border-brand-slate text-right text-red-700">{{ formatCurrency(totalPengeluaran) }}</td>
                <td class="py-2.5 px-3 border border-brand-slate text-right text-brand-teal">{{ formatCurrency(totalSaldoAkhir) }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="print:page-break-before-always my-8"></div>

        <!-- SECTION II: DETAILED TWO COLUMNS -->
        <div class="space-y-4">
          <h3 class="font-semibold text-sm text-brand-teal uppercase tracking-wider border-b border-brand-slate pb-1">
            II. DETAIL KAS MASUK DAN KAS KELUAR GABUNGAN
          </h3>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 print:grid-cols-2">
            <!-- Pemasukan Side (Left) -->
            <div class="space-y-2 border border-brand-slate rounded-lg overflow-hidden">
              <div class="bg-brand-sage text-brand-stone px-4 py-2 font-bold text-center text-xs uppercase tracking-wide">
                Pemasukan Konsolidasi
              </div>
              <div class="p-3">
                <table class="w-full text-left border-collapse text-[10px]">
                  <thead>
                    <tr class="border-b border-brand-slate text-brand-teal font-bold uppercase">
                      <th class="py-1.5 w-24">Bidang</th>
                      <th class="py-1.5">Kategori / Uraian</th>
                      <th class="py-1.5 text-right w-24">Jumlah</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-brand-slate/30">
                    <tr v-for="tx in pemasukan" :key="tx.id">
                      <td class="py-1 px-0.5 text-brand-navy/60 font-semibold truncate max-w-[90px]">{{ tx.bidang?.nama_bidang }}</td>
                      <td class="py-1 px-0.5 text-brand-navy">
                        <span class="font-semibold text-brand-teal mr-1">[{{ tx.sub_kategori?.nama_sub_kategori }}]</span>
                        {{ tx.uraian }}
                      </td>
                      <td class="py-1 px-0.5 text-right text-emerald-600 font-bold whitespace-nowrap">{{ formatCurrency(tx.jumlah) }}</td>
                    </tr>
                    <tr v-if="pemasukan.length === 0">
                      <td colspan="3" class="py-4 text-center italic text-brand-navy/40">Tidak ada transaksi masuk</td>
                    </tr>
                    <tr class="font-bold border-t border-brand-slate/60 text-xs bg-brand-stone/40">
                      <td colspan="2" class="py-2">TOTAL PEMASUKAN</td>
                      <td class="py-2 text-right text-emerald-700">{{ formatCurrency(totalPemasukan) }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Pengeluaran Side (Right) -->
            <div class="space-y-2 border border-brand-slate rounded-lg overflow-hidden">
              <div class="bg-red-800 text-brand-stone px-4 py-2 font-bold text-center text-xs uppercase tracking-wide">
                Pengeluaran Konsolidasi
              </div>
              <div class="p-3">
                <table class="w-full text-left border-collapse text-[10px]">
                  <thead>
                    <tr class="border-b border-brand-slate text-brand-teal font-bold uppercase">
                      <th class="py-1.5 w-24">Bidang</th>
                      <th class="py-1.5">Kategori / Uraian</th>
                      <th class="py-1.5 text-right w-24">Jumlah</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-brand-slate/30">
                    <tr v-for="tx in pengeluaran" :key="tx.id">
                      <td class="py-1 px-0.5 text-brand-navy/60 font-semibold truncate max-w-[90px]">{{ tx.bidang?.nama_bidang }}</td>
                      <td class="py-1 px-0.5 text-brand-navy">
                        <span class="font-semibold text-brand-teal mr-1">[{{ tx.sub_kategori?.nama_sub_kategori }}]</span>
                        {{ tx.uraian }}
                      </td>
                      <td class="py-1 px-0.5 text-right text-red-600 font-bold whitespace-nowrap">{{ formatCurrency(tx.jumlah) }}</td>
                    </tr>
                    <tr v-if="pengeluaran.length === 0">
                      <td colspan="3" class="py-4 text-center italic text-brand-navy/40">Tidak ada transaksi keluar</td>
                    </tr>
                    <tr class="font-bold border-t border-brand-slate/60 text-xs bg-brand-stone/40">
                      <td colspan="2" class="py-2">TOTAL PENGELUARAN</td>
                      <td class="py-2 text-right text-red-700">{{ formatCurrency(totalPengeluaran) }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- SIGNATURE PLACEHOLDERS -->
        <div class="mt-12 pt-6">
          <table class="w-full text-xs border-collapse border-none">
            <tbody>
              <tr class="border-none text-center">
                <td class="pb-16 w-1/2">
                  Mengetahui,<br>
                  <strong class="text-brand-teal font-bold block mt-1">Ketua Yayasan Al Manar</strong>
                </td>
                <td class="pb-16 w-1/2">
                  Jakarta, {{ new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) }}<br>
                  <strong class="text-brand-teal font-bold block mt-1">Bendahara Umum / Rekap</strong>
                </td>
              </tr>
              <tr class="border-none text-center font-bold">
                <td>
                  ( ___________________________ )
                </td>
                <td>
                  ( ___________________________ )
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<style>
/* Custom Print Styles for Print-Preview compatibility */
@media print {
  body {
    background-color: #ffffff !important;
    color: #000000 !important;
  }
  .min-h-screen {
    min-h-screen: 0 !important;
    height: auto !important;
  }
  aside, header {
    display: none !important;
  }
  main {
    padding: 0 !important;
    margin: 0 !important;
  }
}
</style>
