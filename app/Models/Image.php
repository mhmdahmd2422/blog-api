<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'path'
    ];

    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): hasOneThrough
    {
        return $this->hasOneThrough(User::class, Post::class, 'imageable_id')
            ->where('imageable_type', static::class);
    }
}
