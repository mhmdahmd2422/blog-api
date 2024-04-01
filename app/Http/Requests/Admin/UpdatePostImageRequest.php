<?php

namespace App\Http\Requests\Admin;

use App\Models\Image;
use App\Models\Post;
use App\Rules\OneMainImage;
use Illuminate\Database\Eloquent\Builder;
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
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048', new OneMainImage($this->post)],
            'is_main' => ['sometimes', 'boolean']
        ];
    }

    public function updateImage(): Post|bool
    {
        $image = Image::whereHasMorph('imageable',
            Post::class,
            function (Builder $query) {
                $query->whereId($this->post->id);
            }
        )->whereId($this->imageId)->first();

        if ($image) {
            if ($this->is_main) {
                $this->post->main_image->update([
                    'is_main' => false,
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
