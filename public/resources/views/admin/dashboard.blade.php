@extends('layouts.admin')

@php $pageTitle = 'Admin Dashboard | ZABIDA'; $currentPage = 'admin'; @endphp

@section('admin-content')

<h1 class="font-serif text-3xl mb-8">Dashboard</h1>

<div class="grid sm:grid-cols-3 gap-6 mb-12">
  <div class="border border-ink/10 p-6">
    <p class="font-mono text-xs uppercase tracking-wide text-ink/40 mb-2">Journal posts</p>
    <p class="font-serif text-4xl">{{ $postCount }}</p>
  </div>
  <div class="border border-ink/10 p-6">
    <p class="font-mono text-xs uppercase tracking-wide text-ink/40 mb-2">Publications</p>
    <p class="font-serif text-4xl">{{ $publicationCount }}</p>
  </div>
  <div class="border border-ink/10 p-6">
    <p class="font-mono text-xs uppercase tracking-wide text-ink/40 mb-2">Last Facebook sync</p>
    @if ($lastSync)
      <p class="font-serif text-lg {{ $lastSync->status === 'error' ? 'text-clay' : '' }}">
        {{ $lastSync->status === 'success' ? 'OK' : 'Error' }} &middot; {{ $lastSync->ran_at->diffForHumans() }}
      </p>
    @else
      <p class="text-ink/50 text-sm">Never run yet</p>
    @endif
  </div>
</div>

<div class="flex flex-wrap gap-3 mb-12">
  <a href="{{ route('admin.posts.create') }}" class="bg-ink text-paper px-5 py-2.5 text-sm uppercase tracking-wide hover:bg-clay transition-colors">New journal post</a>
  <a href="{{ route('admin.publications.create') }}" class="border border-ink px-5 py-2.5 text-sm uppercase tracking-wide hover:bg-ink hover:text-paper transition-colors">Upload publication</a>
</div>

<h2 class="font-serif text-xl mb-4">Recent posts</h2>
<div class="divide-y divide-ink/10">
  @forelse ($recentPosts as $post)
    <div class="flex items-center justify-between py-3">
      <span>{{ $post->title }}</span>
      <a href="{{ route('admin.posts.edit', $post) }}" class="text-sm text-ink/60 hover:text-clay">Edit</a>
    </div>
  @empty
    <p class="text-ink/50 py-4">No posts yet.</p>
  @endforelse
</div>

@endsection
