<div class="mb-6">
  <label for="title" class="block text-sm font-medium mb-2">Title</label>
  <input type="text" name="title" id="title" required value="{{ old('title', $post->title ?? '') }}"
    class="w-full border border-ink/20 px-4 py-3 bg-white focus:border-clay focus:ring-1 focus:ring-clay outline-none transition-colors"
    @if ($errors->has('title')) aria-invalid="true" @endif>
  @error('title') <p class="text-clay text-sm mt-1.5" role="alert">{{ $message }}</p> @enderror
</div>

<div class="mb-6">
  <label for="excerpt" class="block text-sm font-medium mb-2">Excerpt</label>
  <textarea name="excerpt" id="excerpt" rows="2" maxlength="500"
    class="w-full border border-ink/20 px-4 py-3 bg-white focus:border-clay focus:ring-1 focus:ring-clay outline-none transition-colors">{{ old('excerpt', $post->excerpt ?? '') }}</textarea>
  @error('excerpt') <p class="text-clay text-sm mt-1.5" role="alert">{{ $message }}</p> @enderror
</div>

<div class="mb-6">
  <label for="body" class="block text-sm font-medium mb-2">Body</label>
  <textarea name="body" id="body" rows="8"
    class="w-full border border-ink/20 px-4 py-3 bg-white focus:border-clay focus:ring-1 focus:ring-clay outline-none transition-colors">{{ old('body', $post->body ?? '') }}</textarea>
</div>

<div class="mb-6">
  <label for="published_at" class="block text-sm font-medium mb-2">Publish date</label>
  <input type="date" name="published_at" id="published_at" required
    value="{{ old('published_at', isset($post) ? $post->published_at->format('Y-m-d') : now()->format('Y-m-d')) }}"
    class="w-full sm:w-64 border border-ink/20 px-4 py-3 bg-white focus:border-clay focus:ring-1 focus:ring-clay outline-none transition-colors">
  @error('published_at') <p class="text-clay text-sm mt-1.5" role="alert">{{ $message }}</p> @enderror
</div>

<div class="mb-8">
  <label for="media" class="block text-sm font-medium mb-2">Photos &amp; video</label>

  @if (isset($post) && $post->images->isNotEmpty())
    <div class="grid grid-cols-3 sm:grid-cols-4 gap-3 mb-4">
      @foreach ($post->images as $media)
        <div class="relative group">
          @if ($media->isVideo())
            <video src="{{ $media->url() }}" class="w-full aspect-square object-cover rounded border border-ink/10 bg-ink" muted></video>
            <span class="absolute bottom-1 left-1 bg-ink/70 text-paper text-[10px] px-1.5 py-0.5 rounded font-mono uppercase">Video</span>
          @else
            <img src="{{ $media->url() }}" alt="" class="w-full aspect-square object-cover rounded border border-ink/10">
            @if ($post->image === $media->path)
              <span class="absolute bottom-1 left-1 bg-gold text-ink text-[10px] px-1.5 py-0.5 rounded font-mono uppercase">Cover</span>
            @endif
          @endif
          
          {{-- The form tag is gone. We use form="delete-media-ID" to link it to the hidden forms outside --}}
          <button type="submit" form="delete-media-{{ $loop->index }}" aria-label="Remove this file"
            class="absolute -top-2 -right-2 w-6 h-6 flex items-center justify-center rounded-full bg-ink text-paper text-xs hover:bg-clay transition-colors">&times;</button>
        </div>
      @endforeach
    </div>
  @endif

  <input type="file" name="media[]" id="media" accept="image/*,video/*" multiple
    class="w-full text-sm border border-ink/20 px-4 py-3 bg-white file:mr-4 file:py-1.5 file:px-4 file:border-0 file:bg-ink file:text-paper file:text-xs file:uppercase file:tracking-wide file:cursor-pointer">
  <p class="text-xs text-ink/40 mt-1.5">Add any number of photos and video clips — mix both freely. JPG/PNG/WEBP images or MP4/MOV/WEBM/OGG video, up to 50MB each. The first photo becomes the cover automatically.</p>
  @error('media') <p class="text-clay text-sm mt-1.5" role="alert">{{ $message }}</p> @enderror
  @error('media.*') <p class="text-clay text-sm mt-1.5" role="alert">{{ $message }}</p> @enderror
</div>