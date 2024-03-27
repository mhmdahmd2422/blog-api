<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MaxAllowedImages implements Rule
{
    public function __construct(
        protected Model $model,
        protected int $max_allowed
    ) {

    }

    public function passes($attribute, $value): bool
    {
        return $this->model->images->count() + count($value) <= $this->max_allowed;
    }

    public function message(): string
    {
        $allowed = $this->max_allowed - $this->model->images->count();

        if ($allowed > 0) {
            return "Only " . $allowed . " more " . Str::plural('image', $allowed) . " is allowed.";
        }

        return "A maximum limit of images has been reached. Please remove existing images before adding new ones.";
    }
}
