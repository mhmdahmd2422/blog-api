<?php

use App\Models\Category;
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

it('has images', function () {
    $post = Post::factory()->hasImages(3)->create();

    expect($post->images)
        ->toHaveCount(3)
        ->each->toBeInstanceOf(Image::class);
});

it('has categories', function () {
    $post = Post::factory()->hasCategories(3)->create();

    expect($post->categories)
        ->toHaveCount(3)
        ->each->toBeInstanceOf(Category::class);
});
