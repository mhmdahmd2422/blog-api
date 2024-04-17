<?php

use App\Http\Resources\Admin\PostSimpleResource;
use App\Models\Post;
use function Pest\Laravel\{get};

beforeEach(function () {
    loginAsUser();
});

it('can get all posts', function () {
    $posts = Post::factory()->count(20)->hasImages(3)
        ->sequence(
            ['is_visible' => true],
            ['is_visible' => false],
        )->create();

    get(route('admin.posts.index'))
        ->assertStatus(200)
        ->assertExactJson([
            'posts' => responseData(
                PostSimpleResource::collection($posts->load('images')
                    ->paginate(pagination_length('post')))
            )
        ]);
});
