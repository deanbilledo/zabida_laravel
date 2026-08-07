<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    protected array $videoMimes = ['mp4', 'mov', 'webm', 'ogg'];

    public function index()
    {
        $posts = Post::with('images')->orderByDesc('published_at')->paginate(15);

        return view('admin.posts.index', ['posts' => $posts]);
    }

    public function create()
    {
        return view('admin.posts.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);
        $post = Post::create($validated);

        $this->storeMedia($request, $post);

        return redirect()->route('admin.posts.index')
            ->with('status', 'success')
            ->with('message', '"'.$post->title.'" was published.');
    }

    public function edit(Post $post)
    {
        $post->load('images');

        return view('admin.posts.edit', ['post' => $post]);
    }

    public function update(Request $request, Post $post)
    {
        $validated = $this->validated($request, $post);
        $post->update($validated);

        $this->storeMedia($request, $post);

        return redirect()->route('admin.posts.index')
            ->with('status', 'success')
            ->with('message', '"'.$post->title.'" was updated.');
    }

    // Removes one image/video from a post without deleting the whole post —
    // used by the "x" on each thumbnail in the edit form.
    public function destroyMedia(Post $post, PostImage $media)
    {
        abort_unless($media->post_id === $post->id, 404);

        Storage::disk('public')->delete($media->path);
        $media->delete();

        if ($post->image === $media->path) {
            $next = $post->images()->where('type', 'image')->first();
            $post->update(['image' => $next?->path]);
        }

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['status' => 'success', 'message' => 'Media removed.']);
        }

        return back()
            ->with('status', 'success')
            ->with('message', 'Media removed.');
    }

    public function destroy(Post $post)
    {
        $title = $post->title;

        foreach ($post->images as $image) {
            Storage::disk('public')->delete($image->path);
        }

        $post->delete();

        return redirect()->route('admin.posts.index')
            ->with('status', 'success')
            ->with('message', '"'.$title.'" was deleted.');
    }

    /**
     * Store every uploaded media[] file (photos and video clips, in any
     * mix and any order) as a PostImage row, and set the post's cover to
     * the first photo if it doesn't have one yet.
     */
    protected function storeMedia(Request $request, Post $post): void
    {
        if (! $request->hasFile('media')) {
            return;
        }

        $position = $post->images()->max('position') + 1;

        foreach ($request->file('media') as $file) {
            if (! $file->isValid()) {
                continue;
            }

            $isVideo = in_array(strtolower($file->getClientOriginalExtension()), $this->videoMimes, true);
            $path = $file->store('posts/manual', 'public');

            PostImage::create([
                'post_id' => $post->id,
                'path' => $path,
                'type' => $isVideo ? 'video' : 'image',
                'position' => $position++,
            ]);

            if (! $isVideo && ! $post->image) {
                $post->update(['image' => $path]);
            }
        }
    }

    protected function validated(Request $request, ?Post $post = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['nullable', 'string'],
            'published_at' => ['required', 'date'],
            // Every file in media[] can be a photo OR a video clip — mixed
            // freely, any number of them, per the brief.
            'media' => ['nullable', 'array', 'max:10'],
            'media.*' => ['file', 'mimes:jpg,jpeg,png,webp,mp4,mov,webm,ogg', 'max:51200'], // 50MB cap per file (covers short video clips)
        ], [
            'title.required' => 'Please give the post a title.',
            'published_at.required' => 'Please set a publish date.',
            'media.max' => 'Please upload 10 files or fewer at a time.',
            'media.*.mimes' => 'Only images (JPG, PNG, WEBP) and video clips (MP4, MOV, WEBM, OGG) can be uploaded here.',
            'media.*.max' => 'Each file must be under 50MB.',
        ]) + ['source' => $post->source ?? 'manual'];
    }
}
