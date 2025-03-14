<?php

use App\Http\Resources\Admin\PostResource;
use App\Models\Category;
use App\Models\Image;
use App\Models\Post;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use function Pest\Laravel\{post};

it('can store a new post', function () {
    $post = Post::factory()->invisible()->hasCategories(3)->create();

    $response = post(route('admin.posts.store'), [
        'user_id' => $post->user_id,
        'category_id' => $post->categories,
        'title' => $post->title,
        'description' => $post->description,
        'is_visible' => $post->is_visible,
    ]);

    $post->id = Post::orderBy('id', 'desc')->first()->id;
//    $post->created_at = Post::orderBy('id', 'desc')->first()->created_at;
//    $post->updated_at = Post::orderBy('id', 'desc')->first()->updated_at;

    $response
        ->assertStatus(200)
        ->assertExactJson([
            'post' => responseData(PostResource::make($post->load('images'))),
            'message' => __('posts.store')
        ]);

    $this->assertDatabaseHas(Post::class, [
        'user_id' => $post->user_id,
        'title' => $post->title,
        'description' => $post->description,
        'is_visible' => $post->is_visible
    ]);

    foreach ($post->categories as $category) {
        $this->assertDatabaseHas('category_post', [
            'category_id' => $category->id,
            'post_id' => $post->id
        ]);
    }
});

it('can store a post with images', function () {
    $post = Post::factory()->invisible()->hasCategories(1)->create();
    $image = UploadedFile::fake()->image('testImage.png');

    $response = post(route('admin.posts.store'), [
        'user_id' => $post->user_id,
        'category_id' => $post->categories,
        'title' => $post->title,
        'description' => $post->description,
        'images' => [$image, $image, $image],
        'is_visible' => $post->is_visible
    ]);

    $post->id = Post::orderBy('id', 'desc')->first()->id;

    $response
        ->assertStatus(200)
        ->assertJson([
            'post' => responseData(PostResource::make($post->load('images'))),
            'message' => __('posts.store')
        ]);

    $this->assertDatabaseHas(Post::class, [
        'user_id' => $post->user_id,
        'title' => $post->title,
        'description' => $post->description,
        'is_visible' => $post->is_visible
    ]);

    expect($post->images)
        ->toHaveCount(3);

    foreach ($post->images as $image) {
        $this->assertDatabaseHas(Image::class, [
            'imageable_type' => Post::class,
            'imageable_id' => $post->id,
            'path' => $image->path,
        ]);

        Storage::assertExists($image->path);
    }
});

it('requires a valid data when creating', function (array $badData, array|string $errors) {
    $post = Post::factory()->invisible()->hasCategories(1)->create();

    post(route('admin.posts.store'), [[
        'user_id' => $post->user_id,
        'category_id' => $post->categories,
        'title' => $post->title,
        'description' => $post->description,
        'is_visible' => $post->is_visible,
        'image' => [UploadedFile::fake()->image('testImage')]
    ], ...$badData])
        ->assertInvalid($errors);
})->with([
    [['user_id' => null], 'user_id'],
    [['user_id' => 1.5], 'user_id'],
    [['user_id' => true], 'user_id'],
    [['user_id' => 'string'], 'user_id'],
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
    [['images' => [null]], 'images.0'],
    [['images' => [
        UploadedFile::fake()->image('testImage1'),
        UploadedFile::fake()->image('testImage2'),
        UploadedFile::fake()->image('testImage3'),
    ]], ['images.0', 'images.1', 'images.2']], // max
    [['images' => [5]], 'images.0'],
    [['images' => [1.5]], 'images.0'],
    [['images' => ['string']], 'images.0'],
    [['images' => [UploadedFile::fake()->create('testImage', 2049)]], 'images.0'], // max size
]);
