<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
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
            'name' => ['sometimes', 'string', 'min:2', 'min:5', 'max:50'],
            'email' => ['sometimes', 'string', 'email', Rule::unique('users')->ignore($this->user->email, 'email')],
            'password' => ['sometimes', 'confirmed', Password::defaults()]
        ];
    }

    public function updateUser(): User
    {
        $this->user->update($this->validated());

        return $this->user->fresh();
    }
}
