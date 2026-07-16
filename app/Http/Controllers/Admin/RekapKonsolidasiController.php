<?php

namespace App\Http\Controllers\Admin;

use App\Exports\RekapKonsolidasiExport;
use App\Http\Controllers\Controller;
use App\Models\Bidang;
use App\Models\PeriodeLaporan;
use App\Models\RekapKonsolidasi;
use App\Models\RiwayatVerifikasi;
use App\Services\RekapPdfGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;

class RekapKonsolidasiController extends Controller
{
    protected $pdfGenerator;

    public function __construct(RekapPdfGenerator $pdfGenerator)
    {
        $this->pdfGenerator = $pdfGenerator;
    }

    public function index(Request $request): Response
    {
        $bulan = (int)$request->input('bulan', date('m'));
        $tahun = (int)$request->input('tahun', date('Y'));

        $bidangs = Bidang::orderBy('id', 'asc')->get();
        $bidangReports = [];
        $allVerified = true;
        $anyReport = false;

        foreach ($bidangs as $b) {
            $period = PeriodeLaporan::where('bidang_id', $b->id)
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->first();

            if ($period) {
                $anyReport = true;
                if (!in_array($period->status, ['diverifikasi', 'dikonsolidasi'])) {
                    $allVerified = false;
                }
            } else {
                $allVerified = false;
            }

            $bidangReports[] = [
                'bidang' => $b,
                'period' => $period,
                'status' => $period ? $period->status : 'belum_buka',
                'total_pemasukan' => $period ? (float)$period->transaksi()->where('kategori', 'pemasukan')->sum('jumlah') : 0,
                'total_pengeluaran' => $period ? (float)$period->transaksi()->where('kategori', 'pengeluaran')->sum('jumlah') : 0,
                'saldo_akhir' => $period ? (float)$period->saldo_akhir : 0,
            ];
        }

        $rekap = RekapKonsolidasi::where('bulan', $bulan)->where('tahun', $tahun)->first();

        return Inertia::render('Admin/Finance/Rekap/Index', [
            'reports' => $bidangReports,
            'rekap' => $rekap,
            'selectedBulan' => $bulan,
            'selectedTahun' => $tahun,
            'canGenerate' => $allVerified && $anyReport && !$rekap,
        ]);
    }

    public function showPeriode(Request $request, $id)
    {
        // Reuse view but indicate general treasurer access
        $periode = PeriodeLaporan::findOrFail($id);
        return redirect()->route('admin.finance.periods.show', $periode->id);
    }

    public function verifikasi(Request $request, $id)
    {
        $periode = PeriodeLaporan::findOrFail($id);
        $user = $request->user();

        if (!$user->isBendaharaUmum() && !$user->isAdmin()) {
            abort(403);
        }

        if ($periode->status !== 'diajukan') {
            return redirect()->back()->with('error', 'Hanya laporan yang berstatus DIAJUKAN yang dapat diverifikasi.');
        }

        DB::transaction(function () use ($periode, $user) {
            $periode->update(['status' => 'diverifikasi']);

            RiwayatVerifikasi::create([
                'periode_laporan_id' => $periode->id,
                'status' => 'diverifikasi',
                'catatan' => 'Laporan disetujui & diverifikasi oleh Bendahara Umum',
                'oleh_user_id' => $user->id,
            ]);
        });

        $namaBidang = $periode->bidang ? $periode->bidang->nama_bidang : 'Bidang';

        return redirect()->route('admin.finance.rekap.index', [
            'bulan' => $periode->bulan,
            'tahun' => $periode->tahun
        ])->with('success', "Laporan {$namaBidang} berhasil diverifikasi.");
    }

    public function tolak(Request $request, $id)
    {
        $request->validate([
            'catatan' => 'required|string|max:1000'
        ]);

        $periode = PeriodeLaporan::findOrFail($id);
        $user = $request->user();

        if (!$user->isBendaharaUmum() && !$user->isAdmin()) {
            abort(403);
        }

        if ($periode->status !== 'diajukan') {
            return redirect()->back()->with('error', 'Hanya laporan yang berstatus DIAJUKAN yang dapat ditolak.');
        }

        DB::transaction(function () use ($periode, $request, $user) {
            $periode->update(['status' => 'draft']);

            RiwayatVerifikasi::create([
                'periode_laporan_id' => $periode->id,
                'status' => 'ditolak',
                'catatan' => $request->input('catatan'),
                'oleh_user_id' => $user->id,
            ]);
        });

        return redirect()->route('admin.finance.rekap.index', [
            'bulan' => $periode->bulan,
            'tahun' => $periode->tahun
        ])->with('success', 'Laporan dikembalikan ke status Draft untuk direvisi.');
    }

    public function generate(Request $request)
    {
        $request->validate([
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020|max:2030',
        ]);

        $bulan = (int)$request->input('bulan');
        $tahun = (int)$request->input('tahun');
        $user = $request->user();

        // 1. Double check that all bidang have a verified report for this month/year
        $bidangs = Bidang::all();
        $periods = PeriodeLaporan::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->whereIn('status', ['diverifikasi', 'dikonsolidasi'])
            ->get();

        if ($periods->count() < $bidangs->count()) {
            return redirect()->back()->with('error', 'Gagal membuat rekap. Pastikan laporan semua bidang sudah diverifikasi.');
        }

        // Check if already consolidated
        $exists = RekapKonsolidasi::where('bulan', $bulan)->where('tahun', $tahun)->exists();
        if ($exists) {
            return redirect()->back()->with('error', 'Rekap konsolidasi untuk bulan ini sudah pernah dibuat.');
        }

        // 2. Perform math aggregation
        $totalPemasukan = 0;
        $totalPengeluaran = 0;
        $totalSaldoAkhir = 0;

        foreach ($periods as $p) {
            $pemasukan = (float)$p->transaksi()->where('kategori', 'pemasukan')->sum('jumlah');
            $pengeluaran = (float)$p->transaksi()->where('kategori', 'pengeluaran')->sum('jumlah');
            
            $totalPemasukan += $pemasukan;
            $totalPengeluaran += $pengeluaran;
            $totalSaldoAkhir += ((float)$p->saldo_awal + $pemasukan - $pengeluaran);
        }

        // 3. Save Consolidated Report
        $rekap = DB::transaction(function () use ($bulan, $tahun, $totalPemasukan, $totalPengeluaran, $totalSaldoAkhir, $user, $periods) {
            // Update participating periods to "dikonsolidasi" status
            foreach ($periods as $p) {
                $p->update(['status' => 'dikonsolidasi']);
                
                RiwayatVerifikasi::create([
                    'periode_laporan_id' => $p->id,
                    'status' => 'diverifikasi', // logged as verified/consolidated
                    'catatan' => 'Laporan dimasukkan dalam rekap konsolidasi bulanan',
                    'oleh_user_id' => $user->id,
                ]);
            }

            return RekapKonsolidasi::create([
                'bulan' => $bulan,
                'tahun' => $tahun,
                'total_pemasukan_semua_bidang' => $totalPemasukan,
                'total_pengeluaran_semua_bidang' => $totalPengeluaran,
                'saldo_akhir_gabungan' => $totalSaldoAkhir,
                'status' => 'final',
                'dibuat_oleh' => $user->id,
            ]);
        });

        // 4. Generate and save PDF document automatically
        $pdfPath = $this->pdfGenerator->generate($bulan, $tahun, $rekap);
        $rekap->update(['pdf_path' => $pdfPath]);

        return redirect()->route('admin.finance.rekap.show', [
            'bulan' => $bulan,
            'tahun' => $tahun
        ])->with('success', 'Rekap Konsolidasi Keuangan Bulanan berhasil digenerate dan dokumen PDF berhasil diterbitkan.');
    }

    public function showKonsolidasi(Request $request): Response
    {
        $bulan = (int)$request->input('bulan', date('m'));
        $tahun = (int)$request->input('tahun', date('Y'));

        $rekap = RekapKonsolidasi::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->firstOrFail();

        // Load summary metrics per bidang for screen preview
        $bidangs = Bidang::orderBy('id', 'asc')->get();
        $bidangData = [];

        foreach ($bidangs as $b) {
            $period = PeriodeLaporan::where('bidang_id', $b->id)
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->first();

            $saldoAwal = $period ? (float)$period->saldo_awal : 0;
            $pemasukan = $period ? (float)$period->transaksi()->where('kategori', 'pemasukan')->sum('jumlah') : 0;
            $pengeluaran = $period ? (float)$period->transaksi()->where('kategori', 'pengeluaran')->sum('jumlah') : 0;
            $saldoAkhir = $saldoAwal + $pemasukan - $pengeluaran;

            $bidangData[] = [
                'nama_bidang' => $b->nama_bidang,
                'saldo_awal' => $saldoAwal,
                'total_pemasukan' => $pemasukan,
                'total_pengeluaran' => $pengeluaran,
                'saldo_akhir' => $saldoAkhir
            ];
        }

        // Detailed transaction listings
        $transactions = Transaksi::with(['bidang', 'subKategori'])
            ->whereHas('periodeLaporan', function ($q) use ($bulan, $tahun) {
                $q->where('bulan', $bulan)->where('tahun', $tahun);
            })
            ->get();

        return Inertia::render('Admin/Finance/Rekap/Show', [
            'rekap' => $rekap,
            'bidangData' => $bidangData,
            'pemasukan' => $transactions->where('kategori', 'pemasukan')->values(),
            'pengeluaran' => $transactions->where('kategori', 'pengeluaran')->values(),
            'selectedBulan' => $bulan,
            'selectedTahun' => $tahun,
        ]);
    }

    public function exportExcel(Request $request)
    {
        $request->validate([
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020|max:2030',
        ]);

        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');

        $fileName = "Rekap_Laporan_Keuangan_AlManar_{$bulan}_{$tahun}.xlsx";

        return Excel::download(new RekapKonsolidasiExport($bulan, $tahun), $fileName);
    }

    public function exportPdf(Request $request)
    {
        $request->validate([
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020|max:2030',
        ]);

        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');

        $rekap = RekapKonsolidasi::where('bulan', $bulan)->where('tahun', $tahun)->firstOrFail();

        if (!$rekap->pdf_path || !Storage::exists(str_replace('storage/', 'public/', $rekap->pdf_path))) {
            // Re-generate if missing
            $pdfPath = $this->pdfGenerator->generate($bulan, $tahun, $rekap);
            $rekap->update(['pdf_path' => $pdfPath]);
        }

        $path = str_replace('storage/', 'public/', $rekap->pdf_path);
        
        return Storage::download($path, "Rekap_Laporan_Keuangan_AlManar_{$bulan}_{$tahun}.pdf");
    }
}
