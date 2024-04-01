<?php

namespace App\Http\Requests\Admin;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:25'],
            'image' => ['sometimes', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'is_visible' => ['required', 'boolean']
        ];
    }

    public function storeCategory(): Category
    {
        $category = Category::create($this->safe()->except('image'));

        $this->whenHas('image', function (UploadedFile $image) use ($category) {
            $category->image()->create([
                'path' => uploadImage($image, 'uploads/categories/'),
                'is_main' => true
            ]);
        });

        return $category;
    }
}
