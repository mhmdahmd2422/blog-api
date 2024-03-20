<?php

use App\Models\Category;
use App\Models\Image;
use App\Models\Post;

it('has an image', function () {
    $category = Category::factory()->hasImage()->create();

    expect($category->image)
        ->toBeInstanceOf(Image::Class);
});

it('has posts', function () {
    $category = Category::factory()->hasPosts(3)->create();

    expect($category->posts)
        ->toHaveCount(3)
        ->each->toBeInstanceOf(Post::class);
});
