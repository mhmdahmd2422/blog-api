<?php

use App\Http\Resources\Admin\CategoryResource;
use App\Http\Resources\Admin\PostSimpleResource;
use App\Models\Category;
use function Pest\Laravel\{get};

it('can show a category with its assigned posts', function () {
   $categories = Category::factory()->count(10)->invisible()->hasPosts(10)->create();

    get(route('admin.categories.show', $categories->first()))
        ->assertStatus(200)
        ->assertExactJson([
            'category' => responseData(CategoryResource::make($categories->first()->load('image'))),
            'posts' => responsePaginatedData(
                PostSimpleResource::collection($categories->first()->posts
                    ->paginate(pagination_length('post')))
            )
        ]);
});
