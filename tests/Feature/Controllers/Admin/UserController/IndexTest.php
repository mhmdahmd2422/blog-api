<?php

use App\Http\Resources\Admin\UserResource;
use App\Models\User;
use function Pest\Laravel\get;

it('get all users', function () {
    $users = User::factory()->count(10)->create();

    expect(User::all())
        ->toHaveCount(10);

    get(route('admin.users.index'))
        ->assertStatus(200)
        ->assertExactJson([
            'users' => responsePaginatedData(
                UserResource::collection($users->paginate(pagination_length('user')))
            )
        ]);
});
