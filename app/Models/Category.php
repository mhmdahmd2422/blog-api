<?php

namespace App\Models;

use App\Traits\Filterable;
use App\Traits\Sluggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Arr;

class Category extends Model
{
    use HasFactory, Sluggable, Filterable;

    protected $fillable = [
        'name',
        'is_visible',
    ];

    protected $casts = [
        'is_visible' => 'boolean'
    ];

    public function scopeVisible(Builder $query, bool $visible = true): void
    {
        $query->where('is_visible', $visible);
    }

    public function scopeSearch($query, $attributes, string $keyword, bool $is_visible = null): void
    {
        foreach(Arr::wrap($attributes) as $attribute) {
            $query->orWhere($attribute, 'like', "%{$keyword}%");
        }

        if ($is_visible) {
            $query->visible();
        }
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class)->withTimestamps();
    }

    public function image(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function getMainImageAttribute()
    {
        return $this->image()->isMain()->first();
    }

    public function remove(): bool
    {
        foreach ($this->posts as $post) {
            $categories = $post->categories->pluck('id');
            if ($categories->contains($this->id) && $categories->count() == 1) {
                return false;
            }
            $this->posts()->detach($post->id);
        }

        if ($this->image) {
            $this->image->remove();
        }

        $this->delete();

        return true;
    }

    protected function slugAttribute()
    {
        return $this->name;
    }
}
