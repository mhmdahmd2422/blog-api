<?php

use App\Models\Comment;
use App\Models\Image;
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

it('has images', function () {
    $user = User::factory()->create();
    $post = Post::factory()->for($user)->create();
    $images = Image::factory()->count(5)
        ->for($post, 'imageable')
        ->for($user)->create();

    expect($user->images)
        ->toHaveCount(5)
        ->each->toBeInstanceOf(Image::class);
});
