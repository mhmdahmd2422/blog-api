<?php

namespace App\Http\Requests\Website\Auth;

use App\Models\User;
use App\Notifications\Website\ForgetPasswordNotification;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class ForgetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email']
        ];
    }

    public function sendResetPasswordToken(): bool
    {
        $user = User::where(['email' => $this->email])->first();

        if (is_null($user)) {
            return false;
        }

        $user->update(['reset_password_token' => Str::uuid()->toString()]);

        $user->notify(new ForgetPasswordNotification);

        return true;
    }
}
