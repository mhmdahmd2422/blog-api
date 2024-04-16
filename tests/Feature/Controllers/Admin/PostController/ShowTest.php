<?php

use App\Http\Resources\Admin\PostResource;
use App\Models\Post;
use function Pest\Laravel\{get};

beforeEach(function () {
    loginAsUser();
});

it('can show a post', function () {
    $post = Post::factory()->invisible()->hasCategories(2)->create();

    get(route('admin.posts.show', $post))
        ->assertStatus(200)
        ->assertExactJson([
            'post' => responseData(PostResource::make($post->load('images', 'user', 'categories')))
        ]);
});
