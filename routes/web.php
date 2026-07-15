<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\ProgramController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\DonationProgramController;
use App\Http\Controllers\Admin\DonationReportController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\OrganizationStructureController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return Inertia::render('Home/Index', [
        'programs' => \App\Models\Program::orderBy('id', 'desc')->limit(3)->get(),
        'posts' => \App\Models\Post::with('author:id,name')->orderBy('published_at', 'desc')->limit(3)->get(),
        'donationProgram' => \App\Models\DonationProgram::orderBy('id', 'desc')->first(),
        'testimonials' => \App\Models\Testimonial::where('is_featured', true)->limit(2)->get(),
    ]);
})->name('home');

Route::get('/profil', function () {
    return Inertia::render('Profil/Index', [
        'structureItems' => \App\Models\OrganizationStructure::orderBy('order', 'asc')->get()
    ]);
})->name('profil');

Route::get('/program', function () {
    return Inertia::render('Program/Index', [
        'programs' => \App\Models\Program::orderBy('id', 'desc')->get()
    ]);
})->name('program.index');

Route::get('/program/{slug}', function ($slug) {
    $program = \App\Models\Program::where('slug', $slug)->firstOrFail();
    return Inertia::render('Program/Show', [
        'program' => $program
    ]);
})->name('program.show');

Route::get('/berita', function () {
    return Inertia::render('Berita/Index', [
        'posts' => \App\Models\Post::with('author:id,name')->orderBy('published_at', 'desc')->get()
    ]);
})->name('berita.index');

Route::get('/berita/{slug}', function ($slug) {
    $post = \App\Models\Post::with('author:id,name')->where('slug', $slug)->firstOrFail();
    return Inertia::render('Berita/Show', [
        'post' => $post
    ]);
})->name('berita.show');

Route::get('/galeri', function () {
    return Inertia::render('Galeri/Index', [
        'galleries' => \App\Models\Gallery::with('items')->orderBy('id', 'desc')->get()
    ]);
})->name('galeri');

Route::get('/testimoni', function () {
    return Inertia::render('Testimoni/Index', [
        'testimonials' => \App\Models\Testimonial::orderBy('id', 'desc')->get()
    ]);
})->name('testimoni');

Route::get('/donasi', function () {
    return Inertia::render('Donasi/Index', [
        'donationPrograms' => \App\Models\DonationProgram::orderBy('id', 'desc')->get(),
        'donationReports' => \App\Models\DonationReport::with('donationProgram:id,title')->orderBy('date', 'desc')->get()
    ]);
})->name('donasi.index');

Route::get('/donasi/{slug}', function ($slug) {
    $donationProgram = \App\Models\DonationProgram::where('slug', $slug)->firstOrFail();
    return Inertia::render('Donasi/Show', [
        'donationProgram' => $donationProgram
    ]);
})->name('donasi.show');

Route::get('/kontak', function () {
    return Inertia::render('Kontak/Index');
})->name('kontak');

Route::post('/kontak', function (\Illuminate\Http\Request $request) {
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'max:255'],
        'phone' => ['nullable', 'string', 'max:50'],
        'message' => ['required', 'string'],
    ]);

    \App\Models\ContactMessage::create($validated);

    return redirect()->back()->with('success', 'Pesan Anda berhasil dikirim.');
})->name('kontak.store');

/*
|--------------------------------------------------------------------------
| Admin Panel Routes (Protected)
|--------------------------------------------------------------------------
*/

Route::redirect('/admin', '/admin/dashboard');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('posts', PostController::class)->except(['create', 'show', 'edit']);
    Route::resource('programs', ProgramController::class)->except(['create', 'show', 'edit']);
    Route::resource('galleries', GalleryController::class)->except(['create', 'show', 'edit']);
    Route::resource('testimonials', TestimonialController::class)->except(['create', 'show', 'edit']);
    Route::resource('donation-programs', DonationProgramController::class)->except(['create', 'show', 'edit']);
    Route::resource('donation-reports', DonationReportController::class)->except(['create', 'show', 'edit']);
    Route::resource('organization-structures', OrganizationStructureController::class)->except(['create', 'show', 'edit']);
    
    // Contact Messages
    Route::get('contact-messages', [ContactMessageController::class, 'index'])->name('contact-messages.index');
    Route::patch('contact-messages/{contact_message}/read', [ContactMessageController::class, 'markAsRead'])->name('contact-messages.read');
    Route::delete('contact-messages/{contact_message}', [ContactMessageController::class, 'destroy'])->name('contact-messages.destroy');
});

/*
|--------------------------------------------------------------------------
| Dashboard Bridge Route
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Profile Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
