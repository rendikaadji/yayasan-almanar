<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DonationProgramRequest;
use App\Models\DonationProgram;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class DonationProgramController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/DonationPrograms/Index', [
            'donationPrograms' => DonationProgram::orderBy('id', 'desc')->get(),
        ]);
    }

    public function store(DonationProgramRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('donations', 'public');
            $validated['image'] = '/storage/' . $path;
        }

        DonationProgram::create($validated);

        return redirect()->back()->with('success', 'Program donasi berhasil ditambahkan.');
    }

    public function update(DonationProgramRequest $request, DonationProgram $donationProgram): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            if ($donationProgram->image) {
                $oldPath = str_replace('/storage/', '', $donationProgram->image);
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('image')->store('donations', 'public');
            $validated['image'] = '/storage/' . $path;
        }

        $donationProgram->update($validated);

        return redirect()->back()->with('success', 'Program donasi berhasil diperbarui.');
    }

    public function destroy(DonationProgram $donationProgram): RedirectResponse
    {
        if ($donationProgram->image) {
            $oldPath = str_replace('/storage/', '', $donationProgram->image);
            Storage::disk('public')->delete($oldPath);
        }

        $donationProgram->delete();

        return redirect()->back()->with('success', 'Program donasi berhasil dihapus.');
    }
}
