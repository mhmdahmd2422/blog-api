<?php

namespace App\Http\Requests;

use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'title' => ['required', 'string', 'min:10', 'max:255'],
            'description' => ['required', 'string', 'min:50', 'max:2000'],
            'image' => ['sometimes', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'is_visible' => ['required', 'boolean']
        ];
    }

    public function storePost(): Post
    {
        $post = Post::create($this->safe()->except('image'));

        $this->whenHas('image', function (UploadedFile $image) use ($post) {
            $post->image()->create([
                'user_id' => $post->user_id,
                'path' => uploadImage($image, 'uploads/posts/')
            ]);
        });

        return $post;
    }
}
