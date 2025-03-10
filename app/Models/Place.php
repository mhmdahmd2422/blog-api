<?php

namespace App\Models;

use App\Traits\Sluggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Place extends Model
{
    use HasFactory;
    use Sluggable;

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

    public function scopeSearch($query, string $keyword, bool $is_visible = null): void
    {
        $query->where('name', 'like', "%{$keyword}%");

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

        $this->specifications->each(function ($specification) {
            $this->specifications()->detach($specification->id);
        });

        $this->tags->each(function ($tag) {
            $this->tags()->detach($tag->id);
        });

        $this->delete();

        return true;
    }

    public function destroyImage(string $imageId)
    {
        $image = Image::whereHasMorph(
            'imageable',
            Place::class,
            function (Builder $query) {
                $query->whereId($this->id);
            }
        )->whereId($imageId)->first();

        if ($image) {
            $image->remove();

            return true;
        }

        return false;
    }

    public function getMainImageAttribute()
    {
        return $this->images()->isMain()->first();
    }

    public function slugAttribute()
    {
        return $this->name;
    }
}
