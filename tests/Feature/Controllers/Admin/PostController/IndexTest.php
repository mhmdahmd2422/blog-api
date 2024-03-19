<?php

use App\Http\Resources\PostResource;
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
            'posts' => responseData(PostResource::collection($posts->load('images')))
        ]);
});
