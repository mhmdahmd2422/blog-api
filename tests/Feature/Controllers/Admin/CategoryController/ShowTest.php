<?php

use App\Http\Resources\Admin\CategoryResource;
use App\Http\Resources\Admin\PostSimpleResource;
use App\Models\Category;
use function Pest\Laravel\{get};

it('can show a category with its assigned posts', function () {
   $category = Category::factory()->invisible()->hasPosts(10)->create();

    get(route('admin.categories.show', $category))
        ->assertStatus(200)
        ->assertExactJson([
            'category' => responseData(CategoryResource::make($category->load('image'))),
            'posts' => responsePaginatedData(
                PostSimpleResource::collection($category->posts
                    ->paginate(pagination_length('post')))
            )
        ]);
});
