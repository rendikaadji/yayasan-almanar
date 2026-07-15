<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Program;
use App\Models\DonationProgram;
use App\Models\ContactMessage;
use App\Models\Testimonial;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'posts_count' => Post::count(),
                'programs_count' => Program::count(),
                'donation_programs_count' => DonationProgram::count(),
                'unread_messages_count' => ContactMessage::where('is_read', false)->count(),
                'testimonials_count' => Testimonial::count(),
                'total_donations' => (int) DonationProgram::sum('collected_amount'),
            ],
        ]);
    }
}
