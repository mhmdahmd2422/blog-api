<?php

namespace App\Http\Requests\Website;

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
            'body' => ['sometimes', 'string', 'min:10', 'max:1500']
        ];
    }

    public function updateComment()
    {
        $this->comment->update($this->validated());

        return $this->comment->fresh();
    }
}
