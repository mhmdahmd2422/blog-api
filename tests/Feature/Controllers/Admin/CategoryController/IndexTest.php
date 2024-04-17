<?php

use App\Http\Resources\Admin\CategoryResource;
use App\Models\Category;
use function Pest\Laravel\{get};

beforeEach(function () {
    loginAsUser();
});

it('can get all categories', function () {
   $categories = Category::factory()->count(10)
       ->sequence(
           ['is_visible' => true],
           ['is_visible' => false],
       )->create();

   $paginationLength = pagination_length('category');

   get(route('admin.categories.index'))
       ->assertStatus(200)
       ->assertExactJson([
           'categories' => responseData(
               CategoryResource::collection($categories->load('image')
                   ->paginate($paginationLength))
           )
       ]);
});
