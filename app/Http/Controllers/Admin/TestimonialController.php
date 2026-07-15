<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TestimonialRequest;
use App\Models\Testimonial;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class TestimonialController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Testimonials/Index', [
            'testimonials' => Testimonial::orderBy('id', 'desc')->get(),
        ]);
    }

    public function store(TestimonialRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['is_featured'] = $request->boolean('is_featured');

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('testimonials', 'public');
            $validated['photo'] = '/storage/' . $path;
        }

        Testimonial::create($validated);

        return redirect()->back()->with('success', 'Testimoni berhasil ditambahkan.');
    }

    public function update(TestimonialRequest $request, Testimonial $testimonial): RedirectResponse
    {
        $validated = $request->validated();
        $validated['is_featured'] = $request->boolean('is_featured');

        if ($request->hasFile('photo')) {
            if ($testimonial->photo) {
                $oldPath = str_replace('/storage/', '', $testimonial->photo);
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('photo')->store('testimonials', 'public');
            $validated['photo'] = '/storage/' . $path;
        }

        $testimonial->update($validated);

        return redirect()->back()->with('success', 'Testimoni berhasil diperbarui.');
    }

    public function destroy(Testimonial $testimonial): RedirectResponse
    {
        if ($testimonial->photo) {
            $oldPath = str_replace('/storage/', '', $testimonial->photo);
            Storage::disk('public')->delete($oldPath);
        }

        $testimonial->delete();

        return redirect()->back()->with('success', 'Testimoni berhasil dihapus.');
    }
}
