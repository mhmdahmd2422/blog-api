<?php

namespace App\Models;

use App\Traits\Filterable;
use App\Traits\HasMorphedImages;
use App\Traits\Sluggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Arr;

class Place extends Model
{
    use HasFactory, Sluggable, HasMorphedImages, Filterable;

    protected $fillable = [
        'name',
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

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function specifications(): BelongsToMany
    {
        return $this->belongsToMany(Specification::class)
            ->withTimestamps()->withPivot('description');
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable')->withTimestamps();
    }

    public function remove(): bool
    {
        if ($this->images) {
            foreach ($this->images as $image) {
                $image->remove();
            }
        }

        $this->specifications()->sync([]);
        $this->tags()->sync([]);
        $this->delete();

        return true;
    }

    public function destroyImage(string $imageId): bool
    {
        $image = $this->whereHasImage($imageId);

        if ($image) {
            if ($image->is_main && $this->images()->count() == 1) {
                $image->remove();

                return true;
            }
        }

        return false;
    }

    public function getMainImageAttribute()
    {
        return $this->images()->isMain()->first();
    }

    public function getVisibleTagsAttribute()
    {
        return $this->tags()->visible()->get();
    }

    public function slugAttribute()
    {
        return $this->name;
    }
}
