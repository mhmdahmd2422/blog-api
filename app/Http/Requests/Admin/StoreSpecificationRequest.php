<?php

namespace App\Http\Requests\Admin;

use App\Models\Specification;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

class StoreSpecificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50', 'unique:specifications,name'],
            'image' => ['sometimes', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }

    public function storeSpecification()
    {
        $specification = Specification::create($this->safe()->except('image'));

        $this->whenHas('image', function (UploadedFile $image) use ($specification) {
            $specification->image()->create([
                'path' => uploadImage($image, 'uploads/specifications/'),
                'is_main' => true
            ]);
        });

        return $specification;
    }
}
