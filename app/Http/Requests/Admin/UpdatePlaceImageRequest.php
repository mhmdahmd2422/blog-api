<?php

namespace App\Http\Requests\Admin;

use App\Models\Place;
use App\Rules\OneMainImage;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePlaceImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048', new OneMainImage($this->place, $this->is_main, $this->imageId)],
            'is_main' => ['sometimes', 'boolean']
        ];
    }

    public function updateImage(): Place|bool
    {
        $image = $this->place->whereHasImage($this->imageId);

        if ($image) {
            if ($this->is_main) {
                $this->place->main_image->update([
                    'is_main' => 0,
                ]);
            }
            $image->update([
                'path' => updateImage($this->file('image'), $image->path, 'uploads/places/'),
                'is_main' => $this->is_main ??= $image->is_main,
            ]);

            return $this->place->fresh();
        }

        return false;
    }
}
