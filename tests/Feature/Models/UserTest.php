<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;

it('has posts', function () {
    $user = User::factory()->hasPosts(5)->create();

    expect($user->posts)
        ->toHaveCount(5)
        ->each->toBeInstanceOf(Post::class);
});

it('has comments', function () {
    $user = User::factory()->has(Post::factory()->hasComments(5))->create();

    expect($user->comments)
        ->toHaveCount(5)
        ->each->toBeInstanceOf(Comment::class);
});
