@extends('layouts.app')

@php
  $pageTitle = $post->title.' | ZABIDA';
  $pageDescription = $post->excerpt;
@endphp

@section('content')

<article class="max-w-3xl mx-auto px-6 py-16 md:py-24">
  <a href="{{ route('activities.posts') }}" class="inline-flex items-center gap-2 text-sm text-ink/60 hover:text-clay mb-8 transition-colors">
    <span aria-hidden="true">&larr;</span> Back to activities
  </a>

  <p class="font-mono text-xs uppercase tracking-[0.2em] text-ink/50 mb-4">
    {{ $post->published_at->format('F j, Y') }}
    @if ($post->source === 'facebook') &middot; From Facebook @endif
  </p>
  <h1 class="font-serif text-3xl md:text-5xl leading-tight mb-10">{{ $post->title }}</h1>

  {{-- Photo gallery: every image from the original post/album, not just
       the cover — clicking any thumbnail opens the Facebook-style popup
       viewer below with next/prev between all photos in the set. --}}
  @if ($post->images->isNotEmpty())
    <div class="grid {{ $post->images->count() === 1 ? 'grid-cols-1' : 'grid-cols-2 sm:grid-cols-3' }} gap-2 mb-10" id="gallery">
      @foreach ($post->images as $index => $media)
        <button
          type="button"
          class="js-gallery-item relative aspect-square overflow-hidden rounded-lg border border-ink/10 bg-gray-100 focus:outline-none focus:ring-2 focus:ring-clay"
          data-index="{{ $index }}"
          aria-label="View {{ $media->isVideo() ? 'video' : 'photo' }} {{ $index + 1 }} of {{ $post->images->count() }}"
        >
          @if ($media->isVideo())
            <video src="{{ $media->url() }}" class="w-full h-full object-cover" muted></video>
            <span class="absolute inset-0 flex items-center justify-center bg-ink/20">
              <span class="w-10 h-10 rounded-full bg-paper/90 flex items-center justify-center" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 ml-0.5 text-ink" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
              </span>
            </span>
          @else
            <img src="{{ $media->url() }}" alt="" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
          @endif
        </button>
      @endforeach
    </div>
  @elseif ($post->image)
    <img src="{{ $post->coverImageUrl() }}" alt="" class="w-full rounded-lg border border-ink/10 mb-10">
  @endif

  {{-- Video, when the synced post carried one. --}}
  @if ($post->video_url)
    <div class="mb-10">
      <video controls preload="metadata" class="w-full rounded-lg border border-ink/10 bg-ink" poster="{{ $post->coverImageUrl() }}">
        <source src="{{ $post->video_url }}" type="video/mp4">
        Your browser doesn't support embedded video. <a href="{{ $post->video_url }}" class="underline">Watch it directly</a>.
      </video>
    </div>
  @endif

  <div class="prose prose-lg max-w-none text-ink/80 leading-relaxed whitespace-pre-line">{{ $post->body }}</div>

  @if ($post->facebook_permalink)
    <a href="{{ $post->facebook_permalink }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 mt-10 text-sm uppercase tracking-wide border-b border-ink hover:text-clay hover:border-clay transition-colors">
      View original post on Facebook &rarr;
    </a>
  @endif
</article>

{{-- Facebook-style lightbox: full-screen overlay, prev/next between every
     photo in the post's album, closes on Escape/backdrop click. --}}
@if ($post->images->isNotEmpty())
  <div id="lightbox" class="fixed inset-0 z-50 hidden bg-ink/95" role="dialog" aria-modal="true" aria-label="Photo viewer">
    <button type="button" id="lightbox-close" class="absolute top-4 right-4 z-10 w-10 h-10 flex items-center justify-center rounded-full bg-paper/10 hover:bg-paper/20 text-paper transition-colors" aria-label="Close photo viewer">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
    </button>

    <button type="button" id="lightbox-prev" class="absolute left-2 sm:left-6 top-1/2 -translate-y-1/2 z-10 w-10 h-10 flex items-center justify-center rounded-full bg-paper/10 hover:bg-paper/20 text-paper transition-colors" aria-label="Previous photo">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
    </button>
    <button type="button" id="lightbox-next" class="absolute right-2 sm:right-6 top-1/2 -translate-y-1/2 z-10 w-10 h-10 flex items-center justify-center rounded-full bg-paper/10 hover:bg-paper/20 text-paper transition-colors" aria-label="Next photo">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
    </button>

    <div class="h-full flex items-center justify-center p-6 sm:p-16">
      <img id="lightbox-image" src="" alt="" class="max-h-full max-w-full object-contain">
      <video id="lightbox-video" controls class="hidden max-h-full max-w-full"></video>
    </div>
    <p id="lightbox-counter" class="absolute bottom-4 left-1/2 -translate-x-1/2 text-paper/70 text-sm font-mono"></p>
  </div>

  @push('scripts')
  <script>
  (function () {
    var items = @json($post->images->map(fn ($i) => ['url' => $i->url(), 'type' => $i->type])->values());
    var current = 0;
    var lightbox = document.getElementById('lightbox');
    var imageEl = document.getElementById('lightbox-image');
    var videoEl = document.getElementById('lightbox-video');
    var counterEl = document.getElementById('lightbox-counter');
    var lastFocused;

    function show(index) {
      current = (index + items.length) % items.length;
      var item = items[current];

      videoEl.pause();
      if (item.type === 'video') {
        imageEl.classList.add('hidden');
        videoEl.classList.remove('hidden');
        videoEl.src = item.url;
      } else {
        videoEl.classList.add('hidden');
        videoEl.src = '';
        imageEl.classList.remove('hidden');
        imageEl.src = item.url;
        imageEl.alt = 'Photo ' + (current + 1) + ' of ' + items.length;
      }
      counterEl.textContent = (current + 1) + ' / ' + items.length;
    }

    function open(index) {
      lastFocused = document.activeElement;
      show(index);
      lightbox.classList.remove('hidden');
      document.body.classList.add('overflow-hidden');
      document.getElementById('lightbox-close').focus();
    }

    function close() {
      videoEl.pause();
      lightbox.classList.add('hidden');
      document.body.classList.remove('overflow-hidden');
      if (lastFocused) lastFocused.focus();
    }

    document.querySelectorAll('.js-gallery-item').forEach(function (btn) {
      btn.addEventListener('click', function () {
        open(parseInt(btn.dataset.index, 10));
      });
    });

    document.getElementById('lightbox-close').addEventListener('click', close);
    document.getElementById('lightbox-prev').addEventListener('click', function () { show(current - 1); });
    document.getElementById('lightbox-next').addEventListener('click', function () { show(current + 1); });
    lightbox.addEventListener('click', function (e) { if (e.target === lightbox) close(); });

    document.addEventListener('keydown', function (e) {
      if (lightbox.classList.contains('hidden')) return;
      if (e.key === 'Escape') close();
      if (e.key === 'ArrowLeft') show(current - 1);
      if (e.key === 'ArrowRight') show(current + 1);
    });
  })();
  </script>
  @endpush
@endif

@endsection
