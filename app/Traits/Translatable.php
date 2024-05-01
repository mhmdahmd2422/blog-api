<?php

namespace App\Traits;

use App\Jobs\TranslateModelAttributesJob;

trait Translatable
{
    public static function bootTranslatable(): void
    {
        static::created(function ($model) {
            TranslateModelAttributesJob::dispatch($model, app()->currentLocale());
        });

        static::updated(function ($model) {
            $this->translations()->delete();
            TranslateModelAttributesJob::dispatch($model, app()->currentLocale());
        });
    }
}
