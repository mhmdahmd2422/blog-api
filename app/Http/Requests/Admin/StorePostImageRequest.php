<?php

namespace App\Http\Requests\Admin;

use App\Rules\MaxAllowedImages;
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
            'images' => ['sometimes', 'max:3', new MaxAllowedImages($this->post, 3)],
            'images.*' => ['image', 'extensions:jpg,jpeg,png', 'max:2048'],
        ];
    }

    public function storeImages()
    {
        $this->whenHas('images', function (array $images) {
            foreach ($images as $image) {
                $this->post->images()->create([
                    'path' => uploadImage($image, 'uploads/posts/')
                ]);
            }
        });

        return $this->post->fresh();
    }
}
