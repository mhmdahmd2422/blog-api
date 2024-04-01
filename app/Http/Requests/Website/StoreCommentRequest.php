<?php

namespace App\Http\Requests\Website;

use App\Models\Comment;
use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'body' => ['required', 'string', 'max:1500']
        ];
    }

    public function storeComment(): Comment
    {
        return $this->post->comments()->create($this->validated());
    }
}
