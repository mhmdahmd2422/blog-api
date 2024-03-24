<?php

namespace App\Http\Requests\Admin;

use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

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
            'category_id' => ['required'],
            'category_id.*' => ['integer', 'distinct', 'exists:categories,id'],
            'title' => ['required', 'string', 'min:10', 'max:255'],
            'description' => ['required', 'string', 'min:50', 'max:2000'],
            'images' => ['sometimes', 'max:3'],
            'images.*' => ['image', 'extensions:jpg,jpeg,png', 'max:2048'],
            'is_visible' => ['required', 'boolean']
        ];
    }

    public function storePost(): Post
    {
        $post = Post::create($this->safe()->except('images'));

        $post->categories()->sync(data_get($this->safe()->only(['category_id']), 'category_id'));

        $this->whenHas('images', function (array $images) use ($post) {
            foreach ($images as $image) {
                $post->images()->create([
                    'path' => uploadImage($image, 'uploads/posts/')
                ]);
            }
        });

        return $post->fresh();
    }
}
