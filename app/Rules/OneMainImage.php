<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class OneMainImage implements Rule
{
    public function __construct(
        protected $model = null,
        protected $is_main = null,
        protected $imageId = null,
    ) {
    }

    public function passes($attribute, $value): bool
    {
        if ($this->model && $this->model->images()->count() > 0) {
            $mainImageCount = $this->model->images()->isMain()->count();

            if ($this->is_main === false) {
                $mainImageCount = !($this->imageId == $this->model->main_image->id);
            }
        } else {
            $mainImageCount = collect($value)->filter(function ($imageInput) {
                return isset($imageInput['is_main']) && $imageInput['is_main'];
            })->count();
        }

        return $mainImageCount == 1;
    }

    public function message(): string
    {
        return 'Exactly one image should be marked as main.';
    }
}
