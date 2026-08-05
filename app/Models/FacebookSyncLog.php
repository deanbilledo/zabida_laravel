<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacebookSyncLog extends Model
{
    protected $fillable = ['status', 'posts_created', 'message', 'ran_at'];

    protected $casts = [
        'ran_at' => 'datetime',
    ];
}
