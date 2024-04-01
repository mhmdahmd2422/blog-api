<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Specification extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    public function scopeSearch($query, string $keyword): void
    {
        $query->where('name', 'like', "%{$keyword}%");
    }

    public function image(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function places(): BelongsToMany
    {
        return $this->belongsToMany(Place::class)
            ->withTimestamps()->withPivot('description');
    }

    public function remove(): bool
    {
        if ($this->image) {
            $this->image->remove();
        }

        $this->places()->sync([]);
        $this->delete();

        return true;
    }

    public function getVisiblePlacesAttribute()
    {
        return $this->places()->visible()->get();
    }

    public function getIconAttribute()
    {
        return $this->image()->isMain()->first();
    }
}
