<?php

use App\Http\Resources\Admin\PostResource;
use App\Models\Image;
use App\Models\Post;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use function Pest\Laravel\{post};

beforeEach(function () {
    loginAsUser();
});

it('can store a post', function () {
    $post = Post::factory()->invisible()->hasCategories(1)->create();
    $image = UploadedFile::fake()->image('testImage.png');

    $response = post(route('admin.posts.store'), [
        'user_id' => $post->user_id,
        'category_id' => $post->categories->pluck('id')->toArray(),
        'title' => $post->title,
        'description' => $post->description,
        'images' => [
            ['image' => $image],
            ['image' => $image],
            ['image' => $image, 'is_main' => 1]
        ],
        'is_visible' => $post->is_visible
    ]);

    $post->id = Post::orderBy('id', 'desc')->first()->id;
    $post->slug = Post::orderBy('id', 'desc')->first()->slug;

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

    $this->assertDatabaseHas('category_post', [
        'category_id' => $post->categories->first()->id,
        'post_id' => $post->id,
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
        'category_id' => $post->categories,
        'title' => $post->title,
        'description' => $post->description,
        'is_visible' => $post->is_visible,
        'images' => [UploadedFile::fake()->image('testImage')]
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
    [['images' => null], 'images'],
    [['images' => 5], 'images'],
    [['images' => 1.5], 'images'],
    [['images' => 'string'], 'images'],
    [['images' => [
        ['image' => UploadedFile::fake()->create('testImage', 2049)]
    ]], 'images.0.image'], // max image size
    [['images' => [
        ['image' => UploadedFile::fake()->image('testImage1')],
        ['image' => UploadedFile::fake()->image('testImage1')],
        ['image' => UploadedFile::fake()->image('testImage1')],
        ['image' => UploadedFile::fake()->image('testImage1'), 'is_main' => 1],
    ]], ['images', 'images', 'images', 'images']], // max images
    [['images' => [
        ['image' => UploadedFile::fake()->create('testImage')]
    ]], 'images'], // no main image
]);
