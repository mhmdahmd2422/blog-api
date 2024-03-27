<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class OneMainImage implements Rule
{
    public function __construct(
        protected $model = null,
    ) {

    }

    public function passes($attribute, $value): bool
    {
        if ($this->model) {
            $mainImageCount = $this->model->images()->isMain()->count();
        } else {
            $mainImageCount = collect($value)->filter(function ($image) {
                return isset($image['is_main']) && $image['is_main'];
            })->count();
        }

        return $mainImageCount === 1;
    }

    public function message(): string
    {
        return 'Exactly one image should be marked as main.';
    }
}
