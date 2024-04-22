<?php

namespace App\Http\Requests\Admin;

use App\Models\Post;
use App\Rules\OneMainImage;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePostImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048', new OneMainImage($this->post, $this->is_main, $this->imageId)],
            'is_main' => ['sometimes', 'boolean']
        ];
    }

    public function updateImage(): Post|bool
    {
        $image = $this->post->whereHasImage($this->imageId);

        if ($image) {
            if ($this->is_main) {
                $this->post->main_image->update([
                    'is_main' => 0,
                ]);
            }

            $image->update([
                'path' => updateImage($this->file('image'), $image->path, 'uploads/posts/'),
                'is_main' => $this->is_main ??= $image->is_main,
            ]);

            return $this->post->fresh();
        }

        return false;
    }
}
