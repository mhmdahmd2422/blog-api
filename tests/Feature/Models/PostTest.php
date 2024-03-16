<?php

use App\Models\Comment;
use App\Models\Image;
use App\Models\Post;
use App\Models\User;

it('belongs to a user', function () {
    $post = Post::factory()->forUser()->create();

    expect($post->user)
        ->toBeInstanceOf(User::class);
});

it('has comments', function () {
    $post = Post::factory()->hasComments(5)->create();

    expect($post->comments)
        ->toHaveCount(5)
        ->each->toBeInstanceOf(Comment::class);
});

it('has image', function () {
    $post = Post::factory()->hasImage()->create();

    expect($post->image)
        ->toBeInstanceOf(Image::class);
});
