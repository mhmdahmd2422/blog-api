<?php

namespace App\Models;

use App\Traits\Filterable;
use App\Traits\Sluggable;
use App\Traits\HasMorphedImages;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Arr;

class Post extends Model
{
    use HasFactory, Sluggable, HasMorphedImages, Filterable;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'is_visible',
    ];

    protected $casts = [
        'is_visible' => 'boolean'
    ];

    public function scopeVisible(Builder $query, bool $is_visible = true): void
    {
        $query->where('is_visible', $is_visible);
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class)->withTimestamps();
    }

    public function getMainImageAttribute()
    {
        return $this->images()->isMain()->first();
    }

    public function getVisibleCategoriesAttribute()
    {
        return $this->categories()->visible()->get();
    }

    public function remove(): bool
    {
        if ($this->images) {
            foreach ($this->images as $image) {
                $image->remove();
            }
        }

        $this->categories()->sync([]);
        $this->comments()->delete();
        $this->delete();

        return true;
    }

    public function destroyImage(string $imageId): bool
    {
        $image = $this->images()->where('id', $imageId)->first();

        if ($image) {
            if ($image->is_main && $this->images()->count() > 1) {
                $this->images()->where('id', '!=', $image->id)->first()
                    ->update(['is_main' => 1]);
            }

            $image->remove();
            return true;
        }

        return false;
    }

    public function slugAttribute()
    {
        return $this->title;
    }
}
