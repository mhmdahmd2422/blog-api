<?php

namespace App\Http\Requests\Website;

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
            'body' => ['sometimes', 'string', 'max:1500']
        ];
    }

    public function updateComment(): Comment
    {
        $this->comment->update($this->validated());

        return $this->comment->fresh();
    }
}
