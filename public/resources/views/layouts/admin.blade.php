@extends('layouts.app')

@section('content')

<div class="max-w-6xl mx-auto px-6 py-10">
  <div class="flex flex-wrap items-center justify-between gap-4 mb-10 border-b border-ink/10 pb-6">
    <nav class="flex flex-wrap gap-6 text-sm uppercase tracking-wide" aria-label="Admin navigation">
      <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'text-clay' : 'text-ink/70 hover:text-clay' }} transition-colors">Dashboard</a>
      <a href="{{ route('admin.posts.index') }}" class="{{ request()->routeIs('admin.posts.*') ? 'text-clay' : 'text-ink/70 hover:text-clay' }} transition-colors">Journal Posts</a>
      <a href="{{ route('admin.publications.index') }}" class="{{ request()->routeIs('admin.publications.*') ? 'text-clay' : 'text-ink/70 hover:text-clay' }} transition-colors">PeaceWorks Archive</a>
      <a href="{{ route('admin.facebook.index') }}" class="{{ request()->routeIs('admin.facebook.*') ? 'text-clay' : 'text-ink/70 hover:text-clay' }} transition-colors">Facebook Sync</a>
    </nav>

    {{-- Fixed sign-out: a real POST form with CSRF protection and a
         visible "signing out" state, instead of the original's plain
         <a href="logout.php"> link. --}}
    <form method="POST" action="{{ route('admin.logout') }}" id="logout-form">
      @csrf
      <button type="submit" id="logout-submit" class="text-sm uppercase tracking-wide text-ink/60 hover:text-clay transition-colors inline-flex items-center gap-2 disabled:opacity-60">
        <span data-default-label>Sign out</span>
        <span data-loading-label class="hidden items-center gap-2">
          <svg class="animate-spin h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
          </svg>
          Signing out&hellip;
        </span>
      </button>
    </form>
  </div>

  @yield('admin-content')
</div>

@push('scripts')
<script>
(function () {
  var form = document.getElementById('logout-form');
  var button = document.getElementById('logout-submit');
  if (!form) return;
  form.addEventListener('submit', function () {
    if (button.disabled) return;
    button.disabled = true;
    button.querySelector('[data-default-label]').classList.add('hidden');
    var loading = button.querySelector('[data-loading-label]');
    loading.classList.remove('hidden');
    loading.classList.add('inline-flex');
  });
})();
</script>
@endpush

@endsection
