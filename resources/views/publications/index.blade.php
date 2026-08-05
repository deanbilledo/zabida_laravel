@extends('layouts.app')

@php
  $pageTitle = 'PeaceWorks & Knowledge Products | ZABIDA';
  $pageDescription = "Browse and download ZABIDA's magazine issues, knowledge products, and research publications.";
@endphp

@section('content')

<section class="max-w-6xl mx-auto px-6 py-20 md:py-28">
  <p class="font-mono text-xs uppercase tracking-[0.2em] text-ink/50 mb-4">Read</p>
  <h1 class="font-serif text-4xl md:text-5xl mb-6 leading-tight">PeaceWorks &amp; Knowledge Products</h1>
  <p class="text-lg text-ink/70 max-w-2xl leading-relaxed">The archive of ZABIDA's magazine issues, research, and knowledge products — click any title to read it here, or download a copy.</p>

  {{-- Category filter --}}
  <div class="flex flex-wrap gap-2 mt-10" role="group" aria-label="Filter by category">
    <a href="{{ route('publications.index') }}"
      class="px-4 py-1.5 text-sm border {{ !$activeCategory ? 'bg-ink text-paper border-ink' : 'border-ink/20 hover:border-ink text-ink/70' }} transition-colors">
      All
    </a>
    @foreach ($categories as $category)
      <a href="{{ route('publications.index', ['category' => $category]) }}"
        class="px-4 py-1.5 text-sm border {{ $activeCategory === $category ? 'bg-ink text-paper border-ink' : 'border-ink/20 hover:border-ink text-ink/70' }} transition-colors">
        {{ $category }}
      </a>
    @endforeach
  </div>
</section>

<section class="max-w-6xl mx-auto px-6 pb-20 md:pb-28">
  @if ($publications->isEmpty())
    <p class="text-ink/50 py-10">No publications in this category yet.</p>
  @else
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
      @foreach ($publications as $publication)
        <div class="border border-ink/10 hover:border-clay transition-colors flex flex-col">
          <div class="aspect-[3/4] bg-ink/5 flex items-center justify-center overflow-hidden">
            @if ($publication->cover_image)
              <img src="{{ $publication->coverImageUrl() }}" alt="" class="w-full h-full object-cover">
            @else
              <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-ink/20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
            @endif
          </div>
          <div class="p-5 flex flex-col flex-1">
            <p class="font-mono text-xs uppercase tracking-wide text-ink/40 mb-2">{{ $publication->category }} &middot; {{ $publication->published_at->format('M Y') }}</p>
            <h2 class="font-serif text-lg mb-2 leading-snug">{{ $publication->title }}</h2>
            @if ($publication->description)
              <p class="text-sm text-ink/60 leading-relaxed mb-4 line-clamp-3">{{ $publication->description }}</p>
            @endif
            <div class="mt-auto flex items-center gap-4 pt-2">
              <button type="button"
                class="js-pdf-view text-sm uppercase tracking-wide border-b border-ink hover:text-clay hover:border-clay transition-colors"
                data-title="{{ $publication->title }}"
                data-url="{{ route('publications.view', $publication) }}"
              >
                Read
              </button>
              <a href="{{ route('publications.download', $publication) }}" class="text-sm uppercase tracking-wide text-ink/60 hover:text-clay transition-colors inline-flex items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                {{ $publication->formattedSize() }}
              </a>
            </div>
          </div>
        </div>
      @endforeach
    </div>

    <div class="mt-10">{{ $publications->links() }}</div>
  @endif
</section>

{{-- Click-to-popup PDF viewer --}}
<div id="pdf-modal" class="fixed inset-0 z-50 hidden bg-ink/80 backdrop-blur-sm" role="dialog" aria-modal="true" aria-labelledby="pdf-modal-title">
  <div class="h-full flex flex-col p-3 sm:p-8">
    <div class="flex items-center justify-between mb-3 flex-shrink-0">
      <h2 id="pdf-modal-title" class="font-serif text-lg sm:text-xl text-paper truncate pr-4"></h2>
      <div class="flex items-center gap-2 flex-shrink-0">
        <a id="pdf-modal-download" href="#" class="hidden sm:inline-block text-paper/80 hover:text-paper text-sm uppercase tracking-wide border border-paper/30 hover:border-paper px-4 py-2 transition-colors">Download</a>
        <button type="button" id="pdf-modal-close" class="w-10 h-10 flex items-center justify-center rounded-full bg-paper/10 hover:bg-paper/20 text-paper transition-colors" aria-label="Close PDF viewer">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
      </div>
    </div>
    <div class="flex-1 bg-paper rounded-lg overflow-hidden relative">
      <div id="pdf-modal-loading" class="absolute inset-0 flex items-center justify-center text-ink/40 font-mono text-sm">Loading document&hellip;</div>
      <iframe id="pdf-modal-frame" src="" title="PDF document" class="w-full h-full relative z-10 opacity-0 transition-opacity"></iframe>
    </div>
  </div>
</div>

@push('scripts')
<script>
(function () {
  var modal = document.getElementById('pdf-modal');
  var frame = document.getElementById('pdf-modal-frame');
  var titleEl = document.getElementById('pdf-modal-title');
  var loadingEl = document.getElementById('pdf-modal-loading');
  var downloadLink = document.getElementById('pdf-modal-download');
  var closeBtn = document.getElementById('pdf-modal-close');
  var lastFocused;

  function open(url, title) {
    lastFocused = document.activeElement;
    titleEl.textContent = title;
    downloadLink.href = url.replace('/view', '/download');
    loadingEl.classList.remove('hidden');
    frame.classList.remove('opacity-100');
    frame.classList.add('opacity-0');
    frame.src = url;
    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
    closeBtn.focus();
  }

  function close() {
    modal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    frame.src = '';
    if (lastFocused) lastFocused.focus();
  }

  frame.addEventListener('load', function () {
    if (frame.src) {
      loadingEl.classList.add('hidden');
      frame.classList.remove('opacity-0');
      frame.classList.add('opacity-100');
    }
  });

  document.querySelectorAll('.js-pdf-view').forEach(function (btn) {
    btn.addEventListener('click', function () {
      open(btn.dataset.url, btn.dataset.title);
    });
  });

  closeBtn.addEventListener('click', close);
  modal.addEventListener('click', function (e) { if (e.target === modal) close(); });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && !modal.classList.contains('hidden')) close();
  });
})();
</script>
@endpush

@endsection
