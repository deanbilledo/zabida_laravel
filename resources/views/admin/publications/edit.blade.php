@extends('layouts.admin')

@php $pageTitle = 'Edit Publication | ZABIDA Admin'; @endphp

@section('admin-content')

<h1 class="font-serif text-3xl mb-8">Edit publication</h1>

<form method="POST" action="{{ route('admin.publications.update', $publication) }}" enctype="multipart/form-data" class="max-w-2xl"
  data-async-upload
  data-loading-label="Saving changes&hellip;"
  data-fallback-redirect="{{ route('admin.publications.index') }}">
  @csrf
  @method('PUT')
  @include('admin.publications._form')

  <button type="submit" class="bg-ink text-paper px-6 py-3 text-sm uppercase tracking-wide hover:bg-clay transition-colors">
    Save changes
  </button>
</form>

<script>
    function showLoadingAnimation(form) {
        if (form.checkValidity()) {
            const btn = document.getElementById('upload-btn');
            const text = document.getElementById('upload-text');
            const spinner = document.getElementById('upload-spinner');

            btn.classList.add('opacity-75', 'cursor-wait');
            text.innerText = 'Saving...';
            spinner.style.display = 'inline-block';
            spinner.classList.remove('hidden');
        }
    }
</script>

@endsection