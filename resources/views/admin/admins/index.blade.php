@extends('layouts.admin')

@php $pageTitle = 'Manage Admins | ZABIDA Admin'; @endphp

@section('admin-content')

<div class="flex items-center justify-between mb-8">
  <h1 class="font-serif text-3xl">Admins</h1>
  <a href="{{ route('admin.admins.create') }}" class="bg-ink text-paper px-5 py-2.5 text-sm uppercase tracking-wide hover:bg-clay transition-colors">
    Add admin
  </a>
</div>

@if (session('message'))
  <div class="mb-6 px-4 py-3 text-sm rounded {{ session('status') === 'error' ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-green-50 text-green-700 border border-green-200' }}">
    {{ session('message') }}
  </div>
@endif

<div class="border border-ink/10 rounded-lg overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-gray-50 text-left text-ink/60 uppercase text-xs tracking-wide">
      <tr>
        <th class="px-4 py-3">Name</th>
        <th class="px-4 py-3">Email</th>
        <th class="px-4 py-3">Role</th>
        <th class="px-4 py-3 text-right">Actions</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-ink/10">
      @foreach ($admins as $admin)
        <tr>
          <td class="px-4 py-3">{{ $admin->name }}</td>
          <td class="px-4 py-3 text-ink/70">{{ $admin->email }}</td>
          <td class="px-4 py-3">
            @if ($admin->isSuperAdmin())
              <span class="inline-block px-2 py-0.5 text-xs uppercase tracking-wide bg-ink text-paper rounded">Super Admin</span>
            @else
              <span class="inline-block px-2 py-0.5 text-xs uppercase tracking-wide bg-gray-100 text-ink/70 rounded">Admin</span>
            @endif
            @if ($admin->id === auth()->id())
              <span class="text-xs text-ink/40 ml-1">(you)</span>
            @endif
          </td>
          <td class="px-4 py-3 text-right space-x-3">
            @if (! $admin->isSuperAdmin())
              <form method="POST" action="{{ route('admin.admins.promote', $admin) }}" class="inline" onsubmit="return confirm('Promote {{ $admin->name }} to super admin?');">
                @csrf
                <button type="submit" class="text-xs uppercase tracking-wide text-ink/60 hover:text-clay transition-colors">Promote</button>
              </form>
            @endif
            @if ($admin->id !== auth()->id())
              <form method="POST" action="{{ route('admin.admins.destroy', $admin) }}" class="inline" onsubmit="return confirm('Remove {{ $admin->name }}? This can\'t be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-xs uppercase tracking-wide text-red-600 hover:text-red-800 transition-colors">Remove</button>
              </form>
            @endif
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>

@endsection