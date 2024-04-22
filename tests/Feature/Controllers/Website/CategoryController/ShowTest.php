<?php

use App\Http\Resources\Website\CategoryResource;
use App\Http\Resources\Website\PostSimpleResource;
use App\Models\Category;
use function Pest\Laravel\{get};

beforeEach(function () {
    loginAsUser();
});

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

it('can not show an invisible category', function () {
    $categories = Category::factory()->invisible()->create();

    get(route('website.categories.show', $categories->first()))
        ->assertStatus(404);
});
