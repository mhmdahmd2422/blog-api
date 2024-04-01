<?php

namespace App\Http\Requests\Admin;

use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['sometimes', 'array'],
            'category_id.*' => ['required_with:category_id', 'integer', 'distinct', 'exists:categories,id'],
            'title' => ['sometimes', 'string', 'min:5', 'max:255'],
            'description' => ['sometimes', 'string', 'max:2000'],
            'is_visible' => ['sometimes', 'boolean']
        ];
    }

    public function updatePost(): Post
    {
        $this->post->update($this->validated());

        $this->post->categories()->sync(data_get($this->safe()->only(['category_id']), 'category_id'));

        return $this->post->fresh();
    }
}
