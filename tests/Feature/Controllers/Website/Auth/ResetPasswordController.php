<?php

use App\Models\User;
use App\Notifications\Website\ResetPasswordNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use function Pest\Laravel\{post};

it('can reset password', function () {
    Notification::fake();
    $user = User::factory()->create([
       'reset_password_token' => $token = Str::uuid()
   ]);

   post(route('website.auth.reset.store'), [
       'password' => $new_password = 'New_password222',
       'password_confirmation' => $new_password,
   ], ['X-token' => $token])
       ->assertStatus(200)
       ->assertExactJson([
           'message' => __('passwords.reset')
       ]);

   expect(Hash::check($new_password, $user->fresh()->password))
       ->toBeTrue()
       ->and($user->fresh()->reset_password_token)
       ->toBeNull();

   Notification::assertSentTo(
       [$user], ResetPasswordNotification::class
   );

   Notification::assertCount(1);
});
