<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Response;

class UserController extends Controller
{
    public function index(): Response
    {
        return response([
           'users' => UserResource::collection(User::all()),
        ]);
    }

    public function store(StoreUserRequest $request): Response
    {
        $user = $request->storeUser();

        return response([
            'user' => UserResource::make($user),
            'message' => __('users.store')
        ]);
    }

    public function show(User $user): Response
    {
        return response([
            'user' => UserResource::make($user),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): Response
    {
        $request->updateUser();

        return response([
            'user' => UserResource::make($user),
            'message' => __('users.update')
        ]);
    }

    public function destroy(User $user): Response
    {
        $user->remove();

        return response([
            'message' => __('users.destroy')
        ]);
    }
}
