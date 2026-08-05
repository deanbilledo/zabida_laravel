@php
  $currentPage = $currentPage ?? 'home';
  $navClass = fn (string $page) => 'nav-link hover:text-clay transition-colors'.($page === $currentPage ? ' active' : '');
  // A plain "#about" hash-only link lets the browser's native smooth-scroll
  // (see scroll-behavior in style.css) animate to the section in place.
  // Using a full "/#about" URL — even on the home page itself — forces a
  // full page reload first, which is what made this feel abrupt/unsmooth.
  $anchor = fn (string $id) => $currentPage === 'home' ? "#{$id}" : route('home')."#{$id}";
@endphp
<nav id="navbar" class="sticky top-0 z-50 bg-paper/95 backdrop-blur-sm border-b border-ink/10 w-full" role="navigation" aria-label="Main navigation">
  <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
    <a href="{{ route('home') }}" class="flex items-center gap-3" aria-label="ZABIDA home">
      <img src="{{ asset('assets/images/zabida_logo.png') }}" alt="ZABIDA logo" width="48" height="48" class="h-9 w-auto object-contain" onerror="this.style.display='none'">
      <span class="font-serif font-medium text-lg tracking-tight">ZABIDA</span>
    </a>

    <ul class="hidden lg:flex items-center gap-8 text-sm tracking-wide uppercase">
      <li><a href="{{ $anchor('about') }}" class="{{ $navClass('home') }}">About</a></li>
      <li><a href="{{ $anchor('partners') }}" class="{{ $navClass('partners') }}">Member NGOs</a></li>
      <li><a href="{{ route('activities') }}" class="{{ $navClass('activities') }}">Programs</a></li>
      <li><a href="{{ route('activities.posts') }}" class="{{ $navClass('activities-post') }}">Activities</a></li>
      <li><a href="{{ route('publications.index') }}" class="{{ $navClass('peaceworks') }}">PeaceWorks &amp; Knowledge Products</a></li>
      <li><a href="{{ route('contact') }}" class="border border-ink px-4 py-1.5 hover:bg-ink hover:text-paper transition-colors {{ $currentPage === 'contact' ? 'bg-ink text-paper' : '' }}">Contact</a></li>
    </ul>

    <button class="lg:hidden mobile-menu p-2" aria-label="Toggle menu" aria-expanded="false" aria-controls="mobile-nav-links">
      <div class="w-6 h-5 flex flex-col justify-between">
        <span class="w-full h-0.5 bg-ink"></span>
        <span class="w-full h-0.5 bg-ink"></span>
        <span class="w-full h-0.5 bg-ink"></span>
      </div>
    </button>
  </div>

  <div id="mobile-nav-links" class="lg:hidden nav-links hidden border-t border-ink/10 px-6 py-4 bg-paper">
    <div class="flex flex-col gap-1 text-sm uppercase tracking-wide">
      <a href="{{ $anchor('about') }}" class="py-2.5 {{ $navClass('home') }}">About</a>
      <a href="{{ $anchor('partners') }}" class="py-2.5 {{ $navClass('partners') }}">Member NGOs</a>
      <a href="{{ route('activities') }}" class="py-2.5 {{ $navClass('activities') }}">Programs</a>
      <a href="{{ route('activities.posts') }}" class="py-2.5 {{ $navClass('activities-post') }}">Activities</a>
      <a href="{{ route('publications.index') }}" class="py-2.5 {{ $navClass('peaceworks') }}">PeaceWorks &amp; Knowledge Products</a>
      <a href="{{ route('contact') }}" class="py-2.5 font-semibold {{ $currentPage === 'contact' ? 'text-clay' : '' }}">Contact</a>
    </div>
  </div>
</nav>
