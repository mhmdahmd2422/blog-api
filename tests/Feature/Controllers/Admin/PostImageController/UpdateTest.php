<?php

use App\Http\Resources\Admin\PostResource;
use App\Models\Image;
use App\Models\Post;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use function Pest\Laravel\{put};

it('returns not found if image do not exist for this post', function () {
    $firstPost = Post::factory()->invisible()->hasImages(1)->create();
    $secondPost = Post::factory()->invisible()->create();
    $secondPostImage = Image::factory()->for($secondPost, 'imageable')->create();
    $image = UploadedFile::fake()->image('testImage1.png');

    put(route('admin.posts.images.update', [$firstPost, $secondPostImage->id]), [
        'image' => $image,
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
    $postImage = Image::factory()->for($post, 'imageable')->create([
        'path' => $oldImagePath
    ]);

    put(route('admin.posts.images.update', [$post, $postImage->id]), [
        'image' => $newImage,
    ])
        ->assertStatus(200)
        ->assertExactJson([
            'post' => responseData(PostResource::make($post->load('images'))),
            'message' => __('posts.image.update')
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

    Storage::assertExists($newImagePath);
    Storage::assertMissing($oldImagePath);
});
