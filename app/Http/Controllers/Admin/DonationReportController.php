<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DonationReportRequest;
use App\Models\DonationReport;
use App\Models\DonationProgram;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class DonationReportController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/DonationReports/Index', [
            'donationReports' => DonationReport::with('donationProgram:id,title')->orderBy('date', 'desc')->get(),
            'donationPrograms' => DonationProgram::select('id', 'title')->orderBy('title', 'asc')->get(),
        ]);
    }

    public function store(DonationReportRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DonationReport::create($validated);

        return redirect()->back()->with('success', 'Laporan penggunaan dana berhasil ditambahkan.');
    }

    public function update(DonationReportRequest $request, DonationReport $donationReport): RedirectResponse
    {
        $validated = $request->validated();

        $donationReport->update($validated);

        return redirect()->back()->with('success', 'Laporan penggunaan dana berhasil diperbarui.');
    }

    public function destroy(DonationReport $donationReport): RedirectResponse
    {
        $donationReport->delete();

        return redirect()->back()->with('success', 'Laporan penggunaan dana berhasil dihapus.');
    }
}
