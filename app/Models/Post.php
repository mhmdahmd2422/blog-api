<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'image',
        'is_visible',
    ];

    protected $casts = [
        'is_visible' => 'boolean'
    ];

    public function scopeVisible(Builder $query)
    {
        $query->where('is_visible', true);
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

    public function oldestImage()
    {
        return $this->morphOne(Image::class, 'imageable')->oldestOfMany();
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class)->withTimestamps();
    }

    public function visibleCategories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class)->withTimestamps()->visible();
    }

    public function remove(): bool
    {
        if ($this->images) {
            foreach ($this->images as $image) {
                $image->remove();
            }
        }

        $this->comments()->delete();
        $this->delete();

        return true;
    }

    public function destroyImage(string $imageId)
    {
        $image = Image::whereHasMorph(
            'imageable',
            Post::class,
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
}
