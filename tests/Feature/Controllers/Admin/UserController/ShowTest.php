<?php

use App\Http\Resources\Admin\UserResource;
use App\Models\User;
use function Pest\Laravel\get;

beforeEach(function () {
    loginAsUser();
});

it('show a user', function () {
   $user = User::factory()->create();

   get(route('admin.users.show', $user))
       ->assertStatus(200)
       ->assertExactJson([
           'user' => responseData(UserResource::make($user->loadCount('posts', 'comments')))
       ]);
});
