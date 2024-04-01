<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_visible'
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

    public function places(): MorphToMany
    {
        return $this->morphedByMany(Place::class, 'taggable');
    }

    public function remove(): bool
    {
        $this->places()->sync([]);
        $this->delete();

        return true;
    }

    public function getVisiblePlacesAttribute()
    {
        return $this->places()->visible()->get();
    }
}
