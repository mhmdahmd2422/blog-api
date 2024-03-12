<?php

namespace App\Http\Requests;

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
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string', 'max:2000'],
            'image' => ['sometimes', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'is_visible' => ['sometimes', 'boolean'],
        ];
    }

    public function updatePost(): bool
    {
        return $this->post->update($this->request->all());
    }
}
