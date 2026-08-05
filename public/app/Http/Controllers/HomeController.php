<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Publication;

class HomeController extends Controller
{
    public function index()
    {
        $posts = Post::with('images')->orderByDesc('published_at')->take(6)->get();
        $latestPublications = Publication::orderByDesc('published_at')->take(3)->get();

        return view('home', [
            'posts' => $posts,
            'latestPublications' => $latestPublications,
        ]);
    }
}
