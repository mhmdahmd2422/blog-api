<?php

use App\Http\Resources\Website\PostResource;
use App\Models\Post;
use function Pest\Laravel\{get};

beforeEach(function () {
    loginAsUser();
});

it('can show a post', function () {
    $posts = Post::factory()->count(20)->hasCategories(3)
        ->sequence(
            ['is_visible' => true],
            ['is_visible' => false]
        )->create();

    get(route('website.posts.show', $posts->first()))
        ->assertStatus(200)
        ->assertExactJson([
            'post' => responseData(PostResource::make($posts->first()->load('images', 'categories')))
        ]);
});

it('can not show an invisible post', function () {
    $posts = Post::factory()->count(20)
        ->sequence(
            ['is_visible' => false],
            ['is_visible' => true],
        )->create();

    get(route('website.posts.show', $posts->first()))
        ->assertStatus(404);
});
