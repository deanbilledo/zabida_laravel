@extends('layouts.admin')

@php $pageTitle = 'New Post | ZABIDA Admin'; @endphp

@section('admin-content')

<h1 class="font-serif text-3xl mb-8">New journal post</h1>

{{-- data-async-upload switches this form to XHR submission (real upload
     progress + a genuine Cancel button) instead of a normal page post —
     see assets/js/ui.js. --}}
<form method="POST" action="{{ route('admin.posts.store') }}" enctype="multipart/form-data" class="max-w-2xl"
  data-async-upload
  data-loading-label="Publishing post&hellip;"
  data-fallback-redirect="{{ route('admin.posts.index') }}">
  
  {{-- ADD THIS LINE: This was missing, causing the 419 error! --}}
  @csrf

  @include('admin.posts._form')

  <button type="submit" class="bg-ink text-paper px-6 py-3 text-sm uppercase tracking-wide hover:bg-clay transition-colors">
    Publish post
  </button>
</form>

@endsection