@extends('layouts.admin')

@php $pageTitle = 'Upload Publication | ZABIDA Admin'; @endphp

@section('admin-content')

<h1 class="font-serif text-3xl mb-8">Upload a publication</h1>

<form method="POST" action="{{ route('admin.publications.store') }}" enctype="multipart/form-data" class="max-w-2xl" onsubmit="showLoadingAnimation(this)">
    
    @include('admin.publications._form')

    <button type="submit" id="upload-btn" class="bg-ink text-paper px-6 py-3 text-sm uppercase tracking-wide hover:bg-clay transition-colors mt-4">
        <span id="upload-text">Upload to archive</span>
        
        <!-- SVG Spinner with hardcoded fallback styles just in case CSS fails -->
        <svg id="upload-spinner" class="animate-spin ml-2 h-4 w-4 text-paper hidden inline-block align-text-bottom" style="display: none; width: 16px; height: 16px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </button>
</form>

<script>
    function showLoadingAnimation(form) {
        if (form.checkValidity()) {
            const btn = document.getElementById('upload-btn');
            const text = document.getElementById('upload-text');
            const spinner = document.getElementById('upload-spinner');

            btn.classList.add('opacity-75', 'cursor-wait');
            text.innerText = 'Uploading...';
            spinner.style.display = 'inline-block';
            spinner.classList.remove('hidden');
        }
    }
</script>

@endsection