@extends('layouts.admin')

@php $pageTitle = 'Edit Post | ZABIDA Admin'; @endphp

@section('admin-content')

<h1 class="font-serif text-3xl mb-8">Edit journal post</h1>

<form method="POST" action="{{ route('admin.posts.update', $post) }}" enctype="multipart/form-data" class="max-w-2xl"
  data-async-upload
  data-loading-label="Saving changes&hellip;"
  data-fallback-redirect="{{ route('admin.posts.index') }}">
  @include('admin.posts._form')

  <button type="submit" class="bg-ink text-paper px-6 py-3 text-sm uppercase tracking-wide hover:bg-clay transition-colors">
    Save changes
  </button>
</form>

@endsection
