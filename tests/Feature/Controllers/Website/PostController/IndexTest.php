<?php

use App\Http\Resources\Website\PostSimpleResource;
use App\Models\Post;
use function Pest\Laravel\{get};

it('can get all visible posts', function () {
    Post::factory()->count(20)->hasCategories(2)
        ->sequence(
            ['is_visible' => true],
            ['is_visible' => false],
        )->create();

    get(route('website.posts.index'))
        ->assertStatus(200)
        ->assertExactJson([
            'posts' => responsePaginatedData(
                PostSimpleResource::collection(Post::visible()
                    ->paginate(pagination_length('post')))
            )
        ]);
});
