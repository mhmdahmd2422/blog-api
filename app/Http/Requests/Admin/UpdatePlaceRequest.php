<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePlaceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:100'],
            'description' => ['sometimes', 'string', 'max:1000'],
            'is_visible' => ['sometimes', 'boolean'],
            'tag_id' => ['sometimes'],
            'tag_id.*' => ['integer', 'distinct', 'exists:tags,id'],
            'specifications' => ['sometimes', 'min:1'],
            'specifications.*.specification_id' => ['integer', 'exists:specifications,id'],
            'specifications.*.description' => ['string'],
        ];
    }

    public function updatePlace()
    {
        $this->place->update($this->validated());

        $this->place->tags()->sync(data_get($this->safe()->only(['tag_id']), 'tag_id'));

        $this->place->specifications()->sync($this->safe()->only(['specifications'])['specifications']);

        return $this->place->fresh();
    }
}
