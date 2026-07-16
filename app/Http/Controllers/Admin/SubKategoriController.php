<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bidang;
use App\Models\SubKategori;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SubKategoriController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Finance/SubKategori/Index', [
            'bidangs' => Bidang::orderBy('id', 'asc')->get(),
            'subKategoris' => SubKategori::with('bidang')->orderBy('bidang_id')->orderBy('kategori')->orderBy('nama_sub_kategori')->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bidang_id' => 'required|exists:bidang,id',
            'kategori' => 'required|in:pemasukan,pengeluaran',
            'nama_sub_kategori' => 'required|string|max:255',
        ]);

        SubKategori::create($validated);

        return redirect()->back()->with('success', 'Sub-Kategori baru berhasil ditambahkan.');
    }

    public function update(Request $request, SubKategori $subKategori)
    {
        $validated = $request->validate([
            'nama_sub_kategori' => 'required|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        $subKategori->update($validated);

        return redirect()->back()->with('success', 'Sub-Kategori berhasil diperbarui.');
    }

    public function destroy(SubKategori $subKategori)
    {
        // Safety check: check if there are transactions using this sub_kategori
        $hasTransactions = Transaksi::where('sub_kategori_id', $subKategori->id)->exists();

        if ($hasTransactions) {
            return redirect()->back()->with('error', 'Gagal menghapus. Sub-Kategori sedang digunakan dalam data transaksi.');
        }

        $subKategori->delete();

        return redirect()->back()->with('success', 'Sub-Kategori berhasil dihapus.');
    }
}
