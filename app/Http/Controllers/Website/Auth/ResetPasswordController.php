<?php

namespace App\Http\Controllers\Website\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Website\Auth\ResetPasswordRequest;
use Illuminate\Http\Response;

class ResetPasswordController extends Controller
{
    public function store(ResetPasswordRequest $request): Response
    {
        if ($request->resetPassword()) {
            return response([
                'message' => __('passwords.reset')
            ]);
        }

        return response([
            'message' => __('passwords.token')
        ], 400);
    }
}
