<?php

use App\Http\Resources\UserResource;
use App\Models\User;
use function Pest\Laravel\{put};

it('can update a user', function () {
    $user = User::factory()->create();
    $updatedUser = User::factory()->make();

    $response = put(route('admin.users.update', $user), [
        'name' => $updatedUser->name,
        'email' => $updatedUser->email,
    ]);

    $updatedUser->id = $user->fresh()->id;

    $response
        ->assertStatus(200)
        ->assertExactJson([
            'user' => getResponseData(UserResource::make($updatedUser)),
            'message' => __('users.update')
        ]);

    $this->assertDatabaseHas(User::class, [
        'name' => $updatedUser->name,
        'email' => $updatedUser->email,
    ]);
});

it('cannot update a user with a repeated email', function () {
    $firstUser = User::factory()->create();
    $secondUser = User::factory()->create();

    put(route('admin.users.update', $firstUser), [
        'name' => $secondUser->name,
        'email' => $secondUser->email,
        'password' => 'Password@123',
        'password_confirmation' => 'Password@123',
    ])
        ->assertStatus(302)
        ->assertInvalid([
            "email" => [
                "The email has already been taken."
            ]
        ]);
});

it('requires a valid data when creating', function (array $badData, array|string $errors) {
    $user = User::factory()->create();

    put(route('admin.users.update', $user), [[
        'name' => fake()->name(),
        'email' => fake()->email(),
        'password' => 'Password@123',
        'password_confirmation' => 'Password@123',
    ], ...$badData])
        ->assertInvalid($errors);
})->with([
    [['name' => null], 'name'],
    [['name' => 1], 'name'],
    [['name' => 1.5], 'name'],
    [['name' => true], 'name'],
    [['name' => str_repeat('a', 4)], 'name'],
    [['name' => str_repeat('a', 51)], 'name'],
    [['email' => null], 'email'],
    [['email' => 1], 'email'],
    [['email' => 1.5], 'email'],
    [['email' => true], 'email'],
    [['email' => str_repeat('a', 20)], 'email'],
    [['password' => null, 'password_confirmation' => null], 'password'],
    [['password' => 1, 'password_confirmation' => 1], 'password'],
    [['password' => 1.5, 'password_confirmation' => 1.5], 'password'],
    [['password' => true, 'password_confirmation' => true], 'password'],
    [['password' => 'aA@1', 'password_confirmation' => 'aA@1'], 'password'],
    [['password' => str_repeat('aA@1', 7), 'password_confirmation' => str_repeat('aA@1', 7)], 'password'],
    [['password' => 'a@123456', 'password_confirmation' => 'a@123456'], 'password'],
    [['password' => 'A@123456', 'password_confirmation' => 'A@123456'], 'password'],
    [['password' => '@1234567', 'password_confirmation' => '@1234567'], 'password'],
    [['password' => str_repeat('Aa@', 3), 'password_confirmation' => str_repeat('Aa@', 3)], 'password'],
    [['password' => str_repeat('Aa1', 3), 'password_confirmation' => str_repeat('Aa1', 3)], 'password'],
]);
