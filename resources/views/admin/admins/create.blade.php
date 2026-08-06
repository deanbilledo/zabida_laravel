@extends('layouts.admin')

@php $pageTitle = 'Add Admin | ZABIDA Admin'; @endphp

@section('admin-content')

<h1 class="font-serif text-3xl mb-8">Add a new admin</h1>

<form method="POST" action="{{ route('admin.admins.store') }}" class="max-w-lg">
  @csrf

  <div class="mb-6">
    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Name <span class="text-red-500">*</span></label>
    <input type="text" name="name" id="name" class="w-full border-gray-300 rounded shadow-sm focus:border-ink focus:ring-ink @error('name') border-red-500 @enderror" value="{{ old('name') }}" required>
    @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
  </div>

  <div class="mb-6">
    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
    <input type="email" name="email" id="email" class="w-full border-gray-300 rounded shadow-sm focus:border-ink focus:ring-ink @error('email') border-red-500 @enderror" value="{{ old('email') }}" required>
    @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
  </div>

  <div class="mb-8">
    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password <span class="text-red-500">*</span></label>
    <input type="text" name="password" id="password" class="w-full border-gray-300 rounded shadow-sm focus:border-ink focus:ring-ink font-mono @error('password') border-red-500 @enderror" required>
    <p class="mt-1 text-xs text-ink/50">Shown in plain text so you can copy it and send it to the new admin directly. Min 8 characters.</p>
    @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
  </div>

  <button type="submit" class="bg-ink text-paper px-6 py-3 text-sm uppercase tracking-wide hover:bg-clay transition-colors">
    Create admin
  </button>
</form>

@endsection