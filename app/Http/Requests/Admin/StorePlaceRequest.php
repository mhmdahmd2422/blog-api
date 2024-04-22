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
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'description' => ['required', 'string', 'min:2', 'max:1000'],
            'is_visible' => ['required', 'boolean'],
            'images' => ['sometimes', 'array', 'max:3', new OneMainImage],
            'images.*.image' => ['image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'images.*.is_main' => ['sometimes', 'boolean'],
            'tag_id' => ['sometimes', 'array'],
            'tag_id.*' => ['required_with:tag_id', 'integer', 'distinct', 'exists:tags,id'],
            'specifications' => ['required', 'array', 'min:1'],
            'specifications.*.specification_id' => ['required_with:specifications', 'integer', 'distinct', 'exists:specifications,id'],
            'specifications.*.description' => ['required_with:specifications', 'string', 'min:2', 'max:100']
        ];
    }

    public function storePlace(): Place
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
