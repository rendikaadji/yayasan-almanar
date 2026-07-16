<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatVerifikasi extends Model
{
    protected $table = 'riwayat_verifikasi';

    // Disable default timestamps because migration uses only created_at
    public $timestamps = false;

    protected $fillable = [
        'periode_laporan_id',
        'status',
        'catatan',
        'oleh_user_id',
        'created_at'
    ];

    public function periodeLaporan()
    {
        return $this->belongsTo(PeriodeLaporan::class, 'periode_laporan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'oleh_user_id');
    }
}
