<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

// PeaceWorks and Knowledge Products — a PDF archive entry.
class Publication extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'description', 'category', 'file_path',
        'cover_image', 'file_size', 'published_at', 'uploaded_by',
    ];

    protected $casts = [
        'published_at' => 'date',
    ];

    public const CATEGORIES = ['Magazine', 'Knowledge Product', 'Report', 'Policy Brief'];

    protected static function booted(): void
    {
        static::creating(function (Publication $publication) {
            if (empty($publication->slug)) {
                $base = Str::slug($publication->title) ?: 'publication';
                $slug = $base;
                $i = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $base.'-'.(++$i);
                }
                $publication->slug = $slug;
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function formattedSize(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 1).' MB';
        }
        if ($bytes >= 1024) {
            return round($bytes / 1024).' KB';
        }
        return $bytes.' B';
    }

    public function coverImageUrl(): string
    {
        return $this->cover_image
            ? asset('storage/'.$this->cover_image)
            : asset('assets/images/pdf-placeholder.png');
    }
}
