<?php

use App\Http\Resources\PostResource;
use App\Models\Post;
use function Pest\Laravel\{get};

it('can show a post', function () {
    $posts = Post::factory()->count(20)
        ->sequence(
            ['is_visible' => true],
            ['is_visible' => false],
        )->create();

    get(route('admin.posts.show', $posts->first()))
        ->assertStatus(200)
        ->assertExactJson([
            'post' => responseData(PostResource::make($posts->first()->load('images')))
        ]);
});
