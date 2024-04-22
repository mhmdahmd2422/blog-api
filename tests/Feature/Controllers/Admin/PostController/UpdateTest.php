<?php

use App\Http\Resources\Admin\PostResource;
use App\Models\Category;
use App\Models\Post;
use function Pest\Laravel\{put};

beforeEach(function () {
    loginAsUser();
});

it('can update a post', function () {
    $oldPost = Post::factory()->invisible()->has(
        Category::factory()->count(2)->sequence(
            ['id' => 1],
            ['id' => 2]
        )
    )->create();
    $updatedPost = Post::factory()->for($oldPost->user)->visible()
        ->has(
            Category::factory()->count(2)->sequence(
                ['id' => 3],
                ['id' => 4]
            )
        )
        ->create();
    $response = put(route('admin.posts.update', $oldPost), [
        'user_id' => $updatedPost->user_id,
        'category_id' => $updatedPost->categories->pluck('id')->toArray(),
        'title' => $updatedPost->title,
        'description' => $updatedPost->description,
        'is_visible' => $updatedPost->is_visible,
    ]);

    $updatedPost->id = $oldPost->fresh()->id;
    $updatedPost->slug = $oldPost->fresh()->slug;

    $response->assertStatus(200)
        ->assertExactJson([
            'post' => responseData(PostResource::make($updatedPost->load('images'))),
            'message' => __('posts.update')
        ]);

    $this->assertDatabaseHas(Post::class, [
        'id' => $updatedPost->id,
        'user_id' => $updatedPost->user_id,
        'title' => $updatedPost->title,
        'description' => $updatedPost->description,
        'is_visible' => $updatedPost->is_visible
    ]);

    foreach ($updatedPost->categories as $category) {
        $this->assertDatabaseHas('category_post', [
            'category_id' => $category->id,
            'post_id' => $updatedPost->id
        ]);
    }
});

it('requires a valid data when updating', function (array $badData, array|string $errors) {
    $oldPost = Post::factory()->invisible()->create();
    $updatedPost = Post::factory()->visible()->hasCategories(1)->create([
        'title' => 'updated title',
        'description' => 'updated description'
    ]);

    put(route('admin.posts.update', $oldPost), [[
        'title' => $updatedPost->title,
        'category_id' => $updatedPost->categories,
        'description' => $updatedPost->description,
        'is_visible' => $updatedPost->is_visible,
    ], ...$badData])
        ->assertInvalid($errors);
})->with([
    [['category_id' => null], 'category_id'],
    [['category_id' => 1.5], 'category_id'],
    [['category_id' => true], 'category_id'],
    [['category_id' => 'string'], 'category_id'],
    [['category_id' => [null]], 'category_id.0'],
    [['category_id' => [1.5]], 'category_id.0'],
    [['category_id' => [true]], 'category_id.0'],
    [['category_id' => ['string']], 'category_id.0'],
    [['category_id' => [1, 1]], 'category_id.0'], // distinct
    [['category_id' => [2]], 'category_id.0'], // exists
    [['title' => null], 'title'],
    [['title' => 2], 'title'],
    [['title' => 1.5], 'title'],
    [['title' => true], 'title'],
    [['title' => str_repeat('a', 4)], 'title'],
    [['title' => str_repeat('a', 256)], 'title'],
    [['description' => null], 'description'],
    [['description' => 2], 'description'],
    [['description' => 1.5], 'description'],
    [['description' => true], 'description'],
    [['description' => str_repeat('a', 1)], 'description'],
    [['description' => str_repeat('a', 2001)], 'description'],
    [['is_visible' => null], 'is_visible'],
    [['is_visible' => 5], 'is_visible'],
    [['is_visible' => 1.5], 'is_visible'],
    [['is_visible' => 'string'], 'is_visible'],
]);
