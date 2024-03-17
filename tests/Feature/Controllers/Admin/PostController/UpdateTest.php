<?php

use App\Http\Resources\PostResource;
use App\Models\Image;
use App\Models\Post;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use function Pest\Laravel\{put};

it('can update a post', function () {
    $oldPost = Post::factory()->invisible()->create();
    $updatedPost = Post::factory()->for($oldPost->user)->visible()->create([
        'title' => 'updated title',
        'description' => str_repeat('updated description',5)
    ]);
    $response = put(route('admin.posts.update', $oldPost), [
        'user_id' => $updatedPost->user_id,
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
});

it('can add a photo to existing post', function () {
    $post = Post::factory()->invisible()->create();
    $image = UploadedFile::fake()->image('testImage.png');

    put(route('admin.posts.update', $post), [
        'image' => $image,
    ])
        ->assertStatus(200)
        ->assertExactJson([
            'post' => responseData(PostResource::make($post)),
            'message' => __('posts.update')
        ]);

    $this->assertDatabaseHas(Image::class, [
        'imageable_type' => Post::class,
        'imageable_id' => $post->id,
        'path' => 'uploads/posts/'.$image->hashName()
    ]);

    Storage::assertExists('uploads/posts/'.$image->hashName());
});

it('can update image of an existing post', function () {
    $post = Post::factory()->invisible()->create();
    $oldImage = UploadedFile::fake()->image('testImage1.png');
    $oldImagePath = 'uploads/posts/'.$oldImage->hashName();
    $newImage = UploadedFile::fake()->image('testImage2.png');
    $newImagePath = 'uploads/posts/'.$newImage->hashName();
    $oldImage->storeAs('uploads/posts/', $oldImage->hashName());
    Image::factory()->for($post, 'imageable')->create([
        'path' => $oldImagePath
    ]);

    expect($post->image->path)
        ->toEqual($oldImagePath);

    put(route('admin.posts.update', $post), [
        'image' => $newImage,
    ])
        ->assertStatus(200)
        ->assertExactJson([
            'post' => responseData(PostResource::make($post->fresh())),
            'message' => __('posts.update')
        ]);

    $this->assertDatabaseHas(Image::class, [
        'imageable_type' => Post::class,
        'imageable_id' => $post->id,
        'path' => $newImagePath
    ]);

    $this->assertDatabaseMissing(Image::class, [
        'imageable_type' => Post::class,
        'imageable_id' => $post->id,
        'path' => $oldImagePath
    ]);

    expect($post->fresh()->image->path)
        ->toEqual($newImagePath)
        ->and(Image::all())
        ->toHaveCount(1);

    Storage::assertExists($newImagePath);
    Storage::assertMissing($oldImagePath);
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
