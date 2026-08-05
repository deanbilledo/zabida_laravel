@csrf
@if (isset($publication)) @method('PUT') @endif

<div class="mb-6">
  <label for="title" class="block text-sm font-medium mb-2">Title</label>
  <input type="text" name="title" id="title" required value="{{ old('title', $publication->title ?? '') }}"
    class="w-full border border-ink/20 px-4 py-3 bg-white focus:border-clay focus:ring-1 focus:ring-clay outline-none transition-colors">
  @error('title') <p class="text-clay text-sm mt-1.5" role="alert">{{ $message }}</p> @enderror
</div>

<div class="mb-6">
  <label for="category" class="block text-sm font-medium mb-2">Category</label>
  <select name="category" id="category" required class="w-full border border-ink/20 px-4 py-3 bg-white focus:border-clay focus:ring-1 focus:ring-clay outline-none transition-colors">
    @foreach ($categories as $category)
      <option value="{{ $category }}" @selected(old('category', $publication->category ?? '') === $category)>{{ $category }}</option>
    @endforeach
  </select>
  @error('category') <p class="text-clay text-sm mt-1.5" role="alert">{{ $message }}</p> @enderror
</div>

<div class="mb-6">
  <label for="description" class="block text-sm font-medium mb-2">Description</label>
  <textarea name="description" id="description" rows="3" maxlength="2000"
    class="w-full border border-ink/20 px-4 py-3 bg-white focus:border-clay focus:ring-1 focus:ring-clay outline-none transition-colors">{{ old('description', $publication->description ?? '') }}</textarea>
</div>

<div class="mb-6">
  <label for="published_at" class="block text-sm font-medium mb-2">Publish date</label>
  <input type="date" name="published_at" id="published_at" required
    value="{{ old('published_at', isset($publication) ? $publication->published_at->format('Y-m-d') : now()->format('Y-m-d')) }}"
    class="w-full sm:w-64 border border-ink/20 px-4 py-3 bg-white focus:border-clay focus:ring-1 focus:ring-clay outline-none transition-colors">
  @error('published_at') <p class="text-clay text-sm mt-1.5" role="alert">{{ $message }}</p> @enderror
</div>

<div class="mb-6">
  <label for="file" class="block text-sm font-medium mb-2">PDF file</label>
  @if (isset($publication))
    <p class="text-sm text-ink/50 mb-2">Current file: {{ $publication->formattedSize() }} — leave blank to keep it.</p>
  @endif
  <input type="file" name="file" id="file" accept="application/pdf"
    class="w-full text-sm border border-ink/20 px-4 py-3 bg-white file:mr-4 file:py-1.5 file:px-4 file:border-0 file:bg-ink file:text-paper file:text-xs file:uppercase file:tracking-wide file:cursor-pointer">
  <p class="text-xs text-ink/40 mt-1.5">PDF only — up to 20MB.</p>
  @error('file') <p class="text-clay text-sm mt-1.5" role="alert">{{ $message }}</p> @enderror
</div>

<div class="mb-8">
  <label for="cover_image" class="block text-sm font-medium mb-2">Cover thumbnail</label>
  @if (isset($publication) && $publication->cover_image)
    <img src="{{ $publication->coverImageUrl() }}" alt="" class="w-24 h-32 object-cover rounded border border-ink/10 mb-3">
  @endif
  <div id="cover-auto-preview" class="hidden mb-3">
    <img id="cover-auto-preview-img" src="" alt="" class="w-24 h-32 object-cover rounded border border-ink/10">
    <p class="text-xs text-ink/40 mt-1.5">Auto-generated from page 1 of the PDF. Upload your own below to override it.</p>
  </div>
  <p id="cover-auto-status" class="text-xs text-ink/40 mb-2 hidden">Generating a thumbnail from the PDF's first page&hellip;</p>
  <input type="file" name="cover_image" id="cover_image" accept="image/*"
    class="w-full text-sm border border-ink/20 px-4 py-3 bg-white file:mr-4 file:py-1.5 file:px-4 file:border-0 file:bg-ink file:text-paper file:text-xs file:uppercase file:tracking-wide file:cursor-pointer">
  <p class="text-xs text-ink/40 mt-1.5">Leave blank to auto-generate one from the PDF's first page, or upload your own image.</p>
  @error('cover_image') <p class="text-clay text-sm mt-1.5" role="alert">{{ $message }}</p> @enderror
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.0.379/pdf.min.js"></script>
<script>
// Renders page 1 of the chosen PDF to a canvas entirely in the browser and
// attaches it to the (hidden, unless the admin picks their own) cover_image
// input — so a thumbnail exists with zero server-side PDF/image libraries,
// which matters on shared hosting where Imagick + Ghostscript usually
// aren't available.
(function () {
  var fileInput = document.getElementById('file');
  var coverInput = document.getElementById('cover_image');
  var previewWrap = document.getElementById('cover-auto-preview');
  var previewImg = document.getElementById('cover-auto-preview-img');
  var statusEl = document.getElementById('cover-auto-status');
  if (!fileInput || !window.pdfjsLib) return;

  pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.0.379/pdf.worker.min.js';

  var userPickedCover = false;
  coverInput.addEventListener('change', function () {
    userPickedCover = coverInput.files && coverInput.files.length > 0;
  });

  fileInput.addEventListener('change', function () {
    if (userPickedCover || !fileInput.files || !fileInput.files[0]) return;

    var file = fileInput.files[0];
    statusEl.classList.remove('hidden');
    previewWrap.classList.add('hidden');

    var reader = new FileReader();
    reader.onload = function () {
      pdfjsLib.getDocument({ data: new Uint8Array(reader.result) }).promise
        .then(function (pdf) { return pdf.getPage(1); })
        .then(function (page) {
          var viewport = page.getViewport({ scale: 1.2 });
          var canvas = document.createElement('canvas');
          canvas.width = viewport.width;
          canvas.height = viewport.height;
          return page.render({ canvasContext: canvas.getContext('2d'), viewport: viewport }).promise
            .then(function () { return canvas; });
        })
        .then(function (canvas) {
          canvas.toBlob(function (blob) {
            if (!blob) { statusEl.classList.add('hidden'); return; }
            var thumbFile = new File([blob], 'pdf-thumbnail.png', { type: 'image/png' });
            var transfer = new DataTransfer();
            transfer.items.add(thumbFile);
            coverInput.files = transfer.files;

            previewImg.src = URL.createObjectURL(blob);
            previewWrap.classList.remove('hidden');
            statusEl.classList.add('hidden');
          }, 'image/png');
        })
        .catch(function () {
          // If pdf.js can't render it client-side (e.g. an unusual PDF),
          // we just fall back to the placeholder icon — never block the
          // upload itself over a missing thumbnail.
          statusEl.classList.add('hidden');
        });
    };
    reader.readAsArrayBuffer(file);
  });
})();
</script>
@endpush
