<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksi';

    protected $fillable = [
        'periode_laporan_id',
        'bidang_id',
        'tanggal',
        'kategori',
        'sub_kategori_id',
        'uraian',
        'jumlah',
        'pic',
        'bukti_transaksi',
        'dibuat_oleh',
        'bidang_asal_id',
        'bidang_tujuan_id',
        'parent_transaksi_id'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'decimal:2',
    ];

    public static $isSyncing = false;

    public function periodeLaporan()
    {
        return $this->belongsTo(PeriodeLaporan::class, 'periode_laporan_id');
    }

    public function bidang()
    {
        return $this->belongsTo(Bidang::class, 'bidang_id');
    }

    public function subKategori()
    {
        return $this->belongsTo(SubKategori::class, 'sub_kategori_id');
    }

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function bidangAsal()
    {
        return $this->belongsTo(Bidang::class, 'bidang_asal_id');
    }

    public function bidangTujuan()
    {
        return $this->belongsTo(Bidang::class, 'bidang_tujuan_id');
    }

    protected static function booted()
    {
        // Prevent edits if period is locked
        static::saving(function ($transaksi) {
            $period = $transaksi->periodeLaporan;
            if ($period) {
                if (in_array($period->status, ['diverifikasi', 'dikonsolidasi'])) {
                    throw new \Exception('Periode laporan sudah diverifikasi atau dikonsolidasi dan tidak dapat diubah.');
                }
                
                if ($period->status === 'diajukan' && auth()->check() && auth()->user()->isBendaharaBidang()) {
                    throw new \Exception('Periode laporan sudah diajukan dan sedang menunggu verifikasi.');
                }
            }
        });

        static::deleting(function ($transaksi) {
            $period = $transaksi->periodeLaporan;
            if ($period) {
                if (in_array($period->status, ['diverifikasi', 'dikonsolidasi'])) {
                    throw new \Exception('Periode laporan sudah diverifikasi atau dikonsolidasi dan tidak dapat diubah.');
                }
                
                if ($period->status === 'diajukan' && auth()->check() && auth()->user()->isBendaharaBidang()) {
                    throw new \Exception('Periode laporan sudah diajukan dan sedang menunggu verifikasi.');
                }
            }
        });

        // 1. Recalculate balance on save (create/update)
        static::saved(function ($transaksi) {
            $transaksi->periodeLaporan->recalculateBalance();

            // Handle Sync for Inter-Bidang Transfer
            if (self::$isSyncing) {
                return;
            }

            self::$isSyncing = true;
            try {
                $transaksi->syncTransferOnSave();
            } finally {
                self::$isSyncing = false;
            }
        });

        // 2. Recalculate balance on delete
        static::deleted(function ($transaksi) {
            $transaksi->periodeLaporan->recalculateBalance();

            // Handle Sync delete for Inter-Bidang Transfer
            if (self::$isSyncing) {
                return;
            }

            self::$isSyncing = true;
            try {
                $transaksi->syncTransferOnDelete();
            } finally {
                self::$isSyncing = false;
            }
        });
    }

    /**
     * Synchronize inter-bidang transfers on save.
     */
    public function syncTransferOnSave()
    {
        // Case A: This is a new or edited transfer out from this bidang
        if ($this->kategori === 'pengeluaran' && $this->bidang_tujuan_id) {
            $targetBidang = Bidang::find($this->bidang_tujuan_id);
            if (!$targetBidang) return;

            // Find or create active/draft period for target bidang in the same month/year
            $sourcePeriod = $this->periodeLaporan;
            $targetPeriod = PeriodeLaporan::firstOrCreate(
                [
                    'bidang_id' => $this->bidang_tujuan_id,
                    'bulan' => $sourcePeriod->bulan,
                    'tahun' => $sourcePeriod->tahun,
                ],
                [
                    'status' => 'draft',
                    'saldo_awal' => 0.00,
                    'saldo_akhir' => 0.00,
                ]
            );

            // Look up standard subcategory for receiving bidang
            $subCat = SubKategori::where('bidang_id', $this->bidang_tujuan_id)
                ->where('kategori', 'pemasukan')
                ->where(function ($query) {
                    $query->where('nama_sub_kategori', 'like', '%Pemindahbukuan%')
                          ->orWhere('nama_sub_kategori', 'like', '%Transfer%')
                          ->orWhere('nama_sub_kategori', 'like', '%Tidak Tetap%');
                })
                ->first() ?: SubKategori::where('bidang_id', $this->bidang_tujuan_id)
                    ->where('kategori', 'pemasukan')
                    ->first();

            if (!$subCat) return;

            $sourceBidang = $this->bidang;
            $data = [
                'periode_laporan_id' => $targetPeriod->id,
                'bidang_id' => $this->bidang_tujuan_id,
                'tanggal' => $this->tanggal,
                'kategori' => 'pemasukan',
                'sub_kategori_id' => $subCat->id,
                'uraian' => "Terima transfer dari " . ($sourceBidang ? $sourceBidang->nama_bidang : 'Bidang Lain') . ": " . $this->uraian,
                'jumlah' => $this->jumlah,
                'bidang_asal_id' => $this->bidang_id,
                'dibuat_oleh' => $this->dibuat_oleh,
                'parent_transaksi_id' => $this->id,
            ];

            if ($this->parent_transaksi_id) {
                // If we already linked a transaction, update it
                $linked = self::find($this->parent_transaksi_id);
                if ($linked) {
                    $linked->update($data);
                    $linked->periodeLaporan->recalculateBalance();
                }
            } else {
                // Create a new linked transaction in the target bidang
                $linked = self::create($data);
                
                // Update parent_transaksi_id on both sides to establish the two-way link
                $this->parent_transaksi_id = $linked->id;
                $this->saveQuietly(); // Use saveQuietly to prevent triggering events again
                
                $linked->parent_transaksi_id = $this->id;
                $linked->saveQuietly();
            }
        }
        // Case B: This is a linked transaction that was modified. We sync back.
        elseif ($this->parent_transaksi_id) {
            $linked = self::find($this->parent_transaksi_id);
            if ($linked) {
                // Keep the amount and date in sync
                $linked->update([
                    'jumlah' => $this->jumlah,
                    'tanggal' => $this->tanggal,
                ]);
                $linked->periodeLaporan->recalculateBalance();
            }
        }
    }

    /**
     * Delete the linked transaction when this transfer is deleted.
     */
    public function syncTransferOnDelete()
    {
        if ($this->parent_transaksi_id) {
            $linked = self::find($this->parent_transaksi_id);
            if ($linked) {
                $linked->delete();
            }
        }
    }
}
