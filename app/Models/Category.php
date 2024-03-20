<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_visible',
    ];

    protected $casts = [
        'is_visible' => 'boolean'
    ];

    public function scopeVisible(Builder $query)
    {
        $query->where('is_visible', true);
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class)->withTimestamps();
    }

    public function image(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function remove(): bool
    {
        if ($this->image) {
            $this->image->remove();
        }

        $this->posts->each(function ($post) {
            $categories = $post->categories->pluck('id');
            if ($categories->contains($this->id) && $categories->count() == 1) {
                $post->remove();
            }
            $this->posts()->detach($post->id);
        });

        $this->delete();

        return true;
    }
}
