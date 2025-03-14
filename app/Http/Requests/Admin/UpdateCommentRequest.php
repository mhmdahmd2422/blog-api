<?php

namespace App\Http\Requests\Admin;

use App\Models\Comment;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'is_banned' => ['required', 'boolean']
        ];
    }

    public function updateComment(): Comment
    {
        $this->comment->update($this->validated());

        return $this->comment->fresh();
    }
}
