<?php

use App\Models\User;
use App\Notifications\Website\UserRegisteredNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Laravel\Passport\ClientRepository;
use function Pest\Laravel\{post};

beforeEach(function () {
    Notification::fake();

    $clientRepository = new ClientRepository();
    $this->client = $clientRepository->createPersonalAccessClient(
        null, 'Test Personal Access Client', '/'
    );
    DB::table('oauth_personal_access_clients')->insert([
        'client_id' => $this->client->id,
        'created_at' => date('Y-m-d'),
        'updated_at' => date('Y-m-d'),
    ]);
});

it('can register a user', function () {

    post(route('website.auth.register.store'), [
        'name' => $name = fake()->name(),
        'email' => $email = fake()->email(),
        'password' => 'Password@123',
        'password_confirmation' => 'Password@123'
    ])
        ->assertStatus(200);

    $created_user = User::first();

    expect($created_user->name)
        ->toEqual($name)
        ->and($created_user->email)
        ->toEqual($email);

    Notification::assertSentTo(
        [$created_user], UserRegisteredNotification::class
    );

    Notification::assertCount(1);
});

it('requires a valid data when registering', function (array $badData, array|string $errors) {
    post(route('website.auth.register.store'), [[
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
    [['name' => str_repeat('a', 3)], 'name'],
    [['name' => str_repeat('a', 51)], 'name'],
    [['email' => null], 'email'],
    [['email' => 1], 'email'],
    [['email' => 1.5], 'email'],
    [['email' => true], 'email'],
    [['email' => str_repeat('a', 20)], 'email'],
    [['email' => str_repeat('a', 50)], 'email'],
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
