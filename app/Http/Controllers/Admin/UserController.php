<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        return response([
           'users' => UserResource::collection(User::all()),
        ]);
    }

    public function store(StoreUserRequest $request)
    {
        $user = $request->storeUser();

        return response([
            'user' => UserResource::make($user),
            'message' => 'User Created.'
        ]);
    }

    public function show(User $user)
    {
        return response([
            'user' => UserResource::make($user),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $request->updateUser();

        return response([
            'user' => UserResource::make($user),
            'message' => 'User Updated.'
        ]);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response([
            'message' => 'User Deleted.'
        ]);
    }
}
