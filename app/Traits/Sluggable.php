<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait Sluggable
{
    abstract protected function slugAttribute();

    public static function bootSluggable(): void
    {
        static::creating(function ($model) {
            $model->slug = $model->generateSlug($model->slugAttribute());
        });
    }

    public function generateSlug(string $attribute)
    {
        $slug = Str::slug($attribute, $separator = '-');

        $existingSlugs = $this->getExistingSlugs($slug, $this->getTable());

        if (! in_array($slug, $existingSlugs)) {
            return $slug;
        }

        $uniqueSlugFound = false;
        $i = 1;

        while (! $uniqueSlugFound) {
            $newSlug = $slug . $separator . $i;

            if (!in_array($newSlug, $existingSlugs)) {
                return $newSlug;
            }

            $i++;
        }
    }

    private function getExistingSlugs(string $slug, string $table): array
    {
        return $this->where('slug', 'LIKE', $slug . '%')
            ->pluck('slug')
            ->toArray();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
