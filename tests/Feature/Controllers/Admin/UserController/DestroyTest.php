<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use function Pest\Laravel\{delete};

it('can not delete a user who has posts', function () {
    $user = User::factory()->create();
    Post::factory()->for($user)->create();

    delete(route('admin.users.destroy', $user))
        ->assertStatus(409);
});

it('can delete a user who has comments only and delete his comments', function () {
    $user = User::factory()->create();
    Comment::factory()->count(3)->for($user)->hasPost()->create();

    delete(route('admin.users.destroy', $user))
        ->assertStatus(200)
        ->assertExactJson([
            'message' => __('users.destroy')
        ]);

    $this->assertDatabaseMissing(User::class, [
        'name' => $user->name,
        'email' => $user->email,
    ]);

    foreach ($user->comments as $comment) {
        $this->assertDatabaseMissing(Comment::class, [
            'id' => $comment->id,
            'post_id' => $comment->post->id,
            'body' => $comment->body,
        ]);
    }
});
