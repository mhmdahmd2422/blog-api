<?php

namespace App\Http\Requests\Admin;

use App\Models\Place;
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
            'tag_id' => ['sometimes', 'array'],
            'tag_id.*' => ['required_with:tag_id', 'integer', 'distinct', 'exists:tags,id'],
            'specifications' => ['sometimes', 'array', 'min:1'],
            'specifications.*.specification_id' => ['required_with:specifications', 'integer', 'distinct', 'exists:specifications,id'],
            'specifications.*.description' => ['required_with:specifications', 'string', 'max:100'],
        ];
    }

    public function updatePlace(): Place
    {
        $this->place->update($this->validated());

        $this->place->tags()->sync(data_get($this->safe()->only(['tag_id']), 'tag_id'));

        $this->place->specifications()->sync($this->safe()->only(['specifications'])['specifications']);

        return $this->place->fresh();
    }
}
