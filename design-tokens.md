# Design Tokens — Al Manar Company Profile

Dokumen ini mendefinisikan identitas visual Al Manar untuk diimplementasikan dalam konfigurasi Tailwind dan komponen Vue.

## 1. Color Palette (Palet Warna)

Untuk menghindari warna hijau terang generik masjid tahun 2010 atau krem hangat "terracotta" yang terlalu sering dipakai, kita memilih palet bernuansa *cooling, calming, & stone masonry* (Deep Sage, Teal Obsidian, Brass Gold, dan Warm Stone).

| Token Name | Hex Value | Role & Usage |
| :--- | :--- | :--- |
| **Sage Deep** | `#2C4F3E` | Warna brand utama, digunakan untuk brand header, text highlight, dan dark button backgrounds. |
| **Teal Obsidian** | `#1B3236` | Warna sekunder gelap, digunakan untuk kontras, deep text, dan dark sections (seperti footer). |
| **Brass Gold** | `#C7A870` | Warna aksen, melambangkan kubah logam kuningan, digunakan untuk hover state, active border, dan icon accents. |
| **Warm Stone** | `#F5F5F2` | Background dasar situs, memberikan impresi batuan marmer/dinding batu masjid yang sejuk dibanding off-white krem hangat kertas. |
| **Cool Slate** | `#EBEAE4` | Warna background sekunder untuk membedakan section (seperti kartu atau section highlights). |
| **Navy Slate** | `#1E293B` | Warna teks utama (body text) agar keterbacaan tetap tinggi dibanding warna hitam pekat. |
| **Muted Moss** | `#5F7D6D` | Teks sekunder, label kategori, border halus. |

---

## 2. Typography (Tipografi)

Pairing font ini dipilih agar memberikan kesan abadi, terukir (sculpted), sekaligus modern dan bersih.

*   **Display Font (Judul Utama/H1-H2)**: `Forum` (Google Fonts)
    *   *Penerapan:* Khusus untuk judul besar/display headings, diaplikasikan dengan style `tracking-wider uppercase` atau `font-normal`.
    *   *Rasional:* Font serif dengan proporsi klasik terinspirasi dari pahatan batu kuno. Memberikan aura spiritual yang agung dan tenang.
*   **Body Font (Teks Utama & Sub-Judul)**: `Plus Jakarta Sans` (Google Fonts)
    *   *Penerapan:* Body text, paragraf, subheadings (H3-H4).
    *   *Rasional:* Sans-serif modern berkarakter ramah namun rapi. Menjaga keterbacaan artikel panjang di perangkat mobile.
*   **Utility Font (Button/Label/Metadata)**: `Outfit` (Google Fonts)
    *   *Penerapan:* Tombol, tag kategori, angka statistik, metadata artikel.
    *   *Rasional:* Sangat geometris, membantu memberikan keterbacaan instan untuk angka dan label status.

---

## 3. Layout Concept (Konsep Tata Letak)

Arah visual layout menggunakan grid yang lapang (*generous whitespace*) dengan kartu-kartu berbingkai melengkung mihrab.

### Home Page ASCII Wireframe

```
+--------------------------------------------------------+
|  [Logo: Al Manar]              [Menu: Profil - Program] |
+--------------------------------------------------------+
|                                                        |
|  [Hero Section]                                        |
|  "Tenang, Berdaya, Berbagi"                            |
|  Tagline & Deskripsi Singkat                           |
|  [CTA Donasi] (Solid Sage Button)                      |
|                                                        |
|  *Hero Image menggunakan masker lengkung Ogee Arch*     |
|                                                        |
+--------------------------------------------------------+
|  [Highlights Program - 3 Cards with Arch Hover Outlines]|
|  +----------------+ +----------------+ +----------------+ |
|  | [Ogee Outline] | |                | |                | |
|  | Kajian Rutin   | | TPQ & Tahfidz  | | Sosial-Dakwah  | |
|  +----------------+ +----------------+ +----------------+ |
+--------------------------------------------------------+
|  [Divider: Subtle Girih Line - Knotted at Screen Edges] |
+--------------------------------------------------------+
|  [Berita Terkini - Grid 3 Kolom]                       |
|  +------------+  +------------+  +------------+        |
|  | [Thumbnail]|  | [Thumbnail]|  | [Thumbnail]|        |
|  | Title...   |  | Title...   |  | Title...   |        |
|  +------------+  +------------+  +------------+        |
+--------------------------------------------------------+
|  [Testimoni - Soft Card Carousel]                      |
|  "Program Al Manar sangat transparan..." - Donatur     |
+--------------------------------------------------------+
|  [Donasi Call to Action - Golden Brass Ogee Card]      |
|  Target Dana & Progress Bar + Info Rekening Statis     |
+--------------------------------------------------------+
|  [Footer - Deep Teal]                                  |
+--------------------------------------------------------+
```

---

## 4. Signature Element (Elemen Khas)

*   **Ogee Arch Frame (Lengkung Mihrab Persia)**:
    Alih-alih lengkungan bulat biasa, kita menggunakan **Ogee Arch** (lengkung mihrab dengan kurva ganda berbentuk S yang meruncing lembut di ujung atas). Bentuk ini diterapkan pada:
    *   Masking frame foto utama di Hero dan visual detail program.
    *   Garis aksen/outline berwarna emas kuningan yang menyorot kartu menu/tombol utama saat di-hover.
*   **Fading Girih Pattern Divider**:
    Garis divider pemisah section menggunakan garis tipis `Muted Moss` yang membentuk simpul pola geometris Islam (Girih) rumit di ujung pinggir layar, lalu perlahan memudar (*fade out*) menjadi garis lurus bersih di area tengah konten. Ini memberikan sentuhan artistik tanpa mengorbankan keterbacaan teks utama di tengah halaman.
