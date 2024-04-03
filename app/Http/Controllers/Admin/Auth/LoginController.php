<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\LoginUserRequest;
use Illuminate\Http\Response;

class LoginController extends Controller
{
    public function store(LoginUserRequest $request): Response
    {
        $token = $request->loginUser();

        if ($token) {
            return response([
                'access_token' => $token,
                'message' => __('auth.login')
            ]);
        }

        return response([
            'message' => __('auth.failed'),
        ]);
    }
}
