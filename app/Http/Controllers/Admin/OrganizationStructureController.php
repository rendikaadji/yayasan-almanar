<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OrganizationStructureRequest;
use App\Models\OrganizationStructure;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class OrganizationStructureController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/OrganizationStructure/Index', [
            'structureItems' => OrganizationStructure::orderBy('order', 'asc')->get(),
        ]);
    }

    public function store(OrganizationStructureRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('structure', 'public');
            $validated['photo'] = '/storage/' . $path;
        }

        OrganizationStructure::create($validated);

        return redirect()->back()->with('success', 'Anggota pengurus berhasil ditambahkan.');
    }

    public function update(OrganizationStructureRequest $request, OrganizationStructure $organizationStructure): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('photo')) {
            if ($organizationStructure->photo) {
                $oldPath = str_replace('/storage/', '', $organizationStructure->photo);
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('photo')->store('structure', 'public');
            $validated['photo'] = '/storage/' . $path;
        }

        $organizationStructure->update($validated);

        return redirect()->back()->with('success', 'Data pengurus berhasil diperbarui.');
    }

    public function destroy(OrganizationStructure $organizationStructure): RedirectResponse
    {
        if ($organizationStructure->photo) {
            $oldPath = str_replace('/storage/', '', $organizationStructure->photo);
            Storage::disk('public')->delete($oldPath);
        }

        $organizationStructure->delete();

        return redirect()->back()->with('success', 'Data pengurus berhasil dihapus.');
    }
}
