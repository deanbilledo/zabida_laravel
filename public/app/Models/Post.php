<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'excerpt', 'body', 'image', 'video_url',
        'source', 'facebook_post_id', 'facebook_permalink', 'published_at',
    ];

    protected $casts = [
        'published_at' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (Post $post) {
            if (empty($post->slug)) {
                $post->slug = $post->generateUniqueSlug();
            }
        });
    }

    public function generateUniqueSlug(): string
    {
        $base = Str::slug($this->title) ?: 'post';
        $slug = $base;
        $i = 1;
        while (static::where('slug', $slug)->exists()) {
            $slug = $base.'-'.(++$i);
        }
        return $slug;
    }

    public function images(): HasMany
    {
        return $this->hasMany(PostImage::class)->orderBy('position');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // Cover image with a safe fallback, used everywhere a thumbnail is shown.
    public function coverImageUrl(): string
    {
        if ($this->image) {
            return str_starts_with($this->image, 'http')
                ? $this->image
                : asset('storage/'.$this->image);
        }
        return asset('assets/images/zabida_logo.png');
    }
}
