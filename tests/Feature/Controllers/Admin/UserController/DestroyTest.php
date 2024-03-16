<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use function Pest\Laravel\{delete};

it('can delete a user with all child models', function () {
    $user = User::factory()->create();
    $posts = Post::factory()->count(3)
        ->for($user)->hasComments(5)->create();

    delete(route('admin.users.destroy', $user))
        ->assertStatus(200)
        ->assertExactJson([
            'message' => __('users.destroy')
        ]);

    $this->assertDatabaseMissing(User::class, [
        'name' => $user->name,
        'email' => $user->email,
    ]);

    foreach ($posts as $post) {
        $this->assertDatabaseMissing(Post::class, [
            'id' => $post->id,
            'title' => $post->title,
            'description' => $post->description,
            'is_visible' => $post->is_visible
        ]);
    }

    foreach ($user->comments as $comment) {
        $this->assertDatabaseMissing(Comment::class, [
            'id' => $comment->id,
            'post_id' => $comment->post->id,
            'body' => $comment->body,
        ]);
    }
});
