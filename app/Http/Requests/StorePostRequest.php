<?php

namespace App\Http\Requests;

use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
            'image' => ['sometimes', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'is_visible' => [
                Rule::when(request()->routeIs('website.posts.store'), 'exclude'),
                Rule::when(request()->routeIs('admin.posts.store'), 'required'),
                'boolean'
            ],
        ];
    }

//    protected function passedValidation(): void
//    {
//        $this->replace(['image' => uploadImage($this->request->all(), 'uploads/posts')]);
//    }

    public function storePost(): Post
    {
        return Post::create($this->request->all());
    }
}
