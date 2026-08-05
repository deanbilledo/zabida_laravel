@extends('layouts.app')

@php $pageTitle = 'Admin login | ZABIDA'; @endphp

@section('content')

<section class="max-w-md mx-auto px-6 py-20 md:py-28">
  <p class="font-mono text-xs uppercase tracking-[0.2em] text-ink/50 mb-4">Admin</p>
  <h1 class="font-serif text-3xl md:text-4xl mb-10">Sign in</h1>

  @if (session('auth_notice'))
    <div class="border border-ink/20 bg-ink/5 text-ink px-4 py-3 text-sm mb-6" role="status">
      {{ session('auth_notice') }}
    </div>
  @endif

  @if ($errors->any())
    <div class="border border-clay/40 bg-clay/10 text-clay px-4 py-3 text-sm mb-6" role="alert">
      {{ $errors->first() }}
    </div>
  @endif

  <form method="POST" action="{{ route('admin.login.attempt') }}" id="login-form" novalidate
    data-loading-label="Signing in&hellip;">
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

    <button type="submit"
      class="w-full bg-ink text-paper px-6 py-3.5 text-sm uppercase tracking-wide hover:bg-clay transition-colors">
      Sign in
    </button>
  </form>
</section>

@endsection
