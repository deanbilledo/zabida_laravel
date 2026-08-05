@extends('layouts.app')

@php
  $pageTitle = 'Activities | ZABIDA';
  $pageDescription = 'Latest updates and activities from ZABIDA and its member NGOs.';
@endphp

@section('content')

<section class="max-w-6xl mx-auto px-6 py-20 md:py-28">
  <p class="font-mono text-xs uppercase tracking-[0.2em] text-ink/50 mb-4">Latest updates</p>
  <h1 class="font-serif text-4xl md:text-5xl mb-6 leading-tight">Activities</h1>
  <p class="text-lg text-ink/70 max-w-2xl leading-relaxed">Field updates, events, and news from ZABIDA and its member organizations — including posts synced directly from our Facebook Page.</p>
</section>

<section class="max-w-6xl mx-auto px-6 pb-20 md:pb-28">
  <div class="divide-y divide-ink/10">
    @forelse ($posts as $post)
      <article class="grid grid-cols-1 sm:grid-cols-[80px_1fr_160px] gap-4 sm:gap-6 items-start py-8">
        <p class="font-mono text-sm text-ink/40">{{ $post->published_at->format('M Y') }}</p>
        <div>
          <h2 class="font-serif text-2xl mb-2 hover:text-clay transition-colors">
            <a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a>
          </h2>
          <p class="text-ink/60 leading-relaxed">{{ $post->excerpt }}</p>
          <div class="flex items-center gap-3 mt-2">
            @if ($post->source === 'facebook')
              <span class="text-xs font-mono uppercase tracking-wide text-ink/40">From Facebook</span>
            @endif
            @if ($post->images->count() > 1)
              <span class="text-xs font-mono uppercase tracking-wide text-ink/40">{{ $post->images->count() }} photos</span>
            @endif
            @if ($post->video_url)
              <span class="text-xs font-mono uppercase tracking-wide text-ink/40">Video</span>
            @endif
          </div>
        </div>
        <div class="aspect-square w-full rounded-lg bg-gray-100 overflow-hidden flex items-center justify-center border border-ink/10">
          <img src="{{ $post->coverImageUrl() }}" alt="" class="w-full h-full object-contain p-1">
        </div>
      </article>
    @empty
      <p class="text-ink/50 py-10">No activities yet &mdash; check back soon, or follow along on <a href="https://www.facebook.com/zabidadotorg/" class="underline hover:text-clay" target="_blank" rel="noopener noreferrer">Facebook</a>.</p>
    @endforelse
  </div>

  @if ($posts->hasPages())
    <div class="mt-10">{{ $posts->links() }}</div>
  @endif
</section>

@endsection
