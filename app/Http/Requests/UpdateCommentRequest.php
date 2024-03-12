<?php

namespace App\Http\Requests;

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
            'body' => ['sometimes', 'string', 'max:1500'],
        ];
    }

    public function updateComment()
    {
        return $this->comment->update($this->request->all());
    }
}
