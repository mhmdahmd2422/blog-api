<?php

namespace App\Http\Requests\Admin;

use App\Models\Place;
use App\Rules\OneMainImage;
use Illuminate\Foundation\Http\FormRequest;

class StorePlaceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:1000'],
            'is_visible' => ['required', 'boolean'],
            'images' => ['sometimes', 'max:3', new OneMainImage],
            'images.*.image' => ['image', 'extensions:jpg,jpeg,png', 'max:2048'],
            'images.*.is_main' => ['boolean'],
            'tag_id' => ['sometimes'],
            'tag_id.*' => ['integer', 'distinct', 'exists:tags,id'],
            'specifications' => ['required', 'min:1'],
            'specifications.*.specification_id' => ['integer', 'exists:specifications,id'],
            'specifications.*.description' => ['string'],
        ];
    }

    public function storePlace()
    {
        $place = Place::create($this->safe()->except('images'));

        $place->tags()->sync(data_get($this->safe()->only(['tag_id']), 'tag_id'));

        $place->specifications()->sync($this->safe()->only(['specifications'])['specifications']);

        $this->whenHas('images', function (array $imagesInput) use ($place) {
            foreach ($imagesInput as $imageInput) {
                $place->images()->create([
                    'path' => uploadImage($imageInput['image'], 'uploads/places/'),
                    'is_main' => $imageInput['is_main'] ??= false,
                ]);
            }
        });

        return $place;
    }
}
