<?php

namespace App\Http\Requests\Admin;

use App\Models\Post;
use App\Rules\MaxAllowedImages;
use App\Rules\OneMainImage;
use Illuminate\Foundation\Http\FormRequest;

class StorePostImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'images' => ['sometimes', 'array', 'max:3', new MaxAllowedImages($this->post, 3), new OneMainImage($this->post)],
            'images.*.image' => ['required_with:images', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'images.*.is_main' => ['sometimes', 'boolean'],
        ];
    }

    public function storeImages(): Post
    {
        $this->whenHas('images', function (array $imageInputs) {
            foreach ($imageInputs as $imageInput) {
                if (isset($imageInput['is_main']) && $imageInput['is_main']) {
                    $this->post->main_image?->update([
                        'is_main' => false,
                    ]);
                }
                $this->post->images()->create([
                    'path' => uploadImage($imageInput['image'], 'uploads/posts/'),
                    'is_main' => $imageInput['is_main'] ??= false,
                ]);
            }
        });

        return $this->post;
    }
}
