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
        Schema::create('riwayat_verifikasi', function (Blueprint $table) {
            $table->id();
            // Wait, we need to refer to 'periode_laporan' table
            $table->foreignId('periode_laporan_id')->constrained('periode_laporan')->cascadeOnDelete();
            $table->enum('status', ['diajukan', 'ditolak', 'diverifikasi', 'dikoreksi']);
            $table->text('catatan')->nullable();
            $table->foreignId('oleh_user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_verifikasi');
    }
};
