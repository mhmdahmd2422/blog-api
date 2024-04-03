<?php

namespace App\Http\Controllers\Website\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Website\Auth\StoreUserRequest;
use Illuminate\Http\Response;

class RegisterController extends Controller
{
    public function store(StoreUserRequest $request): Response
    {
        $token = $request->storeUser();

        return response([
            'access_token' => $token,
            'message' => __('auth.login')
        ]);
    }
}
