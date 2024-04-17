<?php

use App\Models\Image;
use App\Models\Post;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use function Pest\Laravel\{delete};

beforeEach(function () {
    loginAsUser();
});

it('returns not found if image do not exist for this post', function () {
    $firstPost = Post::factory()->invisible()->hasImages(1)->create();
    $secondPost = Post::factory()->invisible()->create();
    $secondPostImage = Image::factory()->for($secondPost, 'imageable')->create();

    delete(route('admin.posts.images.destroy', [$firstPost, $secondPostImage->id]))
        ->assertStatus(404);
});

it('can delete a post image', function () {
   $post = Post::factory()->invisible()->create();
   $image = UploadedFile::fake()->image('testImage');
   $image->storeAs('uploads/posts/', $image->hashName());
   $imagePath = 'uploads/posts/'.$image->hashName();
   $postImage = Image::factory()->is_main()->for($post, 'imageable')->create([
       'path' => $imagePath
   ]);

   delete(route('admin.posts.images.destroy', [$post, $postImage->id]))
       ->assertStatus(200)
       ->assertExactJson([
           'message' => __('posts.image.destroy')
       ]);

   $this->assertDatabaseMissing(Image::class, [
       'imageable_type' => Post::class,
       'imageable_id' => $post->id,
       'path' => $imagePath
   ]);

   Storage::assertMissing($imagePath);
});

it('can not delete a post main image if there are other images attached', function () {
    $post = Post::factory()->invisible()->create();
    Image::factory()->count(2)->for($post, 'imageable')->sequence(
        ['is_main' => true],
        ['is_main' => false]
    )->create();

    delete(route('admin.posts.images.destroy', [$post, $post->images()->first()]))
        ->assertStatus(404);
});
