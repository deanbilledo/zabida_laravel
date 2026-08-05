<?php

namespace App\Http\Controllers;

use App\Models\Post;

class ActivitiesController extends Controller
{
    // Programs page (formerly activities.php)
    public function programs()
    {
        return view('activities', ['currentPage' => 'activities']);
    }

    // Activities/journal listing (formerly activities-post.php)
    public function activities()
    {
        $posts = Post::with('images')->orderByDesc('published_at')->paginate(9);

        return view('activities-post', [
            'posts' => $posts,
            'currentPage' => 'activities-post',
        ]);
    }
}
