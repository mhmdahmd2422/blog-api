<?php

use App\Models\User;
use App\Notifications\Website\ForgetPasswordNotification;
use Illuminate\Support\Facades\Notification;
use function Pest\Laravel\{post};

it('can request forget password', function () {
   Notification::fake();
   $user = User::factory()->create();

   post(route('website.auth.forget.store'), [
       'email' => $user->email,
   ])
       ->assertStatus(200)
       ->assertExactJson([
           'message' => __('passwords.forget')
       ]);

   expect($user->fresh()->reset_password_token)
       ->toBeUuid();

   Notification::assertSentTo(
       [$user], ForgetPasswordNotification::class
   );

   Notification::assertCount(1);
});
