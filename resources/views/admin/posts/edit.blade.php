@extends('layouts.admin')

@php $pageTitle = 'Edit Post | ZABIDA Admin'; @endphp

@section('admin-content')

<h1 class="font-serif text-3xl mb-8">Edit journal post</h1>

<form method="POST" action="{{ route('admin.posts.update', $post) }}" enctype="multipart/form-data" class="max-w-2xl"
  onsubmit="showLoadingAnimation(this)"
  data-async-upload
  data-loading-label="Saving changes&hellip;"
  data-fallback-redirect="{{ route('admin.posts.index') }}">
  @csrf
  @method('PUT')
  
  @include('admin.posts._form')

  <button type="submit" id="upload-btn" class="bg-ink text-paper px-6 py-3 text-sm uppercase tracking-wide hover:bg-clay transition-colors inline-flex items-center justify-center gap-2">
    <svg id="upload-spinner" class="hidden animate-spin h-4 w-4 text-paper" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg>
    <span id="upload-text">Save changes</span>
  </button>
</form>

{{-- We render the delete forms OUTSIDE the main form to prevent HTML nesting errors --}}
@if (isset($post) && $post->images->isNotEmpty())
  @foreach ($post->images as $media)
    <form id="delete-media-{{ $loop->index }}" ...>
      ...
    </form>
  @endforeach
@endif

<script>
    function showLoadingAnimation(form) {
        if (form.checkValidity()) {
            const btn = document.getElementById('upload-btn');
            const text = document.getElementById('upload-text');
            const spinner = document.getElementById('upload-spinner');

            if (btn && text && spinner) {
                btn.classList.add('opacity-75', 'cursor-wait');
                text.innerText = 'Saving...';
                spinner.style.display = 'inline-block';
                spinner.classList.remove('hidden');
            }
        }
    }
</script>
<script>
    function showLoadingAnimation(form) {
        if (form.checkValidity()) {
            const btn = document.getElementById('upload-btn');
            const text = document.getElementById('upload-text');
            const spinner = document.getElementById('upload-spinner');

            if (btn && text && spinner) {
                btn.classList.add('opacity-75', 'cursor-wait');
                text.innerText = 'Saving...';
                spinner.style.display = 'inline-block';
                spinner.classList.remove('hidden');
            }
        }
    }

    // Media removal uses ZUI's confirm modal directly, then fetch() —
    // deliberately NOT a form submit, so it never touches the global
    // data-confirm/data-async-upload form listeners in ui.js. That keeps
    // it from reloading the page, which is what was wiping out any
    // unsaved title/body edits.
    document.querySelectorAll('.js-remove-media').forEach(function (button) {
        button.addEventListener('click', async function () {
            var ok = await window.ZUI.confirm('Remove this file from the post?', { danger: true });
            if (!ok) return;

            var thumbnail = button.closest('.relative.group');
            var url = button.dataset.url;
            var token = document.querySelector('meta[name="csrf-token"]')
                ? document.querySelector('meta[name="csrf-token"]').content
                : document.querySelector('input[name="_token"]').value;

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'X-HTTP-Method-Override': 'DELETE',
                        'Accept': 'application/json',
                    },
                    body: (function () {
                        var fd = new FormData();
                        fd.append('_method', 'DELETE');
                        fd.append('_token', token);
                        return fd;
                    })(),
                });

                if (!response.ok) {
                    window.ZUI.showLoadingError
                        ? alert('Could not remove that file — please try again.')
                        : alert('Could not remove that file — please try again.');
                    return;
                }

                if (thumbnail) thumbnail.remove();
            } catch (err) {
                alert('Could not remove that file — please check your connection and try again.');
            }
        });
    });
</script>
@endsection