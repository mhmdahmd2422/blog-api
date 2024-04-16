<?php

namespace App\Http\Controllers\Website\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Website\Auth\ForgetPasswordRequest;
use Illuminate\Http\Response;

class ForgetPasswordController extends Controller
{
    public function store(ForgetPasswordRequest $request): Response
    {
        $request->sendResetPasswordToken();

        return response([
            'message' => __('passwords.forget')
        ]);
    }
}
