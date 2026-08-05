@extends('layouts.admin')

@php $pageTitle = 'Upload Publication | ZABIDA Admin'; @endphp

@section('admin-content')

<h1 class="font-serif text-3xl mb-8">Upload a publication</h1>

<form method="POST" action="{{ route('admin.publications.store') }}" enctype="multipart/form-data" class="max-w-2xl"
  data-async-upload
  data-loading-label="Uploading to archive&hellip;"
  data-fallback-redirect="{{ route('admin.publications.index') }}">
  @include('admin.publications._form')

  <button type="submit" class="bg-ink text-paper px-6 py-3 text-sm uppercase tracking-wide hover:bg-clay transition-colors">
    Upload to archive
  </button>
</form>

@endsection
