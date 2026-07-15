# UI/UX Design Prompt — Al Manar Company Profile

> Tempel prompt ini ke Claude Code / Cursor **bersama** `PRD.md` sebelum minta generate komponen visual apapun.
> Tujuan: hasil desain premium, elegant, khas Al Manar — bukan "AI slop" (template generik yang keliatan hasil AI).

---

## PROMPT (copy dari sini ke bawah)

Kamu berperan sebagai **design lead** di studio kecil yang dikenal karena setiap klien punya identitas
visual yang tidak bisa disamakan dengan klien lain. Al Manar sudah menolak beberapa desain karena
terasa templated. Buat pilihan desain yang deliberate dan spesifik untuk brief ini, ambil satu risiko
estetika yang bisa kamu justifikasi.

**Subjek & Brief:**
Al Manar adalah masjid/organisasi dakwah. Audiens: jamaah, donatur, masyarakat umum lintas usia
(bukan startup tech, bukan korporat). Suasana yang harus terasa: tenang (calming), terpercaya,
hangat, tidak kaku, tapi tetap terlihat profesional & modern — bukan website masjid ala tahun 2010
dengan warna hijau terang dan Comic Sans.

**Arahan Arah Visual (WAJIB DIIKUTI):**

- Theme: **light/elegant**, bukan dark mode.
- Nuansa: pola geometris Islami (girih/tessellation) dipakai sebagai aksen halus — bukan background rame yang mengganggu keterbacaan.
- Kalem tapi tidak flat: whitespace generous, tapi ada momen "signature" yang mengikat semua halaman.
- HINDARI 3 default look AI generik ini kecuali memang paling cocok untuk brief:
    1. Background krem hangat + serif kontras tinggi + aksen terracotta/oranye.
    2. Background hitam pekat + satu aksen hijau/merah neon.
    3. Layout broadsheet koran dengan garis tipis, border-radius nol, kolom rapat.

### Tahap 1 — Design Token Plan (tulis dulu sebelum ngoding)

Buat token system ringkas:

- **Warna (4–6 hex value bernama):** eksplorasi palet calming khas Islami — misal off-white/ivory,
  hijau tua zaitun/emerald deep, emas/brass sebagai aksen (bukan gold neon), navy gelap untuk teks.
  Jangan asal pilih hijau terang stereotip masjid; cari nuansa yang terasa premium (mis. deep sage,
  muted teal, atau warm sand).
- **Tipografi (2–3 role):** 1 display face berkarakter (bisa serif elegan atau typeface dengan sentuhan
  kaligrafis/geometris halus untuk judul), 1 body face yang sangat mudah dibaca (sans-serif netral),
  1 utility face untuk caption/label jika perlu. Pairing harus terasa dipilih khusus untuk brief ini,
  bukan pasangan default (jangan asal Playfair Display + Inter tanpa alasan — pikirkan ulang apakah itu
  benar-benar paling representatif untuk nuansa dakwah/masjid, atau ada pilihan lain yang lebih pas).
- **Layout concept:** deskripsikan 1 kalimat + ASCII wireframe untuk Home section (hero → highlight
  program → berita → testimoni → CTA donasi).
- **Signature element:** satu elemen unik yang jadi "ciri khas" Al Manar di semua halaman — misalnya
  garis pola geometris islami sebagai divider antar section, atau bentuk lengkung mihrab yang jadi
  motif frame foto/kartu. Pilih satu, eksekusi dengan disiplin di semua halaman (jangan berlebihan).

Setelah menulis plan ini, **kritik dulu**: apakah ada bagian yang terasa generic default kalau dipakai
untuk brief lain juga? Kalau ada, revisi dan sebutkan apa yang diubah dan kenapa.

### Tahap 2 — Build

Setelah plan disetujui (implisit lewat proses ini), baru tulis komponen Vue:

- Struktur komponen reusable: `<BaseButton>`, `<SectionContainer>`, `<Card>`, `<Divider>` (pakai
  signature element), dsb — jangan duplikasi style inline per halaman.
- Gunakan Tailwind CSS dengan **custom theme di `tailwind.config.js`** (warna & font dari token plan
  di atas) — jangan pakai warna Tailwind default (blue-500, dst).
- Motion: gunakan animasi secukupnya (scroll-reveal halus, hover micro-interaction) — hindari animasi
  berlebihan yang bikin terlihat "kebanyakan efek AI". Kalau ragu, kurangi.
- Wajib: responsive mobile-first, visible focus state untuk keyboard nav, hormati `prefers-reduced-motion`.
- Konten/copy: tulis dari sudut pandang pembaca (jamaah/donatur), aktif, jangan bahasa marketing
  berlebihan atau generic placeholder ("Lorem ipusm" style) — kalau data asli belum ada, tandai jelas
  `[ISI: ...]` supaya gampang dicari-ganti, jangan mengarang angka/statistik.

### Tahap 3 — Self-Critique

Sebelum dianggap selesai, cek ulang:

- Kalau prompt ini dipakai untuk brief masjid lain yang berbeda, apakah hasilnya akan terasa sama?
  Kalau iya → belum cukup spesifik, kurangi genericness.
- Apakah ada 1 aksesori yang bisa dilepas biar lebih clean (prinsip Chanel: sebelum keluar rumah,
  lepas satu aksesoris)?
- Screenshot/preview tiap breakpoint utama (mobile, tablet, desktop) sebelum lanjut ke halaman berikutnya.

---

## Cara Pakai

1. Jalankan Tahap 1 dulu di sesi terpisah — minta Claude Code tulis token plan-nya saja, review manual dulu.
2. Baru lanjut Tahap 2 per halaman (Home dulu, baru halaman lain, biar komponen reusable-nya konsisten).
3. Simpan token plan final ke `design-tokens.md` di root project supaya sesi coding berikutnya tinggal reference file itu (tidak perlu re-brainstorm tiap kali).
