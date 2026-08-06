@extends('layouts.app')

@section('content')

<div class="max-w-6xl mx-auto px-6 py-10">
  <div class="flex flex-wrap items-center justify-between gap-4 mb-10 border-b border-ink/10 pb-6">
    <nav class="flex flex-wrap gap-6 text-sm uppercase tracking-wide" aria-label="Admin navigation">
      <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'text-clay' : 'text-ink/70 hover:text-clay' }} transition-colors">Dashboard</a>
      <a href="{{ route('admin.posts.index') }}" class="{{ request()->routeIs('admin.posts.*') ? 'text-clay' : 'text-ink/70 hover:text-clay' }} transition-colors">Journal Posts</a>
      <a href="{{ route('admin.publications.index') }}" class="{{ request()->routeIs('admin.publications.*') ? 'text-clay' : 'text-ink/70 hover:text-clay' }} transition-colors">PeaceWorks Archive</a>
      <a href="{{ route('admin.facebook.index') }}" class="{{ request()->routeIs('admin.facebook.*') ? 'text-clay' : 'text-ink/70 hover:text-clay' }} transition-colors">Facebook Sync</a>
      <a href="{{ route('admin.facebook.index') }}" class="{{ request()->routeIs('admin.facebook.*') ? 'text-clay' : 'text-ink/70 hover:text-clay' }} transition-colors">Facebook Sync</a>
      @if (auth()->user()->isSuperAdmin())
        <a href="{{ route('admin.admins.index') }}" class="{{ request()->routeIs('admin.admins.*') ? 'text-clay' : 'text-ink/70 hover:text-clay' }} transition-colors">Admins</a>
      @endif
    </nav>

    <form method="POST" action="{{ route('admin.logout') }}" data-loading-label="Signing out&hellip;">
      @csrf
      <button type="submit" class="text-sm uppercase tracking-wide text-ink/60 hover:text-clay transition-colors">
        Sign out
      </button>
    </form>
  </div>

  @yield('admin-content')
</div>

@endsection
