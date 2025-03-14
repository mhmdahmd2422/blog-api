<?php

namespace App\Http\Requests\Admin;

use App\Models\Tag;
use Illuminate\Foundation\Http\FormRequest;

class StoreTagRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'regex:/^[a-zA-Z0-9_]+$/', 'unique:tags,name'],
            'is_visible' => ['required', 'boolean']
        ];
    }

    public function storeTag()
    {
        return Tag::create($this->validated());
    }
}
