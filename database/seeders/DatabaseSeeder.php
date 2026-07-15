<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Post;
use App\Models\Program;
use App\Models\Gallery;
use App\Models\GalleryItem;
use App\Models\Testimonial;
use App\Models\DonationProgram;
use App\Models\DonationReport;
use App\Models\ContactMessage;
use App\Models\OrganizationStructure;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create default admin user
        $admin = User::create([
            'name' => 'Admin Al Manar',
            'email' => 'admin@almanar.or.id',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        // 2. Seed Posts (Berita)
        Post::create([
            'title' => 'Tabligh Akbar Menyambut Bulan Suci Ramadhan 1447 H',
            'slug' => 'tabligh-akbar-ramadhan-1447',
            'content' => "Alhamdulillah, DKM Al Manar akan menyelenggarakan Tabligh Akbar dalam rangka menyambut datangnya bulan suci Ramadhan 1447 H. Acara ini akan menghadirkan penceramah nasional dan terbuka untuk umum. Mari ajak keluarga dan tetangga untuk menghadiri majelis ilmu ini guna mempersiapkan bekal ruhiyah menyongsong bulan suci penuh berkah.",
            'thumbnail' => '/images/tabligh_akbar_banner.png',
            'published_at' => now()->subDays(2),
            'author_id' => $admin->id,
        ]);

        Post::create([
            'title' => 'Penyaluran Santunan Bulanan Anak Yatim & Dhuafa Sekitar Masjid',
            'slug' => 'penyaluran-santunan-bulanan-anak-yatim',
            'content' => "Al Manar kembali menyalurkan dana santunan rutin bulanan bagi 50 anak yatim dan dhuafa di lingkungan sekitar masjid. Dana yang disalurkan merupakan amanah dari donatur program donasi peduli yatim. Kami mengucapkan jazakumullah khairan katsiran atas kontribusi para donatur sekalian. Semoga menjadi pembuka pintu rezeki.",
            'thumbnail' => '/images/hero.png',
            'published_at' => now()->subDay(),
            'author_id' => $admin->id,
        ]);

        // 3. Seed Programs (Kegiatan)
        Program::create([
            'title' => "Kajian Tafsir Al-Qur'an Tematik",
            'slug' => 'kajian-tafsir-alquran-tematik',
            'category' => 'Kajian',
            'schedule' => 'Setiap Sabtu Ba\'da Subuh',
            'location' => 'Ruang Utama Masjid Al Manar',
            'description' => 'Kajian rutin mingguan mengupas kandungan ayat-ayat Al-Qur\'an secara kontekstual, dipandu oleh Dewan Kehormatan Asatidzah DKM Al Manar untuk umum baik ikhwan maupun akhwat.',
            'image' => '/images/hero.png',
        ]);

        Program::create([
            'title' => 'Taman Pendidikan Al-Qur\'an (TPQ) Anak',
            'slug' => 'tpq-anak-al-manar',
            'category' => 'Pendidikan',
            'schedule' => 'Senin s/d Kamis, Jam 16.00 - 17.30',
            'location' => 'Aula Serbaguna Al Manar',
            'description' => 'Program pembinaan baca tulis Al-Qur\'an untuk usia 6-12 tahun dengan metode Iqra, tajwid praktis, hafalan surat-surat pendek juz 30, doa harian, dan fiqih ibadah dasar.',
            'image' => '/images/program_quran.png',
        ]);

        Program::create([
            'title' => 'Layanan Siaga Ambulans Gratis Al Manar',
            'slug' => 'layanan-siaga-ambulans-gratis',
            'category' => 'Sosial',
            'schedule' => 'Siaga 24 Jam Nonstop',
            'location' => 'Garasi Ambulans Masjid Al Manar',
            'description' => 'Penyediaan fasilitas armada ambulans siaga gratis untuk memfasilitasi kebutuhan darurat medis, rujukan rumah sakit, dan pengantaran jenazah bagi warga umum sekitar masjid.',
            'image' => '/images/hero.png',
        ]);

        // 4. Seed Galleries (Galeri)
        $gallery1 = Gallery::create([
            'album_name' => 'Kegiatan Tabligh Akbar Ramadhan',
            'cover_image' => '/images/tabligh_akbar_banner.png',
        ]);
        GalleryItem::create([
            'gallery_id' => $gallery1->id,
            'file_path' => '/images/tabligh_akbar_banner.png',
            'type' => 'image',
        ]);

        $gallery2 = Gallery::create([
            'album_name' => 'Dokumentasi Penyaluran Sembako Sosial',
            'cover_image' => '/images/hero.png',
        ]);
        GalleryItem::create([
            'gallery_id' => $gallery2->id,
            'file_path' => '/images/hero.png',
            'type' => 'image',
        ]);

        // 5. Seed Testimonials (Testimoni)
        Testimonial::create([
            'name' => 'Bapak H. M. Solihin',
            'message' => 'Sangat terbantu dengan adanya program Ambulans Siaga Gratis Al Manar. Respon tim DKM sangat cepat kala mengantar mertua saya berobat darurat di tengah malam.',
            'photo' => '/images/pengurus_avatar.png',
            'is_featured' => true,
        ]);

        Testimonial::create([
            'name' => 'Ibu Rahmawati',
            'message' => 'Laporan transparansi penggunaan dana donasi yang diunggah secara terbuka membuat kami para donatur merasa tenang karena penyalurannya akuntabel dan jelas sasaran.',
            'photo' => '/images/pengurus_avatar.png',
            'is_featured' => true,
        ]);

        // 6. Seed Donation Programs & Reports (Donasi)
        $donation = DonationProgram::create([
            'title' => 'Donasi Renovasi Sarana Tempat Wudhu Masjid',
            'slug' => 'donasi-renovasi-tempat-wudhu',
            'description' => 'Penggalangan dana renovasi perluasan area wudhu pria dan wanita guna meningkatkan kenyamanan jamaah saat bersuci menjelang waktu shalat berjamaah.',
            'target_amount' => 45000000,
            'collected_amount' => 28350000,
            'deadline' => '2026-12-31',
            'image' => '/images/donation_wudhu.png',
        ]);

        DonationReport::create([
            'donation_program_id' => $donation->id,
            'description' => 'Pembelian keramik anti-licin wudhu sebanyak 60 dus',
            'amount' => 6400000,
            'date' => '2026-07-10',
        ]);

        // 7. Seed Contact Messages (Pesan Masuk)
        ContactMessage::create([
            'name' => 'Ahmad Subarjo',
            'email' => 'ahmad.subarjo@gmail.com',
            'phone' => '081299998888',
            'message' => 'Assalamu\'alaikum, apakah DKM Al Manar memiliki program kajian khusus malam hari untuk pekerja kantor?',
            'is_read' => false,
        ]);

        // 8. Seed Organization Board (Pengurus)
        OrganizationStructure::create([
            'name' => 'Ustadz H. Lukman Hakim, Lc.',
            'position' => 'Ketua Dewan Kemakmuran Masjid (DKM)',
            'photo' => '/images/pengurus_avatar.png',
            'order' => 1,
        ]);

        OrganizationStructure::create([
            'name' => 'Bapak Ir. H. Bambang Widjojo',
            'position' => 'Sekretaris Umum',
            'photo' => '/images/pengurus_avatar.png',
            'order' => 2,
        ]);

        OrganizationStructure::create([
            'name' => 'Bapak H. Teddy Hartanto, S.E.',
            'position' => 'Bendahara Masjid',
            'photo' => '/images/pengurus_avatar.png',
            'order' => 3,
        ]);
    }
}
