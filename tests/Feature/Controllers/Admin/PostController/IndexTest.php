<?php

use App\Http\Resources\Admin\PostSimpleResource;
use App\Models\Post;
use function Pest\Laravel\{get};

it('can get all posts', function () {
    $posts = Post::factory()->count(20)
        ->sequence(
            ['is_visible' => true],
            ['is_visible' => false],
        )->create();

    get(route('admin.posts.index'))
        ->assertStatus(200)
        ->assertExactJson([
            'posts' => responsePaginatedData(
                PostSimpleResource::collection($posts->load('images')
                    ->paginate(pagination_length('post')))
            )
        ]);
});
