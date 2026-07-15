<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GalleryRequest;
use App\Models\Gallery;
use App\Models\GalleryItem;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class GalleryController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Galleries/Index', [
            'galleries' => Gallery::with('items')->orderBy('id', 'desc')->get(),
        ]);
    }

    public function store(GalleryRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('galleries', 'public');
            $validated['cover_image'] = '/storage/' . $path;
        }

        $gallery = Gallery::create([
            'album_name' => $validated['album_name'],
            'cover_image' => $validated['cover_image'] ?? null,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $itemPath = $image->store('gallery_items', 'public');
                GalleryItem::create([
                    'gallery_id' => $gallery->id,
                    'file_path' => '/storage/' . $itemPath,
                    'type' => 'image',
                ]);
            }
        }

        return redirect()->back()->with('success', 'Album galeri berhasil dibuat.');
    }

    public function update(GalleryRequest $request, Gallery $gallery): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('cover_image')) {
            if ($gallery->cover_image) {
                $oldPath = str_replace('/storage/', '', $gallery->cover_image);
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('cover_image')->store('galleries', 'public');
            $gallery->cover_image = '/storage/' . $path;
        }

        $gallery->album_name = $validated['album_name'];
        $gallery->save();

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $itemPath = $image->store('gallery_items', 'public');
                GalleryItem::create([
                    'gallery_id' => $gallery->id,
                    'file_path' => '/storage/' . $itemPath,
                    'type' => 'image',
                ]);
            }
        }

        return redirect()->back()->with('success', 'Album galeri berhasil diperbarui.');
    }

    public function destroy(Gallery $gallery): RedirectResponse
    {
        // Delete cover image
        if ($gallery->cover_image) {
            $oldPath = str_replace('/storage/', '', $gallery->cover_image);
            Storage::disk('public')->delete($oldPath);
        }

        // Delete all items
        foreach ($gallery->items as $item) {
            $oldItemPath = str_replace('/storage/', '', $item->file_path);
            Storage::disk('public')->delete($oldItemPath);
            $item->delete();
        }

        $gallery->delete();

        return redirect()->back()->with('success', 'Album galeri berhasil dihapus.');
    }
}
