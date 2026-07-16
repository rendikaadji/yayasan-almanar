<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bidang', function (Blueprint $table) {
            $table->id();
            $table->string('nama_bidang');
            $table->string('kode_bidang')->unique();
            $table->timestamps();
        });

        Schema::create('sub_kategori', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bidang_id')->constrained('bidang')->cascadeOnDelete();
            $table->enum('kategori', ['pemasukan', 'pengeluaran']);
            $table->string('nama_sub_kategori');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('periode_laporan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bidang_id')->constrained('bidang')->cascadeOnDelete();
            $table->unsignedTinyInteger('bulan');
            $table->unsignedSmallInteger('tahun');
            $table->enum('status', ['draft', 'diajukan', 'diverifikasi', 'dikonsolidasi'])->default('draft');
            $table->decimal('saldo_awal', 15, 2)->default(0);
            $table->decimal('saldo_akhir', 15, 2)->default(0);
            $table->timestamps();

            $table->unique(['bidang_id', 'bulan', 'tahun'], 'uidx_periode_bidang_bulan_tahun');
        });

        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_laporan_id')->constrained('periode_laporan')->cascadeOnDelete();
            $table->foreignId('bidang_id')->constrained('bidang')->cascadeOnDelete();
            $table->date('tanggal');
            $table->enum('kategori', ['pemasukan', 'pengeluaran']);
            $table->foreignId('sub_kategori_id')->constrained('sub_kategori')->cascadeOnDelete();
            $table->text('uraian');
            $table->decimal('jumlah', 15, 2);
            $table->string('pic')->nullable();
            $table->string('bukti_transaksi')->nullable();
            $table->foreignId('dibuat_oleh')->constrained('users')->cascadeOnDelete();
            $table->foreignId('bidang_asal_id')->nullable()->constrained('bidang')->nullOnDelete();
            $table->foreignId('bidang_tujuan_id')->nullable()->constrained('bidang')->nullOnDelete();
            $table->unsignedBigInteger('parent_transaksi_id')->nullable(); // Self-referencing link for transfers
            $table->timestamps();
        });

        Schema::create('rekap_konsolidasi', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('bulan');
            $table->unsignedSmallInteger('tahun');
            $table->decimal('total_pemasukan_semua_bidang', 15, 2)->default(0);
            $table->decimal('total_pengeluaran_semua_bidang', 15, 2)->default(0);
            $table->decimal('saldo_akhir_gabungan', 15, 2)->default(0);
            $table->enum('status', ['draft', 'final', 'ditandatangani'])->default('draft');
            $table->foreignId('dibuat_oleh')->constrained('users')->cascadeOnDelete();
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('tanggal_disetujui')->nullable();
            $table->string('pdf_path')->nullable();
            $table->timestamps();

            $table->unique(['bulan', 'tahun'], 'uidx_rekap_bulan_tahun');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi');
        Schema::dropIfExists('periode_laporan');
        Schema::dropIfExists('sub_kategori');
        Schema::dropIfExists('rekap_konsolidasi');
        Schema::dropIfExists('bidang');
    }
};
