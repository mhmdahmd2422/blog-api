<?php

namespace App\Http\Requests\Admin;

use App\Models\Post;
use App\Rules\OneMainImage;
use Illuminate\Foundation\Http\FormRequest;

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
            'images' => ['sometimes', 'max:3', new OneMainImage],
            'images.*.image' => ['image', 'extensions:jpg,jpeg,png', 'max:2048'],
            'images.*.is_main' => ['boolean'],
            'is_visible' => ['required', 'boolean']
        ];
    }

    public function storePost()
    {
        $post = Post::create($this->safe()->except('images'));

        $post->categories()->sync(data_get($this->safe()->only(['category_id']), 'category_id'));

        $this->whenHas('images', function (array $imagesInput) use ($post) {
            foreach ($imagesInput as $imageInput) {
                $post->images()->create([
                    'path' => uploadImage($imageInput['image'], 'uploads/posts/'),
                    'is_main' => $imageInput['is_main'] ??= false,
                ]);
            }
        });

        return $post;
    }
}
