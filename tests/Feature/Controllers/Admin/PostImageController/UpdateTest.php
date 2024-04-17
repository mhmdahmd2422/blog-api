<?php

use App\Http\Resources\Admin\PostResource;
use App\Models\Image;
use App\Models\Post;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use function Pest\Laravel\{put};

beforeEach(function () {
    loginAsUser();
});

it('returns not found if image do not exist for this post', function () {
    $firstPost = Post::factory()->invisible()->has(
        Image::factory()->is_main()
    )->create();
    $secondPost = Post::factory()->invisible()->create();
    $secondPostImage = Image::factory()->for($secondPost, 'imageable')->create();
    $image = UploadedFile::fake()->image('testImage.png');

    put(route('admin.posts.images.update', [$firstPost, $secondPostImage->id]), [
        'image' => $image,
        'is_main' => 1
    ])
        ->assertStatus(404);
});

it('can update a post image', function () {
    $post = Post::factory()->invisible()->create();
    $oldImage = UploadedFile::fake()->image('testImage1.png');
    $oldImagePath = 'uploads/posts/'.$oldImage->hashName();
    $oldImage->storeAs('uploads/posts/', $oldImage->hashName());
    $newImage = UploadedFile::fake()->image('testImage2.png');
    $newImagePath = 'uploads/posts/'.$newImage->hashName();
    Image::factory()->count(2)->for($post, 'imageable')->sequence(
        ['path' => $oldImagePath, 'is_main' => false],
        ['is_main' => true]
    )->create();

    put(route('admin.posts.images.update', [$post, $post->images()->first()]), [
        'image' => $newImage,
        'is_main' => 1
    ])
        ->assertStatus(200)
        ->assertExactJson([
            'post' => responseData(PostResource::make($post->load('images'))),
            'message' => __('posts.image.update')
        ]);

    expect($post->main_image->path)
        ->toEqual($newImagePath);

    $this->assertDatabaseHas(Image::class, [
        'imageable_type' => Post::class,
        'imageable_id' => $post->id,
        'path' => $newImagePath,
        'is_main' => 1
    ]);

    $this->assertDatabaseMissing(Image::class, [
        'imageable_type' => Post::class,
        'imageable_id' => $post->id,
        'path' => $oldImagePath
    ]);

    Storage::assertExists($newImagePath);
    Storage::assertMissing($oldImagePath);
});


it('requires a valid data when updating', function (array $badData, array|string $errors) {
    $post = Post::factory()->invisible()->create();
    $image = Image::factory()->is_main()->for($post, 'imageable')->create();

    put(route('admin.posts.images.update', [$post, $image->id]), [[
        'image' => UploadedFile::fake()->image('testImage'),
        'is_main' => $image->is_main,
    ], ...$badData])
        ->assertInvalid($errors);
})->with([
    [['image' => null], 'image'],
    [['image' => 5], 'image'],
    [['image' => 1.5], 'image'],
    [['image' => 'string'], 'image'],
    [['image' => UploadedFile::fake()->create('testImage', 2049)], 'image'], // max
    [['is_main' => null], 'is_main'],
    [['is_main' => 5], 'is_main'],
    [['is_main' => 1.5], 'is_main'],
    [['is_main' => 'string'], 'is_main'],
]);

