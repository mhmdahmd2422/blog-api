<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:5', 'max:50'],
            'email' => ['required', 'email', Rule::unique('users')],
            'password' => ['confirmed', Password::defaults()]
        ];
    }

    public function storeUser(): User
    {
        return User::create($this->validated());
    }
}
