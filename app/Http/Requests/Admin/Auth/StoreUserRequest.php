<?php

namespace App\Http\Requests\Admin\Auth;

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
            'name' => ['required', 'string', 'min:2', 'max:50'],
            'email' => ['required', 'string', 'email', 'max:50', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'confirmed', Password::defaults()]
        ];
    }

    public function storeUser(): array
    {
        $user = User::create($this->validated());

        return [
            'user' => $user,
            'token' => $user->createToken('User')->accessToken
        ];
    }
}
