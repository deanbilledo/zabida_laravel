@extends('layouts.admin')

@php $pageTitle = 'Facebook Sync | ZABIDA Admin'; @endphp

@section('admin-content')

<h1 class="font-serif text-3xl mb-4">Facebook sync</h1>
<p class="text-ink/60 leading-relaxed max-w-2xl mb-8">
  Pulls recent posts from the ZABIDA Facebook Page, including every photo in
  multi-image albums and any attached video. Runs automatically every hour
  via the server's scheduled task — use the button below to run it right now.
</p>

@unless ($isConfigured)
  <div class="border border-clay/40 bg-clay/10 text-clay px-4 py-3 text-sm mb-8" role="alert">
    Facebook isn't configured yet. Set <code class="font-mono">FACEBOOK_PAGE_ID</code> and
    <code class="font-mono">FACEBOOK_PAGE_TOKEN</code> in your <code class="font-mono">.env</code> file, then reload this page.
  </div>
@endunless

<form method="POST" action="{{ route('admin.facebook.sync') }}" id="sync-form" class="mb-12">
  @csrf
  <button type="submit" id="sync-submit" @disabled(!$isConfigured)
    class="bg-ink text-paper px-6 py-3 text-sm uppercase tracking-wide hover:bg-clay transition-colors disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center gap-2">
    <span data-default-label>Sync now</span>
    <span data-loading-label class="hidden items-center gap-2">
      <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
      Syncing&hellip; downloading photos and video, this may take a minute
    </span>
  </button>
</form>

<h2 class="font-serif text-xl mb-4">Sync history</h2>
<div class="divide-y divide-ink/10">
  @forelse ($logs as $log)
    <div class="flex items-center justify-between py-3 text-sm">
      <div>
        <span class="{{ $log->status === 'error' ? 'text-clay' : 'text-palm' }} font-medium uppercase tracking-wide text-xs mr-3">{{ $log->status }}</span>
        <span class="text-ink/70">{{ $log->message }}</span>
      </div>
      <span class="text-ink/40 font-mono text-xs flex-shrink-0">{{ $log->ran_at->format('M j, Y g:ia') }}</span>
    </div>
  @empty
    <p class="text-ink/50 py-6">No sync runs yet.</p>
  @endforelse
</div>

@push('scripts')
<script>
(function () {
  var form = document.getElementById('sync-form');
  var button = document.getElementById('sync-submit');
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
