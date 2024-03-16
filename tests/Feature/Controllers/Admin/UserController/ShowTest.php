<?php

use App\Http\Resources\UserResource;
use App\Models\User;
use function Pest\Laravel\get;

it('show a user', function () {
   $user = User::factory()->create();

   get(route('admin.users.show', $user))
       ->assertStatus(200)
       ->assertExactJson([
           'user' => getResponseData(UserResource::make($user))
       ]);
});
