# PRD — Company Profile Al Manar

> Dokumen ini adalah acuan tunggal (single source of truth) untuk vibe coding.
> Setiap kali prompting ke Claude Code / Cursor, lampirkan file ini agar hasil
> generate konsisten dari sesi ke sesi.

---

## 1. Overview Proyek

| Item         | Detail                                                                                    |
| ------------ | ----------------------------------------------------------------------------------------- |
| Nama Proyek  | Al Manar — Company Profile Masjid / Organisasi Dakwah                                     |
| Jenis Produk | Website company profile (public-facing) + panel admin (CMS ringan)                        |
| Tech Stack   | Laravel 11 (backend/API) + Inertia.js + Vue 3 (Composition API) + Tailwind CSS            |
| Database     | MySQL                                                                                     |
| Target Rilis | Website statis-informatif dulu (Fase 1), fitur dinamis (donasi, berita) menyusul (Fase 2) |

**Catatan stack:** Kombinasi Laravel + Inertia + Vue dipilih karena tidak butuh REST API terpisah,
routing tetap di Laravel, tapi UI-nya reaktif ala SPA dengan Vue. Ini pattern paling stabil untuk
"vibe coding" karena satu Claude Code session bisa paham backend & frontend sekaligus tanpa context-switch API contract.

---

## 2. Tujuan Proyek

1. Memberi identitas digital yang elegan & profesional untuk Al Manar sebagai lembaga dakwah.
2. Menjadi pusat informasi program, kegiatan, dan berita bagi jamaah/masyarakat.
3. Membuka kanal donasi yang transparan dan mudah diakses.
4. SEO-friendly agar mudah ditemukan (nama lembaga, lokasi, program unggulan).
5. Tidak terlihat seperti "template AI" — harus punya identitas visual sendiri.

---

## 3. Target Pengguna

- **Jamaah / masyarakat umum** — cari info kegiatan, jadwal, kontak, mau berdonasi.
- **Donatur** — butuh kepercayaan (transparansi laporan, testimoni, legalitas lembaga).
- **Pengurus/Admin Al Manar** — input berita, kegiatan, update laporan donasi lewat panel admin.
- **Calon santri/peserta program (jika ada TPQ/kajian rutin)** — cari info pendaftaran.

---

## 4. Struktur Halaman & Fitur (Public Site)

### 4.1 Home

- Hero section: identitas Al Manar, tagline, 1 CTA utama (Donasi / Info Kegiatan).
- Ringkasan profil singkat + angka pencapaian (jumlah jamaah, program aktif, tahun berdiri — opsional, jangan format "01/02/03" generik kalau tidak representasi urutan nyata).
- Highlight program/kegiatan terbaru (3–4 kartu).
- Cuplikan berita terbaru (3 kartu, link ke halaman Berita).
- Testimoni singkat (carousel/slider ringan).
- CTA Donasi (banner section).

### 4.2 Profil / Tentang Kami

- Sejarah singkat berdirinya Al Manar.
- Visi & Misi.
- Struktur pengurus/kepengurusan (foto + jabatan).
- Legalitas lembaga (jika ada — no. SK yayasan, dsb).

### 4.3 Program & Kegiatan

- Listing program dakwah (kajian rutin, TPQ, tahfidz, sosial-kemanusiaan, dll).
- Detail per program: jadwal, lokasi, pengisi/ustadz, deskripsi.
- Bisa difilter berdasarkan kategori (Kajian / Pendidikan / Sosial).

### 4.4 Berita / Blog

- Listing artikel berita/kegiatan (dengan pagination).
- Halaman detail artikel (judul, tanggal, penulis, konten rich text, gambar).
- Kategori & tag (opsional Fase 2).

### 4.5 Galeri

- Grid foto/video kegiatan, dikelompokkan per album/kegiatan.
- Lightbox saat foto diklik.

### 4.6 Testimoni

- Listing testimoni jamaah/donatur (nama, foto opsional, isi testimoni).
- Bisa ditampilkan sebagai grid atau slider.

### 4.7 Donasi

- Listing program donasi aktif (judul program, target dana, dana terkumpul, progress bar).
- Detail program donasi + metode pembayaran (transfer bank / QRIS — tampilkan info statis dulu di Fase 1, integrasi payment gateway di Fase 2).
- Laporan penggunaan dana (transparansi) — bisa berupa daftar/table sederhana.

> Catatan: "PPDB" hanya relevan jika Al Manar punya unit pendidikan formal (TPQ/sekolah). Jika ya,
> tambahkan halaman **Pendaftaran** dengan form pendaftaran calon santri. Jika belum ada kepastian,
> sediakan section ini sebagai placeholder yang mudah diaktifkan nanti.

### 4.8 Kontak

- Alamat, peta lokasi (embed Google Maps), nomor telepon/WA, email.
- Form kontak/pesan sederhana (kirim ke email admin atau simpan ke DB).
- Link media sosial.

---

## 5. Panel Admin (CMS Ringan)

Akses terbatas (login admin), untuk mengelola konten tanpa perlu ubah kode:

- CRUD Berita/Blog
- CRUD Program & Kegiatan
- CRUD Galeri (upload foto/video, kelompokkan album)
- CRUD Testimoni
- CRUD Program Donasi + update jumlah dana terkumpul
- Kelola Pesan masuk dari form kontak
- Kelola data Struktur Pengurus

**Auth:** Laravel Breeze/Fortify (session-based, cukup untuk kebutuhan admin internal, tidak perlu API token terpisah karena satu aplikasi Inertia).

---

## 6. Data Model Ringkas (ERD Konsep)

```
users (admin)
├── id, name, email, password, role

posts (berita)
├── id, title, slug, content, thumbnail, published_at, author_id

programs (program & kegiatan)
├── id, title, slug, category, schedule, location, description, image

galleries
├── id, album_name, cover_image
gallery_items
├── id, gallery_id, file_path, type(image/video)

testimonials
├── id, name, photo, message, is_featured

donation_programs
├── id, title, slug, description, target_amount, collected_amount, deadline, image
donation_reports (laporan penggunaan dana)
├── id, donation_program_id, description, amount, date

contact_messages
├── id, name, email, phone, message, is_read

organization_structure
├── id, name, position, photo, order
```

---

## 7. Non-Functional Requirements

- **Visual:** Elegant, clean, light theme — nuansa Islami yang calming, BUKAN template generik (lihat `UI-UX-PROMPT.md`).
- **Responsive:** Mobile-first, wajib rapi di HP (mayoritas traffic jamaah dari mobile).
- **SEO:** Meta title/description per halaman, sitemap.xml, semantic HTML, Open Graph tags untuk share ke WhatsApp/media sosial.
- **Performance:** Lazy load gambar, optimasi asset, Lighthouse score minimal 85+ untuk performance & SEO.
- **Accessibility:** Kontras warna cukup, alt text gambar, keyboard-navigable.
- **Konsistensi:** Semua komponen Vue reusable (Button, Card, Section, Container) — jangan duplikat style per halaman.

---

## 8. Deliverables

1. `PRD.md` (dokumen ini)
2. `UI-UX-PROMPT.md` — prompt arahan desain untuk dipakai bareng PRD ini
3. `EXECUTION-PROMPT.md` — prompt eksekusi step-by-step untuk Claude Code/Cursor
4. Source code Laravel + Inertia + Vue sesuai struktur di atas

---

## 9. Catatan Konten yang Perlu Dilengkapi Rendi

Sebelum eksekusi coding, siapkan/isi dulu (biar AI tidak mengarang data):

- [ ] Nama resmi lengkap lembaga & tagline
- [ ] Sejarah singkat, visi-misi (draft teks)
- [ ] Daftar program/kegiatan aktual + jadwal
- [ ] Foto-foto asli (hero, kegiatan, pengurus)
- [ ] Info rekening/QRIS donasi
- [ ] Alamat, nomor kontak, media sosial
- [ ] Warna/identitas brand jika sudah ada logo Al Manar (kirim logo jika ada, biar palet warna diturunkan dari situ)
