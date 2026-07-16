<?php

namespace App\Services;

use App\Models\Bidang;
use App\Models\PeriodeLaporan;
use App\Models\Transaksi;
use App\Models\RekapKonsolidasi;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

class RekapPdfGenerator
{
    /**
     * Generate consolidated report PDF and save it to storage.
     * 
     * @param int $bulan
     * @param int $tahun
     * @param RekapKonsolidasi $rekap
     * @return string Relative path to PDF file
     */
    public function generate(int $bulan, int $tahun, RekapKonsolidasi $rekap): string
    {
        $namaBulan = Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F');
        $bidangs = Bidang::orderBy('id', 'asc')->get();
        
        $bidangData = [];
        $totals = [
            'saldo_awal' => 0,
            'pemasukan' => 0,
            'pengeluaran' => 0,
            'selisih' => 0,
            'saldo_akhir' => 0
        ];

        foreach ($bidangs as $b) {
            $period = PeriodeLaporan::where('bidang_id', $b->id)
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->first();

            $saldoAwal = $period ? (float)$period->saldo_awal : 0;
            $pemasukan = $period ? (float)$period->transaksi()->where('kategori', 'pemasukan')->sum('jumlah') : 0;
            $pengeluaran = $period ? (float)$period->transaksi()->where('kategori', 'pengeluaran')->sum('jumlah') : 0;
            $selisih = $pemasukan - $pengeluaran;
            $saldoAkhir = $saldoAwal + $selisih;

            $bidangData[] = [
                'nama' => $b->nama_bidang,
                'saldo_awal' => $saldoAwal,
                'pemasukan' => $pemasukan,
                'pengeluaran' => $pengeluaran,
                'selisih' => $selisih,
                'saldo_akhir' => $saldoAkhir
            ];

            $totals['saldo_awal'] += $saldoAwal;
            $totals['pemasukan'] += $pemasukan;
            $totals['pengeluaran'] += $pengeluaran;
            $totals['selisih'] += $selisih;
            $totals['saldo_akhir'] += $saldoAkhir;
        }

        // Detailed Transactions
        $txList = Transaksi::with(['bidang', 'subKategori'])
            ->whereHas('periodeLaporan', function ($q) use ($bulan, $tahun) {
                $q->where('bulan', $bulan)->where('tahun', $tahun);
            })
            ->get();

        $inflow = $txList->where('kategori', 'pemasukan')->values();
        $outflow = $txList->where('kategori', 'pengeluaran')->values();

        $data = [
            'bulan' => $bulan,
            'tahun' => $tahun,
            'namaBulan' => $namaBulan,
            'bidangData' => $bidangData,
            'totals' => $totals,
            'inflow' => $inflow,
            'outflow' => $outflow,
            'tanggalCetak' => Carbon::now()->translatedFormat('d F Y')
        ];

        // Render PDF
        $pdf = Pdf::loadView('exports.rekap_pdf', $data);
        
        // Ensure storage directory exists
        $dir = 'public/rekap';
        if (!Storage::exists($dir)) {
            Storage::makeDirectory($dir);
        }

        $fileName = "rekap_konsolidasi_{$bulan}_{$tahun}.pdf";
        $filePath = "$dir/$fileName";
        
        // Save to storage
        Storage::put($filePath, $pdf->output());

        // Return path for database (we store relative to public disk)
        return "storage/rekap/$fileName";
    }
}
