<?php

use App\Http\Resources\PostResource;
use App\Models\Image;
use App\Models\Post;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use function Pest\Laravel\{post};

it('can store a new post', function () {
    $post = Post::factory()->invisible()->make();

    post(route('admin.posts.store'), [
        'user_id' => $post->user_id,
        'title' => $post->title,
        'description' => $post->description,
        'is_visible' => $post->is_visible
    ])
        ->assertStatus(200)
        ->assertExactJson([
            'post' => responseData(PostResource::make(Post::first())),
            'message' => __('posts.store')
        ]);

    $this->assertDatabaseHas(Post::class, [
        'user_id' => $post->user_id,
        'title' => $post->title,
        'description' => $post->description,
        'is_visible' => $post->is_visible
    ]);
});

it('can store a post with image', function () {
    $post = Post::factory()->invisible()->make();
    $image = UploadedFile::fake()->image('testImage.png');

    post(route('admin.posts.store'), [
        'user_id' => $post->user_id,
        'title' => $post->title,
        'description' => $post->description,
        'image' => $image,
        'is_visible' => $post->is_visible
    ])
        ->assertStatus(200)
        ->assertExactJson([
            'post' => responseData(PostResource::make(Post::first()->load('image'))),
            'message' => __('posts.store')
        ]);

    $this->assertDatabaseHas(Post::class, [
        'user_id' => $post->user_id,
        'title' => $post->title,
        'description' => $post->description,
        'is_visible' => $post->is_visible
    ]);

    $createdPost = Post::first();

    expect($createdPost->image)
        ->toEqual(Image::first());

    $this->assertDatabaseHas(Image::class, [
        'imageable_type' => Post::class,
        'imageable_id' => $createdPost->id,
        'path' => $createdPost->image->path,
    ]);

    Storage::assertExists('uploads/posts/'.$image->hashName());
});

it('requires a valid data when creating', function (array $badData, array|string $errors) {
    $post = Post::factory()->invisible()->create();

    post(route('admin.posts.store'), [[
        'user_id' => $post->user_id,
        'title' => $post->title,
        'description' => $post->description,
        'is_visible' => $post->is_visible,
        'image' => UploadedFile::fake()->image('testImage')
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
    [['image' => null], 'image'],
    [['image' => 5], 'image'],
    [['image' => 1.5], 'image'],
    [['image' => 'string'], 'image'],
]);
