<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\StoreUserRequest;
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
