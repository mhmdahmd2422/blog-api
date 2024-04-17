<?php

use App\Http\Resources\Admin\PostResource;
use App\Models\Image;
use App\Models\Post;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use function Pest\Laravel\post;

beforeEach(function () {
    loginAsUser();
});

it('can add images to existing post', function () {
   $post = Post::factory()->invisible()->create();
   $image = UploadedFile::fake()->image('testImage.png');

   expect($post->images)
       ->toHaveCount(0);

   post(route('admin.posts.images.store', $post), [
       'images' => [
           ['image' => $image],
           ['image' => $image, 'is_main' => true]
       ]
   ])
       ->assertStatus(200)
       ->assertJson([
           'post' => responseData(PostResource::make($post->load('images'))),
           'message' => __('posts.image.store')
       ]);

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
    $post = Post::factory()->invisible()->create();

    post(route('admin.posts.images.store', $post), [[
        'images' => [
            'image' => UploadedFile::fake()->image('testImage'),
            'is_main' => 1
        ]
    ], ...$badData])
        ->assertInvalid($errors);
})->with([
    [['images' => [null]], 'images'],
    [['images' => [5]], 'images'],
    [['images' => [1.5]], 'images'],
    [['images' => ['string']], 'images'],
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
