<?php

namespace App\Http\Requests\Admin;

use App\Rules\MaxAllowedImages;
use App\Rules\OneMainImage;
use Illuminate\Foundation\Http\FormRequest;

class StorePlaceImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'images' => ['sometimes', 'max:3', new MaxAllowedImages($this->place, 3), new OneMainImage($this->place)],
            'images.*.image' => ['image', 'extensions:jpg,jpeg,png', 'max:2048'],
            'images.*.is_main' => ['boolean'],
        ];
    }

    public function storeImages()
    {
        $this->whenHas('images', function (array $imageInputs) {
            foreach ($imageInputs as $imageInput) {
                if (isset($imageInput['is_main']) && $imageInput['is_main']) {
                    $this->place->main_image->update([
                        'is_main' => false,
                    ]);
                }
                $this->place->images()->create([
                    'path' => uploadImage($imageInput['image'], 'uploads/places/'),
                    'is_main' => $imageInput['is_main'] ??= false,
                ]);
            }
        });

        return $this->place;
    }
}
