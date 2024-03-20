<?php

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use function Pest\Laravel\{get};

it('can show a category', function () {
    $categories = Category::factory()->count(20)
        ->sequence(
            ['is_visible' => true],
            ['is_visible' => false]
        )->create();

    get(route('website.categories.show', $categories->first()))
        ->assertStatus(200)
        ->assertExactJson([
            'category' => responseData(CategoryResource::make($categories->first()->load('image')))
        ]);
});

it('can not show an invisible post', function () {
    $categories = Category::factory()->count(20)
        ->sequence(
            ['is_visible' => false],
            ['is_visible' => true]
        )->create();

    get(route('website.categories.show', $categories->first()))
        ->assertStatus(404);
});
