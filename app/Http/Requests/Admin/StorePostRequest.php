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
            'category_id' => ['required', 'array'],
            'category_id.*' => ['required_with:category_id', 'integer', 'distinct', 'exists:categories,id'],
            'title' => ['required', 'string', 'min:5', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
            'images' => ['sometimes', 'array', 'max:3', new OneMainImage],
            'images.*.image' => ['required_with:images', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'images.*.is_main' => ['sometimes', 'boolean'],
            'is_visible' => ['required', 'boolean']
        ];
    }

    public function storePost(): Post
    {
        $post = Post::create($this->safe()->merge(['user_id' => auth()::id()])->except('images'));

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
