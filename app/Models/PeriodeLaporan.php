<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodeLaporan extends Model
{
    protected $table = 'periode_laporan';

    protected $fillable = ['bidang_id', 'bulan', 'tahun', 'status', 'saldo_awal', 'saldo_akhir'];

    protected $casts = [
        'saldo_awal' => 'decimal:2',
        'saldo_akhir' => 'decimal:2',
    ];

    public function bidang()
    {
        return $this->belongsTo(Bidang::class, 'bidang_id');
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'periode_laporan_id');
    }

    public function riwayatVerifikasi()
    {
        return $this->hasMany(RiwayatVerifikasi::class, 'periode_laporan_id')->orderBy('created_at', 'asc');
    }

    /**
     * Recalculates the saldo_akhir for the current period
     * and cascades the change as saldo_awal to the next period.
     */
    public function recalculateBalance()
    {
        $totalPemasukan = $this->transaksi()->where('kategori', 'pemasukan')->sum('jumlah');
        $totalPengeluaran = $this->transaksi()->where('kategori', 'pengeluaran')->sum('jumlah');
        
        $this->saldo_akhir = $this->saldo_awal + $totalPemasukan - $totalPengeluaran;
        $this->save();

        // Propagate to the next month
        $nextMonth = $this->bulan == 12 ? 1 : $this->bulan + 1;
        $nextYear = $this->bulan == 12 ? $this->tahun + 1 : $this->tahun;

        $nextPeriod = self::where('bidang_id', $this->bidang_id)
            ->where('bulan', $nextMonth)
            ->where('tahun', $nextYear)
            ->first();

        if ($nextPeriod) {
            $nextPeriod->saldo_awal = $this->saldo_akhir;
            $nextPeriod->recalculateBalance();
        }
    }
}
