<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'path',
        'is_main'
    ];

    protected $casts = [
        'is_main' => 'boolean'
    ];

    public function scopeIsMain($query, bool $is_main = true): void
    {
        $query->where('is_main', $is_main);
    }

    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }

    public function remove(): bool
    {
        deleteImage($this->path);
        $this->delete();

        return true;
    }
}
