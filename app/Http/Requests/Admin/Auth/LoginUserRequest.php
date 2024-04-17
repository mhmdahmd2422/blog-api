<?php

namespace App\Http\Requests\Admin\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class LoginUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', Password::defaults()]
        ];
    }

    public function loginUser(): array|bool
    {
        if (Auth::attempt($this->validated())) {
            $user = User::find(Auth::id());

            return [
                'user' => $user,
                'token' => $user->createToken('User')->accessToken
            ];
        }

        return false;
    }
}
