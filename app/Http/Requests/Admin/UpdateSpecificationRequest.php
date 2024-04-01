<?php

namespace App\Http\Requests\Admin;

use App\Models\Specification;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;

class UpdateSpecificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:100', Rule::unique('specifications')->ignore($this->specification->name, 'name')],
            'image' => ['sometimes', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }

    public function updateSpecification(): Specification
    {
        $specification = $this->specification;
        $specification->update($this->safe()->except('image'));

        $this->whenHas('image', function (UploadedFile $image) use ($specification) {
            if ($specification->image) {
                $specification->image()->update([
                    'path' => updateImage($image, $specification->image->path, 'uploads/specifications/'),
                    'is_main' => true
                ]);
            } else {
                $specification->image()->create([
                    'path' => uploadImage($image, 'uploads/specifications/'),
                    'is_main' => true
                ]);
            }
        });

        return $specification->fresh();
    }
}
