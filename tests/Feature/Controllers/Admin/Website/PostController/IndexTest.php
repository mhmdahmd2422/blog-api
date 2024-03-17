<?php

use App\Http\Resources\PostResource;
use App\Models\Post;
use function Pest\Laravel\{get};

it('can get all visible posts', function () {
    Post::factory()->count(20)
        ->sequence(
            ['is_visible' => true],
            ['is_visible' => false],
        )->create();

    get(route('website.posts.index'))
        ->assertStatus(200)
        ->assertExactJson([
            'posts' => responseData(PostResource::collection(Post::visible()->get()))
        ]);
});
