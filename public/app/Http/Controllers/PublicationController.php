<?php

namespace App\Http\Controllers;

use App\Models\Publication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PublicationController extends Controller
{
    // The PeaceWorks and Knowledge Products archive page.
    public function index(Request $request)
    {
        $query = Publication::query()->orderByDesc('published_at');

        if ($category = $request->query('category')) {
            $query->where('category', $category);
        }

        $publications = $query->paginate(12)->withQueryString();

        return view('publications.index', [
            'publications' => $publications,
            'categories' => Publication::CATEGORIES,
            'activeCategory' => $request->query('category'),
            'currentPage' => 'peaceworks',
        ]);
    }

    // Streamed inline for the click-to-popup PDF viewer.
    public function view(Publication $publication): StreamedResponse
    {
        abort_unless(Storage::disk('publications')->exists($publication->file_path), 404);

        return Storage::disk('publications')->response($publication->file_path, $publication->title.'.pdf', [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$publication->title.'.pdf"',
        ]);
    }

    // Forced download.
    public function download(Publication $publication): StreamedResponse
    {
        abort_unless(Storage::disk('publications')->exists($publication->file_path), 404);

        return Storage::disk('publications')->download($publication->file_path, $publication->title.'.pdf');
    }
}
