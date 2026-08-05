@extends('layouts.app')

@php $pageTitle = 'Admin login | ZABIDA'; @endphp

@section('content')

<section class="max-w-md mx-auto px-6 py-20 md:py-28">
  <p class="font-mono text-xs uppercase tracking-[0.2em] text-ink/50 mb-4">Admin</p>
  <h1 class="font-serif text-3xl md:text-4xl mb-10">Sign in</h1>

  @if ($errors->any())
    <div class="border border-clay/40 bg-clay/10 text-clay px-4 py-3 text-sm mb-6" role="alert">
      {{ $errors->first() }}
    </div>
  @endif

  <form method="POST" action="{{ route('admin.login.attempt') }}" id="login-form" novalidate>
    @csrf

    <div class="mb-6">
      <label for="email" class="block text-sm font-medium mb-2">Email</label>
      <input type="email" name="email" id="email" required autofocus value="{{ old('email') }}"
        class="w-full border border-ink/20 px-4 py-3 bg-white focus:border-clay focus:ring-1 focus:ring-clay outline-none transition-colors"
        @if ($errors->has('email')) aria-invalid="true" @endif>
    </div>

    <div class="mb-4">
      <label for="password" class="block text-sm font-medium mb-2">Password</label>
      <input type="password" name="password" id="password" required
        class="w-full border border-ink/20 px-4 py-3 bg-white focus:border-clay focus:ring-1 focus:ring-clay outline-none transition-colors">
    </div>

    <label class="flex items-center gap-2 text-sm text-ink/70 mb-8">
      <input type="checkbox" name="remember" class="rounded border-ink/30"> Keep me signed in
    </label>

    <button type="submit" id="login-submit"
      class="w-full bg-ink text-paper px-6 py-3.5 text-sm uppercase tracking-wide hover:bg-clay transition-colors disabled:opacity-60 disabled:cursor-not-allowed inline-flex items-center justify-center gap-2">
      <span data-default-label>Sign in</span>
      <span data-loading-label class="hidden items-center gap-2">
        <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
        Signing in&hellip;
      </span>
    </button>
  </form>
</section>

@push('scripts')
<script>
(function () {
  var form = document.getElementById('login-form');
  var button = document.getElementById('login-submit');
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
