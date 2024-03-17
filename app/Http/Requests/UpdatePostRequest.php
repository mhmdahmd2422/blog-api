<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

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
            'title' => ['sometimes', 'string', 'min:10', 'max:255'],
            'description' => ['sometimes', 'string', 'min:50', 'max:2000'],
            'image' => ['sometimes', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'is_visible' => ['sometimes', 'boolean']
        ];
    }

    public function updatePost(): void
    {
        $post = $this->post->load('image');
        $post->update($this->safe()->except('image'));

        $this->whenHas('image', function (UploadedFile $image) use ($post) {
            if ($post->image) {
                $post->image()->update([
                    'path' => updateImage($image, $post->image->path, 'uploads/posts/')
                ]);
            } else {
                $post->image()->create([
                    'user_id' => $post->user_id,
                    'path' => uploadImage($image, 'uploads/posts/')
                ]);
            }
        });
    }
}
