@extends('layouts.admin')

@php $pageTitle = 'Upload Publication | ZABIDA Admin'; @endphp

@section('admin-content')

<h1 class="font-serif text-3xl mb-8">Upload a publication</h1>

<form method="POST" action="{{ route('admin.publications.store') }}" enctype="multipart/form-data" class="max-w-2xl" id="pub-form">
  @include('admin.publications._form')

  <button type="submit" id="pub-submit" class="bg-ink text-paper px-6 py-3 text-sm uppercase tracking-wide hover:bg-clay transition-colors disabled:opacity-60 inline-flex items-center gap-2">
    <span data-default-label>Upload to archive</span>
    <span data-loading-label class="hidden items-center gap-2">
      <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
      Uploading&hellip; this can take a moment for larger PDFs
    </span>
  </button>
</form>

@push('scripts')
<script>
(function () {
  var form = document.getElementById('pub-form');
  var button = document.getElementById('pub-submit');
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
