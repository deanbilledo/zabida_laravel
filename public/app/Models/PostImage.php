<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostImage extends Model
{
    protected $fillable = ['post_id', 'path', 'type', 'facebook_media_id', 'position'];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function url(): string
    {
        return asset('storage/'.$this->path);
    }

    public function isVideo(): bool
    {
        return $this->type === 'video';
    }
}
