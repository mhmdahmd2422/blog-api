<?php

namespace App\Http\Requests;

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
            'user_id' => ['sometimes', 'exists:users,id'],
            'category_id' => ['sometimes'],
            'category_id.*' => ['integer', 'exists:categories,id'],
            'title' => ['sometimes', 'string', 'min:10', 'max:255'],
            'description' => ['sometimes', 'string', 'min:50', 'max:2000'],
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
