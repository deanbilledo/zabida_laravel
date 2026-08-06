<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Publication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PublicationController extends Controller
{
    public function index()
    {
        $publications = Publication::orderByDesc('published_at')->paginate(15);

        return view('admin.publications.index', ['publications' => $publications]);
    }

    public function create()
    {
        return view('admin.publications.create', ['categories' => Publication::CATEGORIES]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        $file = $request->file('file');
        $storedPath = $file->store('publications', 'publications');

        $validated['file_path'] = $storedPath;
        $validated['file_size'] = $file->getSize();
        $validated['uploaded_by'] = Auth::id();

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('publications/covers', 'public');
        } elseif ($autoCover = $this->storeAutoCover($request)) {
            $validated['cover_image'] = $autoCover;
        }

        $publication = Publication::create($validated);

        return redirect()->route('admin.publications.index')
            ->with('status', 'success')
            ->with('message', '"'.$publication->title.'" was uploaded to the archive.');
    }

    public function edit(Publication $publication)
    {
        return view('admin.publications.edit', [
            'publication' => $publication,
            'categories' => Publication::CATEGORIES,
        ]);
    }

    public function update(Request $request, Publication $publication)
    {
        $validated = $this->validated($request, requireFile: false);

        if ($request->hasFile('file')) {
            Storage::disk('publications')->delete($publication->file_path);
            $file = $request->file('file');
            $validated['file_path'] = $file->store('publications', 'publications');
            $validated['file_size'] = $file->getSize();
        }

        if ($request->hasFile('cover_image')) {
            if ($publication->cover_image) {
                Storage::disk('public')->delete($publication->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')->store('publications/covers', 'public');
        } elseif (! $publication->cover_image && $autoCover = $this->storeAutoCover($request)) {
            // Only auto-fill if this publication genuinely has no cover yet —
            // don't clobber an existing one just because a new PDF was uploaded.
            $validated['cover_image'] = $autoCover;
        }

        $publication->update($validated);

        return redirect()->route('admin.publications.index')
            ->with('status', 'success')
            ->with('message', '"'.$publication->title.'" was updated.');
    }

    public function destroy(Publication $publication)
    {
        $title = $publication->title;

        Storage::disk('publications')->delete($publication->file_path);
        if ($publication->cover_image) {
            Storage::disk('public')->delete($publication->cover_image);
        }

        $publication->delete();

        return redirect()->route('admin.publications.index')
            ->with('status', 'success')
            ->with('message', '"'.$title.'" was removed from the archive.');
    }

    protected function validated(Request $request, bool $requireFile = true): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'category' => ['required', 'string', 'in:'.implode(',', Publication::CATEGORIES)],
            'published_at' => ['required', 'date'],
            'file' => [$requireFile ? 'required' : 'nullable', 'file', 'mimes:pdf', 'max:20480'],
            'cover_image' => ['nullable', 'image', 'max:4096'],
            'auto_cover_image' => ['nullable', 'string'],
        ], [
            'title.required' => 'Please give the publication a title.',
            'category.in' => 'Please choose a valid category.',
            'file.required' => 'Please choose a PDF file to upload.',
            'file.mimes' => 'Only PDF files can be uploaded here.',
            'file.max' => 'That PDF is too large — please keep it under 20MB.',
        ]);
    }

    /**
     * Decode the browser-generated page-1 thumbnail (sent as a base64 PNG
     * data URL via pdf.js) and store it as the cover image. Returns null
     * if no auto-thumbnail was submitted or it fails to decode.
     */
    protected function storeAutoCover(Request $request): ?string
    {
        $dataUrl = $request->input('auto_cover_image');

        if (! $dataUrl || ! str_starts_with($dataUrl, 'data:image/png;base64,')) {
            return null;
        }

        $base64 = substr($dataUrl, strlen('data:image/png;base64,'));
        $binary = base64_decode($base64, true);

        if ($binary === false) {
            return null;
        }

        $path = 'publications/covers/'.\Illuminate\Support\Str::random(20).'.png';
        Storage::disk('public')->put($path, $binary);

        return $path;
    }
}
