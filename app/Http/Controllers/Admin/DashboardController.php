<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FacebookSyncLog;
use App\Models\Post;
use App\Models\Publication;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'postCount' => Post::count(),
            'publicationCount' => Publication::count(),
            'recentPosts' => Post::orderByDesc('created_at')->take(5)->get(),
            'lastSync' => FacebookSyncLog::orderByDesc('ran_at')->first(),
        ]);
    }
}
