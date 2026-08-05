@csrf

<!-- Title Field -->
<div class="mb-6">
    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
        Publication Title <span class="text-red-500">*</span>
    </label>
    <input 
        type="text" 
        name="title" 
        id="title" 
        class="w-full border-gray-300 rounded shadow-sm focus:border-ink focus:ring-ink @error('title') border-red-500 @enderror" 
        value="{{ old('title', $publication->title ?? '') }}" 
        required
    >
    @error('title')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<!-- Category Field -->
<div class="mb-6">
    <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
        Category <span class="text-red-500">*</span>
    </label>
    <select 
        name="category" 
        id="category" 
        class="w-full border-gray-300 rounded shadow-sm focus:border-ink focus:ring-ink @error('category') border-red-500 @enderror" 
        required
    >
        <option value="">Select a category...</option>
        @foreach($categories as $category)
            <option value="{{ $category }}" {{ old('category', $publication->category ?? '') === $category ? 'selected' : '' }}>
                {{ $category }}
            </option>
        @endforeach
    </select>
    @error('category')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<!-- Published At Field -->
<div class="mb-6">
    <label for="published_at" class="block text-sm font-medium text-gray-700 mb-2">
        Publication Date <span class="text-red-500">*</span>
    </label>
    <input 
        type="date" 
        name="published_at" 
        id="published_at" 
        class="w-full border-gray-300 rounded shadow-sm focus:border-ink focus:ring-ink @error('published_at') border-red-500 @enderror" 
        value="{{ old('published_at', isset($publication) && $publication->published_at ? date('Y-m-d', strtotime($publication->published_at)) : now()->format('Y-m-d')) }}" 
        required
    >
    @error('published_at')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<!-- Description / Abstract Field -->
<div class="mb-6">
    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
        Description / Abstract
    </label>
    <textarea 
        name="description" 
        id="description" 
        rows="4" 
        class="w-full border-gray-300 rounded shadow-sm focus:border-ink focus:ring-ink @error('description') border-red-500 @enderror"
    >{{ old('description', $publication->description ?? '') }}</textarea>
    @error('description')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<!-- Document Upload -->
<div class="mb-6">
    <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
        Document File (PDF only, max 20MB) @if(!isset($publication))<span class="text-red-500">*</span>@endif
    </label>
    <input 
        type="file" 
        name="file" 
        id="file" 
        accept=".pdf"
        data-manual="true" 
        class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-ink hover:file:bg-gray-200 transition-colors @error('file') border-red-500 @enderror"
        {{ isset($publication) ? '' : 'required' }}
    >
    @error('file')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror

    @if(isset($publication) && $publication->file_path)
        <p class="mt-2 text-sm text-gray-500">
            Current file: 
            <a href="{{ route('publications.download', $publication->id) }}" target="_blank" class="underline text-ink hover:text-clay">
                Download current PDF
            </a>
        </p>
    @endif
</div>

<!-- Optional Cover Image -->
<div class="mb-8">
    <label for="cover_image" class="block text-sm font-medium text-gray-700 mb-2">
        Cover Image (Optional, max 4MB)
    </label>
    <input 
        type="file" 
        name="cover_image" 
        id="cover_image" 
        accept="image/*"
        data-manual="true"
        class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-ink hover:file:bg-gray-200 transition-colors @error('cover_image') border-red-500 @enderror"
    >
    @error('cover_image')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>