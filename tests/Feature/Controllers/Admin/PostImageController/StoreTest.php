<?php

use App\Http\Resources\Admin\PostResource;
use App\Models\Image;
use App\Models\Post;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use function Pest\Laravel\post;

it('can add images to existing post', function () {
   $post = Post::factory()->invisible()->create();
   $image = UploadedFile::fake()->image('testImage1.png');


   post(route('admin.posts.images.store', $post), [
       'images' => [$image, $image]
   ])
       ->assertStatus(200)
       ->assertJson([
           'post' => responseData(PostResource::make($post->load('images'))),
           'message' => __('posts.image.store')
       ]);

   expect($post->images)
       ->toHaveCount(2);

   foreach ($post->images as $image) {
       $this->assertDatabaseHas(Image::class, [
           'imageable_type' => Post::class,
           'imageable_id' => $post->id,
           'path' => $image->path,
       ]);

       Storage::assertExists($image->path);
   }
});
