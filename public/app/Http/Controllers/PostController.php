<?php

namespace App\Http\Controllers;

use App\Models\Post;

class PostController extends Controller
{
    public function show(Post $post)
    {
        $post->load('images');

        return view('posts.show', [
            'post' => $post,
            'currentPage' => 'journal',
        ]);
    }
}
