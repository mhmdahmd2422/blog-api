<?php

namespace App\Http\Requests\Website\Auth;

use App\Models\User;
use App\Notifications\Website\UserRegisteredNotification;
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
            'email' => ['required', 'string', 'email', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'confirmed', Password::defaults()]
        ];
    }

    public function storeUser(): array
    {
        $user = User::create($this->validated());

        $user->notify(new UserRegisteredNotification);

        return [
            'user' => $user,
            'token' => $user->createToken('User')->accessToken
        ];
    }
}
