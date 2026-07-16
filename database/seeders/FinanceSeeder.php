<?php

namespace Database\Seeders;

use App\Models\Bidang;
use App\Models\SubKategori;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FinanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Bidang
        $bidangs = [
            'YAYASAN' => 'Bendahara Yayasan',
            'DKM' => 'Bendahara DKM / BKM Masjid',
            'ZIS' => 'Bendahara ZIS (Kesra)',
            'SARPRAS' => 'Bendahara Sarpras / PU',
            'TPQ' => 'Bendahara TPQ',
            'TKIT' => 'Bendahara TKIT',
        ];

        $bidangModels = [];
        foreach ($bidangs as $kode => $nama) {
            $bidangModels[$kode] = Bidang::create([
                'kode_bidang' => $kode,
                'nama_bidang' => $nama,
            ]);
        }

        // 2. Create default Sub-Kategori per Bidang
        $subKategoriData = [
            'YAYASAN' => [
                'pemasukan' => ['Pemasukan Tetap', 'Idul Fitri', 'Idul Adha', 'Pemasukan Tidak Tetap', 'Pemindahbukuan Masuk'],
                'pengeluaran' => ['Pengeluaran Tetap', 'Pengeluaran Tidak Tetap', 'Pemindahbukuan Keluar'],
            ],
            'DKM' => [
                'pemasukan' => ['Kotak Infaq Jumat', 'Infaq Harian', 'Infaq Kajian Subuh', 'QRIS', 'Pemindahbukuan Masuk'],
                'pengeluaran' => ['Honor Imam/Khatib/Marbot/Satpam/Ustadz', 'Biaya Operasional', 'Mutasi Bank DKI Syariah & BSI', 'Pemindahbukuan Keluar'],
            ],
            'ZIS' => [
                'pemasukan' => ['Zakat Fitrah', 'Zakat Maal', 'Infaq', 'Shodaqoh', 'Pemindahbukuan Masuk'],
                'pengeluaran' => ['Penyaluran Zakat', 'Penyaluran Infaq/Shodaqoh', 'Biaya Operasional ZIS', 'Pemindahbukuan Keluar'],
            ],
            'SARPRAS' => [
                'pemasukan' => ['Alokasi Dana Yayasan', 'Infaq Khusus Pembangunan', 'Pemindahbukuan Masuk'],
                'pengeluaran' => ['Biaya Pemeliharaan', 'Perbaikan Fasilitas', 'Proyek Fisik Masjid', 'Pemindahbukuan Keluar'],
            ],
            'TPQ' => [
                'pemasukan' => ['SPP Santri', 'Infaq Wali Santri', 'Pemindahbukuan Masuk'],
                'pengeluaran' => ['Gaji Guru TPQ', 'Operasional KBM', 'Kegiatan Santri', 'Pemindahbukuan Keluar'],
            ],
            'TKIT' => [
                'pemasukan' => ['SPP Bulanan', 'Uang Pangkal', 'Donasi Pendidikan', 'Pemindahbukuan Masuk'],
                'pengeluaran' => ['Gaji Guru & Staff TKIT', 'Operasional Sekolah', 'Kegiatan Belajar Mengajar', 'Sarana Prasarana TKIT', 'Pemindahbukuan Keluar'],
            ],
        ];

        foreach ($subKategoriData as $kode => $categories) {
            $bidangId = $bidangModels[$kode]->id;

            foreach ($categories['pemasukan'] as $nama) {
                SubKategori::create([
                    'bidang_id' => $bidangId,
                    'kategori' => 'pemasukan',
                    'nama_sub_kategori' => $nama,
                    'is_active' => true,
                ]);
            }

            foreach ($categories['pengeluaran'] as $nama) {
                SubKategori::create([
                    'bidang_id' => $bidangId,
                    'kategori' => 'pengeluaran',
                    'nama_sub_kategori' => $nama,
                    'is_active' => true,
                ]);
            }
        }

        // 3. Create General Treasurer (Bendahara Umum)
        User::create([
            'name' => 'Bendahara Umum Al Manar',
            'email' => 'bendahara.umum@gmail.com',
            'role' => 'bendahara_umum',
            'password' => Hash::make('password'),
        ]);

        // 4. Create Field Treasurers (Bendahara Bidang)
        $usersBidang = [
            'YAYASAN' => ['name' => 'Bendahara Yayasan', 'email' => 'bendahara.yayasan@gmail.com'],
            'DKM' => ['name' => 'Bendahara DKM', 'email' => 'bendahara.dkm@gmail.com'],
            'ZIS' => ['name' => 'Bendahara ZIS', 'email' => 'bendahara.zis@gmail.com'],
            'SARPRAS' => ['name' => 'Bendahara Sarpras', 'email' => 'bendahara.sarpras@gmail.com'],
            'TPQ' => ['name' => 'Bendahara TPQ', 'email' => 'bendahara.tpq@gmail.com'],
            'TKIT' => ['name' => 'Bendahara TKIT', 'email' => 'bendahara.tkit@gmail.com'],
        ];

        foreach ($usersBidang as $kode => $userData) {
            User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'role' => 'bendahara_bidang',
                'bidang_id' => $bidangModels[$kode]->id,
                'password' => Hash::make('password'),
            ]);
        }
    }
}
