<?php

namespace App\Http\Requests\Website\Auth;

use App\Models\User;
use App\Notifications\Website\ResetPasswordNotification;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'password' => ['required', 'string', 'confirmed', Password::defaults()]
        ];
    }

    public function resetPassword(): bool
    {
        if ($this->hasHeader('X-token')) {
            $user = User::where('reset_password_token', $this->header('X-token'))->first();

            $user->forceFill([
                'password' => $this->password,
                'reset_password_token' => null,
            ])->save();

            $user->notify(new ResetPasswordNotification);

            return true;
        }

        return false;
    }
}
