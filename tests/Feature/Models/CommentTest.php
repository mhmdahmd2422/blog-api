<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;

it('belongs to a user', function () {
    $comment = Comment::factory()
        ->forUser()->create();

    expect($comment->user)
        ->toBeInstanceOf(User::class);
});

it('belongs to a post', function () {
    $comment = Comment::factory()
        ->forPost()->create();

    expect($comment->post)
        ->toBeInstanceOf(Post::class);
});
