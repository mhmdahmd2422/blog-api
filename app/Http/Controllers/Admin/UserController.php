<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Http\Resources\Admin\UserResource;
use App\Models\User;
use Illuminate\Http\Response;

class UserController extends Controller
{
    public function index(): Response
    {
        $paginationLength = pagination_length('user');

        return response([
           'users' => UserResource::collection(User::paginate($paginationLength))
        ]);
    }

    public function show(User $user): Response
    {
        return response([
            'user' => UserResource::make($user->loadCount('posts', 'comments')),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): Response
    {
        $user = $request->updateUser();

        return response([
            'user' => UserResource::make($user),
            'message' => __('users.update')
        ]);
    }

    public function destroy(User $user): Response
    {
        if ($user->remove()) {
            return response([
                'message' => __('users.destroy')
            ]);
        }

        return response([
            'message' => __('users.cant_destroy'),
        ], 409);
    }
}
