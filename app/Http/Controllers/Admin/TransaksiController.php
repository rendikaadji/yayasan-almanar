<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PeriodeLaporan;
use App\Models\RiwayatVerifikasi;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TransaksiController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'periode_laporan_id' => 'required|exists:periode_laporan,id',
            'tanggal' => 'required|date',
            'kategori' => 'required|in:pemasukan,pengeluaran',
            'sub_kategori_id' => 'required|exists:sub_kategori,id',
            'uraian' => 'required|string|max:500',
            'jumlah' => 'required|numeric|min:0.01',
            'pic' => 'nullable|string|max:255',
            'bukti_transaksi' => 'nullable|image|max:2048', // Max 2MB image
            'bidang_tujuan_id' => 'nullable|exists:bidang,id', // Optional target bidang for transfers
        ]);

        $periode = PeriodeLaporan::findOrFail($validated['periode_laporan_id']);

        // Authenticated user role check
        $user = $request->user();
        if ($user->isBendaharaBidang() && (int)$periode->bidang_id !== (int)$user->bidang_id) {
            abort(403, 'Unauthorized access to this period.');
        }

        // Lock validation: prevent adding to submitted/verified reports for standard treasurer
        if ($user->isBendaharaBidang() && $periode->status !== 'draft') {
            return redirect()->back()->with('error', 'Laporan sudah diajukan atau diverifikasi. Tidak dapat menambah transaksi.');
        }

        if ($request->hasFile('bukti_transaksi')) {
            $path = $request->file('bukti_transaksi')->store('bukti_transaksi', 'public');
            $validated['bukti_transaksi'] = '/storage/' . $path;
        }

        $validated['bidang_id'] = $periode->bidang_id;
        $validated['dibuat_oleh'] = $user->id;

        Transaksi::create($validated);

        return redirect()->back()->with('success', 'Transaksi berhasil dicatat.');
    }

    public function update(Request $request, Transaksi $transaksi)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'sub_kategori_id' => 'required|exists:sub_kategori,id',
            'uraian' => 'required|string|max:500',
            'jumlah' => 'required|numeric|min:0.01',
            'pic' => 'nullable|string|max:255',
            'bukti_transaksi_file' => 'nullable|image|max:2048',
            'bidang_tujuan_id' => 'nullable|exists:bidang,id',
        ]);

        $periode = $transaksi->periodeLaporan;
        $user = $request->user();

        if ($user->isBendaharaBidang() && (int)$periode->bidang_id !== (int)$user->bidang_id) {
            abort(403);
        }

        // Edit lock constraints
        if ($user->isBendaharaBidang() && $periode->status !== 'draft') {
            return redirect()->back()->with('error', 'Laporan sudah diajukan atau diverifikasi. Tidak dapat mengubah transaksi.');
        }

        $oldJumlah = (float)$transaksi->jumlah;
        $newJumlah = (float)$validated['jumlah'];

        if ($request->hasFile('bukti_transaksi_file')) {
            if ($transaksi->bukti_transaksi) {
                // Delete old file
                $oldPath = str_replace('/storage/', '', $transaksi->bukti_transaksi);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('bukti_transaksi_file')->store('bukti_transaksi', 'public');
            $transaksi->bukti_transaksi = '/storage/' . $path;
        }

        DB::transaction(function () use ($transaksi, $validated, $periode, $user, $oldJumlah, $newJumlah) {
            $transaksi->update($validated);

            // Log correction if edited by Bendahara Umum during review
            if ($periode->status === 'diajukan' && ($user->isBendaharaUmum() || $user->isAdmin())) {
                RiwayatVerifikasi::create([
                    'periode_laporan_id' => $periode->id,
                    'status' => 'dikoreksi',
                    'catatan' => "Transaksi '{$transaksi->uraian}' dikoreksi oleh Bendahara Umum. Nominal: Rp " . number_format($oldJumlah, 0, ',', '.') . " -> Rp " . number_format($newJumlah, 0, ',', '.'),
                    'oleh_user_id' => $user->id,
                ]);
            }
        });

        return redirect()->back()->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(Request $request, Transaksi $transaksi)
    {
        $periode = $transaksi->periodeLaporan;
        $user = $request->user();

        if ($user->isBendaharaBidang() && (int)$periode->bidang_id !== (int)$user->bidang_id) {
            abort(403);
        }

        if ($user->isBendaharaBidang() && $periode->status !== 'draft') {
            return redirect()->back()->with('error', 'Laporan sudah diajukan atau diverifikasi. Tidak dapat menghapus transaksi.');
        }

        DB::transaction(function () use ($transaksi, $periode, $user) {
            if ($transaksi->bukti_transaksi) {
                $oldPath = str_replace('/storage/', '', $transaksi->bukti_transaksi);
                Storage::disk('public')->delete($oldPath);
            }

            $uraian = $transaksi->uraian;
            $jumlah = (float)$transaksi->jumlah;
            $transaksi->delete();

            // Log correction if deleted by Bendahara Umum during review
            if ($periode->status === 'diajukan' && ($user->isBendaharaUmum() || $user->isAdmin())) {
                RiwayatVerifikasi::create([
                    'periode_laporan_id' => $periode->id,
                    'status' => 'dikoreksi',
                    'catatan' => "Transaksi '{$uraian}' senilai Rp " . number_format($jumlah, 0, ',', '.') . " dihapus oleh Bendahara Umum.",
                    'oleh_user_id' => $user->id,
                ]);
            }
        });

        return redirect()->back()->with('success', 'Transaksi berhasil dihapus.');
    }
}
