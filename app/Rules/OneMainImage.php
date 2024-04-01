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
        if ($this->model && $this->model->images->isnotEmpty()) {
            $mainImageCount = $this->model->images()->isMain()->count();
        } else {
            $mainImageCount = collect($value)->filter(function ($imageInput) {
                return isset($imageInput['is_main']) && $imageInput['is_main'];
            })->count();
        }

        return $mainImageCount === 1;
    }

    public function message(): string
    {
        return 'Exactly one image should be marked as main.';
    }
}
