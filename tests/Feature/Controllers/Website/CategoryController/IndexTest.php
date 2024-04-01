<?php

use App\Http\Resources\Website\CategoryResource;
use App\Models\Category;
use function Pest\Laravel\{get};

it('can get all visible categories', function () {
    Category::factory()->count(10)
        ->sequence(
            ['is_visible' => true],
            ['is_visible' => false],
        )->create();

    get(route('website.categories.index'))
        ->assertStatus(200)
        ->assertExactJson([
            'categories' => responseData(
                CategoryResource::collection(Category::visible()
                    ->paginate(pagination_length('category')))
            )
        ]);
});
