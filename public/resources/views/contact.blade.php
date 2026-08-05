@extends('layouts.app')

@php
  $pageTitle = 'Contact | ZABIDA';
  $pageDescription = 'Get in touch with ZABIDA — Zamboanga-Basilan Integrated Development Alliance, Inc.';
@endphp

@section('content')

<section class="max-w-6xl mx-auto px-6 py-20 md:py-28 grid lg:grid-cols-2 gap-16">
  <div>
    <p class="font-mono text-xs uppercase tracking-[0.2em] text-ink/50 mb-4">Get in touch</p>
    <h1 class="font-serif text-4xl md:text-5xl mb-8 leading-tight">Let's talk</h1>
    <div class="space-y-6 text-ink/75 leading-relaxed">
      <p>Whether you're a partner organization, a funder, or a community member with a question — we'd like to hear from you.</p>
      <dl class="space-y-4 text-base">
        <div>
          <dt class="font-mono text-xs uppercase tracking-wide text-ink/40">Address</dt>
          <dd>Macrohon Compound, Suterville, San Jose Gusu, Zamboanga City</dd>
        </div>
        <div>
          <dt class="font-mono text-xs uppercase tracking-wide text-ink/40">Phone</dt>
          <dd><a href="tel:0629902410" class="hover:text-clay">0629902410</a></dd>
        </div>
        <div>
          <dt class="font-mono text-xs uppercase tracking-wide text-ink/40">Email</dt>
          <dd><a href="mailto:zabidamail.ph@gmail.com" class="hover:text-clay">zabidamail.ph@gmail.com</a></dd>
        </div>
      </dl>
    </div>
  </div>

  <div>
    <form
      method="POST"
      action="{{ route('contact.submit') }}"
      id="contact-form"
      novalidate
      aria-describedby="contact-form-help"
    >
      @csrf

      {{-- Honeypot: hidden from real users via CSS, catches basic bots.
           A visible label is kept for screen reader users who override
           visual styling — tabindex -1 keeps it out of the tab order. --}}
      <div class="absolute -left-[9999px]" aria-hidden="true">
        <label for="website">Leave this field empty</label>
        <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
      </div>

      <p id="contact-form-help" class="text-sm text-ink/50 mb-6">Fields marked with an asterisk are required.</p>

      <div class="mb-6">
        <label for="name" class="block text-sm font-medium mb-2">Name <span class="text-clay" aria-hidden="true">*</span></label>
        <input type="text" name="name" id="name" required value="{{ old('name') }}"
          class="w-full border border-ink/20 px-4 py-3 bg-white focus:border-clay focus:ring-1 focus:ring-clay outline-none transition-colors"
          @if ($errors->has('name')) aria-invalid="true" aria-describedby="name-error" @endif>
        @error('name')
          <p id="name-error" class="text-clay text-sm mt-1.5" role="alert">{{ $message }}</p>
        @enderror
      </div>

      <div class="mb-6">
        <label for="email" class="block text-sm font-medium mb-2">Email <span class="text-clay" aria-hidden="true">*</span></label>
        <input type="email" name="email" id="email" required value="{{ old('email') }}"
          class="w-full border border-ink/20 px-4 py-3 bg-white focus:border-clay focus:ring-1 focus:ring-clay outline-none transition-colors"
          @if ($errors->has('email')) aria-invalid="true" aria-describedby="email-error" @endif>
        @error('email')
          <p id="email-error" class="text-clay text-sm mt-1.5" role="alert">{{ $message }}</p>
        @enderror
      </div>

      <div class="mb-8">
        <label for="message" class="block text-sm font-medium mb-2">Message <span class="text-clay" aria-hidden="true">*</span></label>
        <textarea name="message" id="message" rows="5" required
          class="w-full border border-ink/20 px-4 py-3 bg-white focus:border-clay focus:ring-1 focus:ring-clay outline-none transition-colors"
          @if ($errors->has('message')) aria-invalid="true" aria-describedby="message-error" @endif>{{ old('message') }}</textarea>
        @error('message')
          <p id="message-error" class="text-clay text-sm mt-1.5" role="alert">{{ $message }}</p>
        @enderror
      </div>

      <button type="submit" id="contact-submit"
        class="w-full sm:w-auto bg-ink text-paper px-8 py-3.5 text-sm uppercase tracking-wide hover:bg-clay transition-colors disabled:opacity-60 disabled:cursor-not-allowed inline-flex items-center justify-center gap-2">
        <span data-default-label>Send message</span>
        <span data-loading-label class="hidden items-center gap-2">
          <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
          </svg>
          Sending&hellip;
        </span>
      </button>
    </form>
  </div>
</section>

@push('scripts')
<script>
// Visible loading state on submit — the button disables and swaps its
// label so the person gets immediate feedback instead of wondering if
// the click registered, while the real submit still happens normally.
(function () {
  var form = document.getElementById('contact-form');
  var button = document.getElementById('contact-submit');
  if (!form || !button) return;

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
