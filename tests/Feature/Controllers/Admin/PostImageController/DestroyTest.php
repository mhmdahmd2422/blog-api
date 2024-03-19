<?php

use App\Http\Resources\PostResource;
use App\Models\Image;
use App\Models\Post;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use function Pest\Laravel\{delete};

it('can delete a post image', function () {
   $post = Post::factory()->invisible()->create();
   $image = UploadedFile::fake()->image('testImage');
   $image->storeAs('uploads/posts/', $image->hashName());
   $imagePath = 'uploads/posts/'.$image->hashName();
   $postImage = Image::factory()->for($post, 'imageable')->create([
       'path' => $imagePath
   ]);

   delete(route('admin.posts.image.destroy', [$post, $postImage->id]))
       ->assertStatus(200)
       ->assertExactJson([
           'post' => responseData(PostResource::make($post->load('images'))),
           'message' => __('posts.image.destroy')
       ]);

   $this->assertDatabaseMissing(Image::class, [
       'imageable_type' => Post::class,
       'imageable_id' => $post->id,
       'path' => $imagePath
   ]);

   Storage::assertMissing($imagePath);
});

it('returns not found if image do not exist for this post', function () {
    $firstPost = Post::factory()->invisible()->create();
    $secondPost = Post::factory()->invisible()->create();
    $firstPostImage = Image::factory()->for($firstPost, 'imageable')->create();
    $secondPostImage = Image::factory()->for($secondPost, 'imageable')->create();

    delete(route('admin.posts.image.destroy', [$firstPost, $secondPostImage->id]))
        ->assertStatus(404);
});
