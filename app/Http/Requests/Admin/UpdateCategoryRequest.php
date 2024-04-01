<?php

namespace App\Http\Requests\Admin;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:25'],
            'image' => ['sometimes', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'is_visible' => ['sometimes', 'boolean']
        ];
    }

    public function updateCategory(): Category
    {
        $category = $this->category;
        $category->update($this->safe()->except('image'));

        $this->whenHas('image', function (UploadedFile $image) use ($category) {
            if ($category->image) {
                $category->image()->update([
                    'path' => updateImage($image, $category->image->path, 'uploads/categories/'),
                    'is_main' => true
                ]);
            } else {
                $category->image()->create([
                    'path' => uploadImage($image, 'uploads/categories/'),
                    'is_main' => true
                ]);
            }
        });

        return $category->fresh();
    }
}
