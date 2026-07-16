<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bidang;
use App\Models\PeriodeLaporan;
use App\Models\RiwayatVerifikasi;
use App\Models\SubKategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class PeriodeLaporanController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        
        if ($user->isBendaharaBidang()) {
            $periods = PeriodeLaporan::where('bidang_id', $user->bidang_id)
                ->orderBy('tahun', 'desc')
                ->orderBy('bulan', 'desc')
                ->get();
            $bidang = $user->bidang;
            $allBidang = [$bidang];
        } else {
            // General treasurer or Admin
            $periods = PeriodeLaporan::with('bidang')
                ->orderBy('tahun', 'desc')
                ->orderBy('bulan', 'desc')
                ->get();
            $allBidang = Bidang::all();
        }

        return Inertia::render('Admin/Finance/Periods/Index', [
            'periods' => $periods,
            'bidangs' => $allBidang
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        
        $rules = [
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020|max:2030',
        ];

        // Only enforce bidang_id if not a bidang treasurer (they are locked to their own bidang)
        if (!$user->isBendaharaBidang()) {
            $rules['bidang_id'] = 'required|exists:bidang,id';
        }

        $validated = $request->validate($rules);
        $bidangId = $user->isBendaharaBidang() ? $user->bidang_id : $validated['bidang_id'];

        // Check if period already exists
        $exists = PeriodeLaporan::where('bidang_id', $bidangId)
            ->where('bulan', $validated['bulan'])
            ->where('tahun', $validated['tahun'])
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Periode laporan untuk bulan dan tahun tersebut sudah ada.');
        }

        // Determine starting balance
        // Look for the preceding month
        $prevMonth = $validated['bulan'] == 1 ? 12 : $validated['bulan'] - 1;
        $prevYear = $validated['bulan'] == 1 ? $validated['tahun'] - 1 : $validated['tahun'];

        $prevPeriod = PeriodeLaporan::where('bidang_id', $bidangId)
            ->where('bulan', $prevMonth)
            ->where('tahun', $prevYear)
            ->first();

        $saldoAwal = 0.00;
        $canInputSaldoAwal = true;

        if ($prevPeriod) {
            $saldoAwal = (float)$prevPeriod->saldo_akhir;
            $canInputSaldoAwal = false;
        } else {
            // Check if there is ANY previous period at all in database to ensure it's truly the "first"
            $hasAnyPastPeriod = PeriodeLaporan::where('bidang_id', $bidangId)->exists();
            if ($hasAnyPastPeriod) {
                // If there are other periods, but we are initializing a gap month, we default to 0
                // or search for the most recent preceding period in history. Let's find the closest past period:
                $closestPastPeriod = PeriodeLaporan::where('bidang_id', $bidangId)
                    ->where(function ($query) use ($validated) {
                        $query->where('tahun', '<', $validated['tahun'])
                              ->orWhere(function ($q) use ($validated) {
                                  $q->where('tahun', $validated['tahun'])
                                    ->where('bulan', '<', $validated['bulan']);
                              });
                    })
                    ->orderBy('tahun', 'desc')
                    ->orderBy('bulan', 'desc')
                    ->first();

                if ($closestPastPeriod) {
                    $saldoAwal = (float)$closestPastPeriod->saldo_akhir;
                    $canInputSaldoAwal = false;
                }
            }
        }

        // If manual input is allowed, check request
        if ($canInputSaldoAwal && $request->has('saldo_awal')) {
            $request->validate(['saldo_awal' => 'required|numeric|min:0']);
            $saldoAwal = (float)$request->input('saldo_awal');
        }

        $period = PeriodeLaporan::create([
            'bidang_id' => $bidangId,
            'bulan' => $validated['bulan'],
            'tahun' => $validated['tahun'],
            'saldo_awal' => $saldoAwal,
            'saldo_akhir' => $saldoAwal,
            'status' => 'draft',
        ]);

        return redirect()->route('admin.finance.periods.show', $period->id)
            ->with('success', 'Periode laporan baru berhasil dibuka.');
    }

    public function show(Request $request, $id): Response
    {
        $user = $request->user();
        $periode = PeriodeLaporan::with(['bidang', 'riwayatVerifikasi.user'])->findOrFail($id);

        // Access enforcement check double-safety
        if ($user->isBendaharaBidang() && (int)$periode->bidang_id !== (int)$user->bidang_id) {
            abort(403, 'Unauthorized access to this period.');
        }

        $transactions = $periode->transaksi()
            ->with(['subKategori', 'pembuat', 'bidangAsal', 'bidangTujuan'])
            ->orderBy('tanggal', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $subKategoris = SubKategori::where('bidang_id', $periode->bidang_id)
            ->where('is_active', true)
            ->get();

        // Get list of other bidangs for transfer options
        $otherBidangs = Bidang::where('id', '!=', $periode->bidang_id)->get();

        // Verify summary values
        $totalPemasukan = $transactions->where('kategori', 'pemasukan')->sum('jumlah');
        $totalPengeluaran = $transactions->where('kategori', 'pengeluaran')->sum('jumlah');

        return Inertia::render('Admin/Finance/Periods/Show', [
            'periode' => $periode,
            'transactions' => $transactions,
            'subKategoris' => $subKategoris,
            'otherBidangs' => $otherBidangs,
            'summary' => [
                'total_pemasukan' => (float)$totalPemasukan,
                'total_pengeluaran' => (float)$totalPengeluaran,
                'saldo_awal' => (float)$periode->saldo_awal,
                'saldo_akhir' => (float)$periode->saldo_akhir,
            ],
            'riwayat' => $periode->riwayatVerifikasi
        ]);
    }

    public function submit(Request $request, $id)
    {
        $user = $request->user();
        $periode = PeriodeLaporan::findOrFail($id);

        if ($user->isBendaharaBidang() && (int)$periode->bidang_id !== (int)$user->bidang_id) {
            abort(403);
        }

        if ($periode->status !== 'draft') {
            return redirect()->back()->with('error', 'Hanya laporan berstatus DRAFT yang dapat diajukan.');
        }

        // Enforce validations: prevent submit of empty transactions
        $hasTransactions = $periode->transaksi()->exists();
        if (!$hasTransactions && $periode->saldo_awal <= 0) {
            return redirect()->back()->with('error', 'Gagal mengajukan. Laporan tidak memiliki data transaksi.');
        }

        DB::transaction(function () use ($periode, $user) {
            $periode->update(['status' => 'diajukan']);

            RiwayatVerifikasi::create([
                'periode_laporan_id' => $periode->id,
                'status' => 'diajukan',
                'catatan' => 'Laporan diajukan oleh Bendahara Bidang',
                'oleh_user_id' => $user->id,
            ]);
        });

        return redirect()->back()->with('success', 'Laporan bulanan berhasil diajukan untuk verifikasi.');
    }
}
