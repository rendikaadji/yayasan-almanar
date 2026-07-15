<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PostRequest;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class PostController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Posts/Index', [
            'posts' => Post::with('author:id,name')->orderBy('id', 'desc')->get(),
        ]);
    }

    public function store(PostRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('posts', 'public');
            $validated['thumbnail'] = '/storage/' . $path;
        }

        $validated['author_id'] = auth()->id();

        Post::create($validated);

        return redirect()->back()->with('success', 'Berita berhasil diterbitkan.');
    }

    public function update(PostRequest $request, Post $post): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail
            if ($post->thumbnail) {
                $oldPath = str_replace('/storage/', '', $post->thumbnail);
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('thumbnail')->store('posts', 'public');
            $validated['thumbnail'] = '/storage/' . $path;
        }

        $post->update($validated);

        return redirect()->back()->with('success', 'Berita berhasil diperbarui.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        if ($post->thumbnail) {
            $oldPath = str_replace('/storage/', '', $post->thumbnail);
            Storage::disk('public')->delete($oldPath);
        }

        $post->delete();

        return redirect()->back()->with('success', 'Berita berhasil dihapus.');
    }
}
