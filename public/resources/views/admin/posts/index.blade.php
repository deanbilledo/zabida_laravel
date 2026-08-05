@extends('layouts.admin')

@php $pageTitle = 'Journal Posts | ZABIDA Admin'; @endphp

@section('admin-content')

<div class="flex items-center justify-between mb-8 flex-wrap gap-4">
  <h1 class="font-serif text-3xl">Journal posts</h1>
  <a href="{{ route('admin.posts.create') }}" class="bg-ink text-paper px-5 py-2.5 text-sm uppercase tracking-wide hover:bg-clay transition-colors">New post</a>
</div>

<div class="divide-y divide-ink/10">
  @forelse ($posts as $post)
    <div class="flex items-center justify-between gap-4 py-4">
      <div>
        <p class="font-serif text-lg">{{ $post->title }}</p>
        <p class="text-sm text-ink/50">
          {{ $post->published_at->format('M j, Y') }}
          &middot; {{ ucfirst($post->source) }}
          @if ($post->images->count() > 1) &middot; {{ $post->images->count() }} photos @endif
        </p>
      </div>
      <div class="flex items-center gap-4 flex-shrink-0">
        <a href="{{ route('posts.show', $post) }}" target="_blank" class="text-sm text-ink/60 hover:text-clay">View</a>
        <a href="{{ route('admin.posts.edit', $post) }}" class="text-sm text-ink/60 hover:text-clay">Edit</a>
        <form method="POST" action="{{ route('admin.posts.destroy', $post) }}" onsubmit="return confirm('Delete &quot;{{ $post->title }}&quot;? This can\'t be undone.');">
          @csrf
          @method('DELETE')
          <button type="submit" class="text-sm text-clay hover:underline">Delete</button>
        </form>
      </div>
    </div>
  @empty
    <p class="text-ink/50 py-10">No posts yet — create one, or run a Facebook sync from the <a href="{{ route('admin.facebook.index') }}" class="underline hover:text-clay">Facebook Sync</a> page.</p>
  @endforelse
</div>

@if ($posts->hasPages())
  <div class="mt-8">{{ $posts->links() }}</div>
@endif

@endsection
