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
    Facebook isn't configured yet. Check your <code class="font-mono">.env</code> file, then reload this page.
  </div>
@endunless

<form method="POST" action="{{ route('admin.facebook.sync') }}" class="mb-12" data-loading-label="Syncing&hellip; downloading photos and video, this may take a minute">
  @csrf
  <button type="submit" @disabled(!$isConfigured)
    class="bg-ink text-paper px-6 py-3 text-sm uppercase tracking-wide hover:bg-clay transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
    Sync now
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

@endsection
