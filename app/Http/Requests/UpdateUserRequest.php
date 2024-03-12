<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:100'],
            'email' => ['sometimes', 'email'],
            'password' => ['sometimes', 'confirmed', Password::defaults()],
        ];
    }

    public function updateUser(): bool
    {
        return $this->user->update($this->request->all());
    }
}
