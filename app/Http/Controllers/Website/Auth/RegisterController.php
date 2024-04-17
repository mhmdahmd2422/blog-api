<?php

namespace App\Http\Controllers\Website\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Website\Auth\StoreUserRequest;
use App\Http\Resources\Website\UserResource;
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
