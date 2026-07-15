<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProgramRequest;
use App\Models\Program;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class ProgramController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Programs/Index', [
            'programs' => Program::orderBy('id', 'desc')->get(),
        ]);
    }

    public function store(ProgramRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('programs', 'public');
            $validated['image'] = '/storage/' . $path;
        }

        Program::create($validated);

        return redirect()->back()->with('success', 'Program berhasil ditambahkan.');
    }

    public function update(ProgramRequest $request, Program $program): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            if ($program->image) {
                $oldPath = str_replace('/storage/', '', $program->image);
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('image')->store('programs', 'public');
            $validated['image'] = '/storage/' . $path;
        }

        $program->update($validated);

        return redirect()->back()->with('success', 'Program berhasil diperbarui.');
    }

    public function destroy(Program $program): RedirectResponse
    {
        if ($program->image) {
            $oldPath = str_replace('/storage/', '', $program->image);
            Storage::disk('public')->delete($oldPath);
        }

        $program->delete();

        return redirect()->back()->with('success', 'Program berhasil dihapus.');
    }
}
