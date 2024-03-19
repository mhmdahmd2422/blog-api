<?php

namespace App\Http\Requests;

use App\Models\Image;
use App\Models\Post;
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
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }

    public function updateImage()
    {
        $image = Image::whereHasMorph(
            'imageable',
            Post::class,
            function (Builder $query) {
                $query->whereId($this->post->id);
            }
        )->whereId($this->imageId)->first();

        if ($image) {
            $image->update([
                'path' => updateImage($this->file('image'), $image->path, 'uploads/posts/')
            ]);

            return $this->post->load('images');
        }

        return false;
    }
}
