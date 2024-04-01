<?php

namespace App\Http\Requests\Admin;

use App\Models\Image;
use App\Models\Place;
use App\Rules\OneMainImage;
use Illuminate\Database\Eloquent\Builder;
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
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048', new OneMainImage($this->place)],
            'is_main' => ['sometimes', 'boolean']
        ];
    }

    public function updateImage(): Place|bool
    {
        $image = Image::whereHasMorph('imageable', Place::class,
            function (Builder $query) {
                $query->whereId($this->place->id);
            }
        )->whereId($this->imageId)->first();

        if ($image) {
            if ($this->is_main) {
                $this->place->main_image->update([
                    'is_main' => false,
                ]);
            }
            $image->update([
                'path' => updateImage($this->file('image'), $image->path, 'uploads/places/'),
                'is_main' => $this->is_main ??= false,
            ]);

            return $this->place->fresh();
        }

        return false;
    }
}
