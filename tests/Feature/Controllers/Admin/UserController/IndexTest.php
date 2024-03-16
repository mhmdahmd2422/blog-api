<?php

use App\Http\Resources\UserResource;
use App\Models\User;
use function Pest\Laravel\get;

it('get all users', function () {
    $users = User::factory()->count(10)->create();

    expect(User::all())
        ->toHaveCount(10);

    get(route('admin.users.index'))
        ->assertStatus(200)
        ->assertExactJson([
            'users' => getResponseData(UserResource::collection($users))
        ]);
});
