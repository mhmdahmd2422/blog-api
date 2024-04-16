<?php

namespace App\Http\Requests\Website;

use App\Models\Comment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'max:1500']
        ];
    }

    public function storeComment(): Comment
    {
        return $this->post->comments()->create(
            [
                'user_id' => Auth::id(),
                'body' => $this->body
            ]);
    }
}
