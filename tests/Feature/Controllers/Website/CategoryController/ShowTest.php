<?php

use App\Http\Resources\Website\CategoryResource;
use App\Http\Resources\Website\PostSimpleResource;
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
            'category' => responseData(CategoryResource::make($categories->first()->load('image'))),
            'posts' => responseData(
                PostSimpleResource::collection($categories->first()->posts
                ->paginate(pagination_length('post')))
            )
        ]);
});

it('can not show an invisible category', function () {
    $categories = Category::factory()->invisible()->create();

    get(route('website.categories.show', $categories->first()))
        ->assertStatus(404);
});
