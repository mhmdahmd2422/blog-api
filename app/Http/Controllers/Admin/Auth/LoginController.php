<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\LoginUserRequest;
use App\Http\Resources\Admin\UserResource;
use Illuminate\Http\Response;

class LoginController extends Controller
{
    public function store(LoginUserRequest $request): Response
    {
        $credentials = $request->loginUser();

        if ($credentials) {
            return response([
                'user' => UserResource::make($credentials['user']),
                'access_token' => $credentials['token'],
                'message' => __('auth.login')
            ]);
        }

        return response([
            'message' => __('auth.failed'),
        ]);
    }
}
