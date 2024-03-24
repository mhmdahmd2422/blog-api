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

it('has visible local scope', function () {
    $VisibleCategories = Category::factory()->visible()->count(5)->create();
    Category::factory()->invisible()->count(5)->create();

    expect(Category::visible()->get()->toArray())
        ->toEqual($VisibleCategories->toArray());
});
