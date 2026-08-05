<!DOCTYPE html>
<html lang="en-PH">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>{{ $pageTitle ?? 'ZABIDA | Zamboanga-Basilan Integrated Development Alliance' }}</title>
<meta name="description" content="{{ $pageDescription ?? 'ZABIDA is a consortium of local NGOs working for peace and development across the Zamboanga Peninsula and Basilan.' }}">
<meta name="theme-color" content="#17303D">
<link rel="icon" href="{{ asset('assets/images/zabida_logo.png') }}" type="image/png">

<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,500&family=Work+Sans:wght@400;500;600&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}?v={{ @filemtime(public_path('assets/css/style.css')) ?: '1' }}">

<script>
tailwind.config = {
  theme: {
    extend: {
      fontFamily: {
        serif: ['Fraunces', 'serif'],
        sans: ['Work Sans', 'sans-serif'],
        mono: ['Space Mono', 'monospace']
      },
      colors: {
        ink: '#17303D',
        paper: '#EEF2EE',
        clay: '#B14A2E',
        gold: '#D9A72B',
        palm: '#3E6B4F',
        violet: '#4A3B7A'
      }
    }
  }
}
</script>
@stack('head')
</head>
<body class="font-sans bg-paper text-ink">

<a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-ink text-paper px-4 py-2 z-50">Skip to main content</a>

<div class="flex h-1.5 w-full" aria-hidden="true">
  <div class="flex-1 bg-gold"></div>
  <div class="flex-1 bg-clay"></div>
  <div class="flex-1 bg-ink"></div>
  <div class="flex-1 bg-palm"></div>
  <div class="flex-1 bg-violet"></div>
</div>

<header role="banner">
  @include('partials.navbar')
</header>

@if (session('status') || session('contact_status'))
  @php
    $flashType = session('status', session('contact_status'));
    $flashMessage = session('message', session('contact_message'));
  @endphp
  <div
    role="status"
    aria-live="polite"
    class="max-w-6xl mx-auto px-6 mt-4"
  >
    <div class="flex items-start gap-3 border px-4 py-3 text-sm
      {{ $flashType === 'error' ? 'border-clay/40 bg-clay/10 text-clay' : 'border-palm/40 bg-palm/10 text-palm' }}">
      <span aria-hidden="true" class="font-mono mt-0.5">{{ $flashType === 'error' ? '!' : '✓' }}</span>
      <p>{{ $flashMessage }}</p>
    </div>
  </div>
@endif

<main id="main-content">
  {{ $slot ?? '' }}
  @yield('content')
</main>

@include('partials.footer')

{{-- Custom confirm/loading modal markup — see assets/js/ui.js --}}
@include('partials.ui-overlay')

<script src="{{ asset('assets/js/app.js') }}?v={{ @filemtime(public_path('assets/js/app.js')) ?: '1' }}"></script>
<script src="{{ asset('assets/js/ui.js') }}?v={{ @filemtime(public_path('assets/js/ui.js')) ?: '1' }}"></script>
@stack('scripts')
</body>
</html>