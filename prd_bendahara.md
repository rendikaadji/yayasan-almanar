# PROMPT — Eksekusi Fitur Role Bendahara & Rekap Laporan Keuangan

### Aplikasi: Sistem Laporan Keuangan Yayasan Al Manar Pesanggrahan

---

## KONTEKS

Aplikasi laporan keuangan Yayasan Al Manar Pesanggrahan sudah berjalan. Sekarang perlu ditambahkan **modul multi-role untuk pencatatan laporan keuangan oleh masing-masing bendahara bidang**, ditutup dengan **satu role Rekap** yang menggabungkan seluruh laporan bidang menjadi satu laporan konsolidasi bulanan.

Struktur ini diambil dari pola laporan manual yang sudah berjalan (Excel/PDF), yang mencakup bidang-bidang berikut:

1. **Bendahara Yayasan** — laporan induk (pemasukan tetap, Idul Fitri, Idul Adha, tidak tetap; pengeluaran tetap, tidak tetap, pemindahbukuan antar-bidang)
2. **Bendahara DKM / BKM Masjid** — kas harian masjid (kotak infaq Jumat, infaq harian, infaq kajian subuh, QRIS, honor imam/khatib/marbot/satpam/ustadz, biaya operasional, mutasi bank DKI Syariah & BSI)
3. **Bendahara ZIS (Bidang Kesra)** — zakat, infaq, shodaqoh per bulan
4. **Bendahara Sarpras / PU (Pembangunan & Umum)** — biaya pemeliharaan, perbaikan, proyek fisik masjid
5. **Bendahara TPQ**
6. **Bendahara TKIT**
7. **Bendahara Umum / Rekap Yayasan** — role konsolidasi lintas-bidang

Jalankan tugas ini sebagai **eksekusi pengembangan fitur**, bukan sekadar rencana. Bangun langsung: skema data, halaman/role, form input, validasi, dan tampilan rekap.

---

## TUJUAN

Membuat sistem pencatatan laporan keuangan bulanan **multi-bendahara**, di mana:

- Setiap bendahara bidang mencatat pemasukan & pengeluaran bidangnya sendiri secara mandiri.
- Satu role terpisah (**Bendahara Umum / Rekap**) dapat melihat, memverifikasi, dan menggabungkan seluruh laporan bidang menjadi **Laporan Bulanan Konsolidasi Yayasan**, meniru format "Laporan Bulanan Bendahara Yayasan" yang sudah ada.

---

## DEFINISI ROLE & HAK AKSES

| Role                           | Lingkup Data                | Bisa Input                   | Bisa Lihat Bidang Lain     | Bisa Approve/Rekap                          |
| ------------------------------ | --------------------------- | ---------------------------- | -------------------------- | ------------------------------------------- |
| Bendahara Yayasan              | Data bidang Yayasan (induk) | ✅                           | ❌ (default)               | ❌                                          |
| Bendahara DKM/BKM              | Data kas masjid harian      | ✅                           | ❌                         | ❌                                          |
| Bendahara ZIS/Kesra            | Data ZIS                    | ✅                           | ❌                         | ❌                                          |
| Bendahara Sarpras/PU           | Data sarpras & proyek fisik | ✅                           | ❌                         | ❌                                          |
| Bendahara TPQ                  | Data TPQ                    | ✅                           | ❌                         | ❌                                          |
| Bendahara TKIT                 | Data TKIT                   | ✅                           | ❌                         | ❌                                          |
| **Bendahara Umum (Rekap)**     | Seluruh bidang              | ✅ (opsional, entri koreksi) | ✅ **penuh, semua bidang** | ✅ **approve & generate rekap konsolidasi** |
| Admin/Ketua Yayasan (opsional) | Seluruh bidang              | ❌                           | ✅ (read-only)             | ✅ (approve akhir/tanda tangan)             |

Catatan: setiap role bendahara bidang **hanya melihat & mengelola datanya sendiri**. Role Rekap adalah satu-satunya yang punya visibilitas lintas-bidang.

---

## SKEMA DATA (usulan)

### 1. `bidang` (master data)

- `id`, `nama_bidang` (Yayasan, DKM, ZIS, Sarpras/PU, TPQ, TKIT), `kode_bidang`

### 2. `user_bendahara`

- `id`, `nama`, `email/username`, `role` (bendahara_bidang / bendahara_umum / admin), `bidang_id` (nullable untuk role rekap/admin)

### 3. `periode_laporan`

- `id`, `bulan`, `tahun`, `bidang_id`, `status` (draft / diajukan / diverifikasi / dikonsolidasi), `saldo_awal`, `saldo_akhir` (dihitung otomatis)

### 4. `transaksi`

- `id`, `periode_laporan_id`, `bidang_id`, `tanggal`, `kategori` (pemasukan/pengeluaran), `sub_kategori` (tetap / tidak tetap / pemindahbukuan / idul_fitri / idul_adha, dst — mengikuti pola laporan existing per bidang), `uraian`, `jumlah`, `pic` (opsional), `bukti_transaksi` (upload file/foto, opsional), `dibuat_oleh`, `dibuat_pada`

### 5. `rekap_konsolidasi`

- `id`, `bulan`, `tahun`, `total_pemasukan_semua_bidang`, `total_pengeluaran_semua_bidang`, `saldo_akhir_gabungan`, `status` (draft / final / ditandatangani), `dibuat_oleh`, `disetujui_oleh`, `tanggal_disetujui`

> Field `sub_kategori` per bidang mengikuti pola nyata dari laporan yang sudah ada (contoh DKM: kotak infaq Jumat, infaq harian, infaq kajian subuh, QRIS, honor imam/khatib/marbot/satpam, dst). Simpan sebagai lookup table `sub_kategori` yang bisa dikonfigurasi per bidang, jangan hardcode, karena tiap bidang polanya berbeda.

---

## ALUR KERJA (WORKFLOW)

1. **Bendahara bidang** login → pilih/buka periode bulan berjalan → input transaksi pemasukan & pengeluaran satu per satu (atau import dari template Excel bila diperlukan) → sistem otomatis hitung saldo awal (carry-over dari saldo akhir bulan sebelumnya), total pemasukan, total pengeluaran, saldo akhir.
2. Bendahara bidang **mengajukan** laporan bulanan (status: draft → diajukan) setelah selesai input.
3. **Bendahara Umum (Rekap)** melihat dashboard berisi status laporan semua bidang (siapa yang sudah/belum mengajukan bulan ini).
4. Bendahara Umum membuka tiap laporan bidang, **memverifikasi** (bisa memberi catatan/menolak untuk revisi, atau menyetujui → status "diverifikasi").
5. Setelah semua bidang berstatus "diverifikasi" (atau minimal bidang yang wajib), Bendahara Umum menekan **"Generate Rekap Konsolidasi"** → sistem menjumlahkan seluruh pemasukan & pengeluaran semua bidang menjadi satu **Laporan Bulanan Konsolidasi**, mengikuti format existing (Pemasukan | Pengeluaran | Saldo bulan sebelumnya | Plus/Minus | Saldo akhir bulan).
6. Rekap konsolidasi dapat **diekspor ke Excel/PDF** dengan format serupa dokumen "LAPORAN BULANAN BENDAHARA YAYASAN" yang sudah ada (kop yayasan, tabel pemasukan/pengeluaran dua kolom, tanda tangan Ketua Yayasan & Bendahara).
7. (Opsional) Ketua Yayasan melakukan approval akhir/tanda tangan digital pada rekap konsolidasi.

---

## KEBUTUHAN TAMPILAN (UI)

### A. Dashboard Bendahara Bidang

- Ringkasan saldo bulan berjalan (saldo awal, total masuk, total keluar, saldo akhir)
- Form/tabel input transaksi (tambah, edit, hapus — hanya untuk status draft)
- Riwayat laporan bulan-bulan sebelumnya (read-only setelah diverifikasi)
- Tombol "Ajukan Laporan Bulan Ini"

### B. Dashboard Bendahara Umum (Rekap)

- Tabel status semua bidang per bulan (Yayasan, DKM, ZIS, Sarpras/PU, TPQ, TKIT) — kolom: status, total pemasukan, total pengeluaran, saldo akhir, aksi (lihat detail/verifikasi)
- Drill-down ke detail transaksi tiap bidang
- Tombol "Generate Rekap Konsolidasi" (aktif jika semua bidang sudah diverifikasi)
- Tampilan Laporan Konsolidasi final (format sama seperti laporan Yayasan existing) + tombol export Excel/PDF
- Grafik tren saldo & pemasukan/pengeluaran per bidang per bulan (opsional, nice-to-have)

---

## HAL PENTING YANG HARUS DIPERHATIKAN SAAT EKSEKUSI

1. **Saldo berjalan (carry-over)**: saldo akhir bulan N-1 harus otomatis menjadi saldo awal bulan N per bidang — jangan input manual, ambil dari data sistem.
2. **Pemindahbukuan antar-bidang** (contoh: dana dari DKM ditransfer ke Yayasan, atau dana TKIT ditransfer ke Yayasan) harus dicatat di kedua sisi (bidang pengirim = pengeluaran pemindahbukuan, bidang penerima = pemasukan tidak tetap) agar rekap konsolidasi tidak double count maupun kehilangan data. Sediakan field `bidang_tujuan`/`bidang_asal` khusus untuk transaksi jenis ini.
3. **Validasi**: cegah bidang mengajukan laporan dengan data kosong/nominal negatif tanpa keterangan; kunci (lock) transaksi setelah status "diverifikasi" agar tidak bisa diubah bendahara bidang lagi.
4. **Hak akses**: pastikan bendahara bidang benar-benar tidak bisa melihat data bidang lain (bukan sekadar disembunyikan di UI, tapi dibatasi di level query/API).
5. **Riwayat & audit trail**: setiap transaksi menyimpan siapa yang input dan kapan; setiap perubahan status laporan (diajukan/diverifikasi/ditolak) tercatat dengan waktu dan user.
6. **Ekspor**: format ekspor rekap konsolidasi mengikuti tata letak dokumen fisik yang sudah ada (kop surat yayasan, tabel dua kolom Pemasukan/Pengeluaran, baris Total, Plus/Minus, Saldo Akhir, serta blok tanda tangan Ketua Yayasan & Bendahara).
7. **Mobile-friendly**: bendahara sering input dari HP saat di lapangan/masjid — pastikan form input transaksi ringkas dan mudah dipakai di layar kecil.

---

## PRIORITAS EKSEKUSI (urutan pengerjaan)

1. Skema database (tabel di atas) + migrasi
2. Autentikasi & role-based access control (RBAC) per bidang
3. CRUD transaksi untuk role bendahara bidang + hitung saldo otomatis
4. Alur status laporan (draft → diajukan → diverifikasi)
5. Dashboard & fitur verifikasi untuk role Bendahara Umum/Rekap
6. Fitur "Generate Rekap Konsolidasi" (agregasi lintas bidang)
7. Export ke Excel/PDF dengan format sesuai laporan existing
8. (Opsional) Approval akhir Ketua Yayasan + tanda tangan digital
9. (Opsional) Dashboard grafik tren

**Mulai eksekusi dari poin 1, kerjakan secara bertahap, dan tampilkan hasil tiap tahap (skema, lalu UI, lalu integrasi) untuk direview sebelum lanjut ke tahap berikutnya.**
