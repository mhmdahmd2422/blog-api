<?php

use App\Models\Image;
use App\Models\Post;
use App\Models\User;

it('belong to a user', function () {
    $image = Image::factory()->for(Post::factory(), 'imageable')->create();

    expect($image->user)
        ->toBeInstanceOf(User::class);
});

it('belong to a post', function () {
   $post = Post::factory()->create();
   $image = Image::factory()->for($post, 'imageable')->create();

    expect($image->imageable)
        ->toBeInstanceOf(Post::class);
});
