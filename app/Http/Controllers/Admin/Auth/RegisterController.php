<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\StoreUserRequest;
use App\Http\Resources\Admin\UserResource;
use Illuminate\Http\Response;

class RegisterController extends Controller
{
    public function store(StoreUserRequest $request): Response
    {
        $credentials = $request->storeUser();

        return response([
            'user' => UserResource::make($credentials['user']),
            'access_token' => $credentials['token'],
            'message' => __('auth.register')
        ]);
    }
}
