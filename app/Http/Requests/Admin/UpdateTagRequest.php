<?php

namespace App\Http\Requests\Admin;

use App\Models\Tag;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z0-9_]+$/', Rule::unique('tags')->ignore($this->tag->name, 'name')],
            'is_visible' => ['sometimes', 'boolean']
        ];
    }

    public function updateTag(): Tag
    {
        $this->tag->update($this->validated());

        return $this->tag->fresh();
    }
}
