<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class ContactMessageController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/ContactMessages/Index', [
            'contactMessages' => ContactMessage::orderBy('id', 'desc')->get(),
        ]);
    }

    public function markAsRead(ContactMessage $contactMessage): RedirectResponse
    {
        $contactMessage->update(['is_read' => true]);

        return redirect()->back()->with('success', 'Pesan ditandai sebagai dibaca.');
    }

    public function destroy(ContactMessage $contactMessage): RedirectResponse
    {
        $contactMessage->delete();

        return redirect()->back()->with('success', 'Pesan berhasil dihapus.');
    }
}
