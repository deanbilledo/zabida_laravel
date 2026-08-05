@extends('layouts.admin')

@php $pageTitle = 'PeaceWorks Archive | ZABIDA Admin'; @endphp

@section('admin-content')

<div class="flex items-center justify-between mb-8 flex-wrap gap-4">
  <h1 class="font-serif text-3xl">PeaceWorks &amp; Knowledge Products archive</h1>
  <a href="{{ route('admin.publications.create') }}" class="bg-ink text-paper px-5 py-2.5 text-sm uppercase tracking-wide hover:bg-clay transition-colors">Upload PDF</a>
</div>

<div class="divide-y divide-ink/10">
  @forelse ($publications as $publication)
    <div class="flex items-center justify-between gap-4 py-4">
      <div>
        <p class="font-serif text-lg">{{ $publication->title }}</p>
        <p class="text-sm text-ink/50">{{ $publication->category }} &middot; {{ $publication->published_at->format('M j, Y') }} &middot; {{ $publication->formattedSize() }}</p>
      </div>
      <div class="flex items-center gap-4 flex-shrink-0">
        <a href="{{ route('publications.view', $publication) }}" target="_blank" class="text-sm text-ink/60 hover:text-clay">View</a>
        <a href="{{ route('admin.publications.edit', $publication) }}" class="text-sm text-ink/60 hover:text-clay">Edit</a>
        <form method="POST" action="{{ route('admin.publications.destroy', $publication) }}"
          data-confirm="Remove &quot;{{ $publication->title }}&quot; from the archive? This can't be undone."
          data-loading-label="Removing&hellip;">
          @csrf
          @method('DELETE')
          <button type="submit" class="text-sm text-clay hover:underline">Delete</button>
        </form>
      </div>
    </div>
  @empty
    <p class="text-ink/50 py-10">No publications uploaded yet.</p>
  @endforelse
</div>

@if ($publications->hasPages())
  <div class="mt-8">{{ $publications->links() }}</div>
@endif

@endsection
