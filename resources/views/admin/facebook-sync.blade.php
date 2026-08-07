@extends('layouts.admin')

@php $pageTitle = 'Facebook Sync | ZABIDA Admin'; @endphp

@section('admin-content')

<h1 class="font-serif text-3xl mb-4">Facebook sync</h1>
<p class="text-ink/60 leading-relaxed max-w-2xl mb-8">
  Pulls recent posts from the ZABIDA Facebook Page, including every photo in
  multi-image albums and any attached video. Runs automatically every hour
  via the server's scheduled task — use the button below to run it right now.
</p>

@if (session('message'))
  <div class="mb-8 px-4 py-3 text-sm rounded {{ session('status') === 'error' ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-green-50 text-green-700 border border-green-200' }}">
    {{ session('message') }}
  </div>
@endif

@unless ($isConfigured)
  <div class="border border-clay/40 bg-clay/10 text-clay px-4 py-3 text-sm mb-8" role="alert">
    Facebook isn't configured yet.
    @if (auth()->user()->isSuperAdmin())
      Set it up below.
    @else
      Ask a super admin to set it up.
    @endif
  </div>
@endunless

<form method="POST" action="{{ route('admin.facebook.sync') }}" class="mb-12" data-loading-label="Syncing&hellip; downloading photos and video, this may take a minute">
  @csrf
  <button type="submit" @disabled(!$isConfigured)
    class="bg-ink text-paper px-6 py-3 text-sm uppercase tracking-wide hover:bg-clay transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
    Sync now
  </button>
</form>

@if (auth()->user()->isSuperAdmin())
  <div class="mb-12 border-t border-ink/10 pt-8">
    <h2 class="font-serif text-xl mb-2">Facebook credentials</h2>
    <p class="text-ink/60 text-sm mb-6 max-w-2xl">
      Only visible to super admins. The Page Access Token is verified against
      Facebook before it's saved — a bad token is rejected and nothing is
      written. Values are saved directly to the server's <code class="font-mono">.env</code> file.
    </p>

    <form method="POST" action="{{ route('admin.facebook.settings.update') }}" class="max-w-lg" data-loading-label="Verifying token with Facebook&hellip;">
      @csrf

      <div class="mb-5">
        <label for="page_id" class="block text-sm font-medium text-gray-700 mb-2">Page ID <span class="text-red-500">*</span></label>
        <input type="text" name="page_id" id="page_id" class="w-full border-gray-300 rounded shadow-sm focus:border-ink focus:ring-ink font-mono text-sm @error('page_id') border-red-500 @enderror" value="{{ old('page_id', $currentPageId) }}" required>
        @error('page_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
      </div>

      <div class="mb-5">
        <label for="page_token" class="block text-sm font-medium text-gray-700 mb-2">Page Access Token <span class="text-red-500">*</span></label>
        <input type="text" name="page_token" id="page_token" class="w-full border-gray-300 rounded shadow-sm focus:border-ink focus:ring-ink font-mono text-sm @error('page_token') border-red-500 @enderror" placeholder="{{ $currentTokenMasked ?? 'Not set' }}" required>
        <p class="mt-1 text-xs text-ink/50">Current: <span class="font-mono">{{ $currentTokenMasked ?? 'not set' }}</span>. Leave the current value in place unless you're rotating it — paste the new full token here to replace it.</p>
        @error('page_token') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
      </div>

      <div class="mb-5">
        <label for="app_id" class="block text-sm font-medium text-gray-700 mb-2">App ID <span class="text-ink/40">(optional)</span></label>
        <input type="text" name="app_id" id="app_id" class="w-full border-gray-300 rounded shadow-sm focus:border-ink focus:ring-ink font-mono text-sm" value="{{ old('app_id', $currentAppId) }}">
      </div>

      <div class="mb-8">
        <label for="app_secret" class="block text-sm font-medium text-gray-700 mb-2">App Secret <span class="text-ink/40">(optional)</span></label>
        <input type="text" name="app_secret" id="app_secret" class="w-full border-gray-300 rounded shadow-sm focus:border-ink focus:ring-ink font-mono text-sm" placeholder="{{ $currentAppSecretMasked ?? 'Not set' }}">
        <p class="mt-1 text-xs text-ink/50">Current: <span class="font-mono">{{ $currentAppSecretMasked ?? 'not set' }}</span>. Leave blank to keep unchanged.</p>
      </div>

      <button type="submit" class="bg-ink text-paper px-6 py-3 text-sm uppercase tracking-wide hover:bg-clay transition-colors">
        Verify &amp; save
      </button>
    </form>
  </div>
@endif

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