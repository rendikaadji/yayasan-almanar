<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RekapKonsolidasi extends Model
{
    protected $table = 'rekap_konsolidasi';

    protected $fillable = [
        'bulan',
        'tahun',
        'total_pemasukan_semua_bidang',
        'total_pengeluaran_semua_bidang',
        'saldo_akhir_gabungan',
        'status',
        'dibuat_oleh',
        'disetujui_oleh',
        'tanggal_disetujui',
        'pdf_path'
    ];

    protected $casts = [
        'total_pemasukan_semua_bidang' => 'decimal:2',
        'total_pengeluaran_semua_bidang' => 'decimal:2',
        'saldo_akhir_gabungan' => 'decimal:2',
        'tanggal_disetujui' => 'datetime',
    ];

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function penyetuju()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }
}
