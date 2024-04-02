<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LogoutController extends Controller
{
    public function store(Request $request): Response
    {
        $request->user()->token()->revoke();

        return response([
            'message' => __('auth.logout')
        ]);
    }
}
