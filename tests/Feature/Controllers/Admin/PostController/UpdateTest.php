<?php

use App\Http\Resources\PostResource;
use App\Models\Category;
use App\Models\Post;
use function Pest\Laravel\{put};

it('can update a post and its category', function () {
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
        ->create([
        'title' => 'updated title',
        'description' => str_repeat('updated description',5)
    ]);
    $response = put(route('admin.posts.update', $oldPost), [
        'user_id' => $updatedPost->user_id,
        'category_id' => $updatedPost->categories,
        'title' => $updatedPost->title,
        'description' => $updatedPost->description,
        'is_visible' => $updatedPost->is_visible,
    ]);

    $updatedPost->id = $oldPost->fresh()->id;

    $response->assertStatus(200)
        ->assertExactJson([
            'post' => responseData(PostResource::make($updatedPost)),
            'message' => __('posts.update')
        ]);

    $updatedPost->delete();

    expect($oldPost->user->posts)
        ->toHaveCount(1);

    $this->assertDatabaseHas(Post::class, [
        'id' => $oldPost->fresh()->id,
        'user_id' => $oldPost->user->id,
        'title' => 'updated title',
        'description' => str_repeat('updated description',5),
        'is_visible' => true
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
    $updatedPost = Post::factory()->for($oldPost->user)->visible()->create([
        'title' => 'updated title',
        'description' => 'updated description'
    ]);

    put(route('admin.posts.update', $oldPost), [[
        'user_id' => $updatedPost->user_id,
        'title' => $updatedPost->title,
        'description' => $updatedPost->description,
        'is_visible' => $updatedPost->is_visible,
    ], ...$badData])
        ->assertInvalid($errors);
})->with([
    [['user_id' => null], 'user_id'],
    [['user_id' => 1.5], 'user_id'],
    [['user_id' => true], 'user_id'],
    [['user_id' => 'string'], 'user_id'],
    [['title' => null], 'title'],
    [['title' => 2], 'title'],
    [['title' => 1.5], 'title'],
    [['title' => true], 'title'],
    [['title' => str_repeat('a', 256)], 'title'],
    [['title' => str_repeat('a', 9)], 'title'],
    [['description' => null], 'description'],
    [['description' => 2], 'description'],
    [['description' => 1.5], 'description'],
    [['description' => true], 'description'],
    [['description' => str_repeat('a', 2001)], 'description'],
    [['description' => str_repeat('a', 49)], 'description'],
    [['is_visible' => null], 'is_visible'],
    [['is_visible' => 5], 'is_visible'],
    [['is_visible' => 1.5], 'is_visible'],
    [['is_visible' => 'string'], 'is_visible'],
]);
