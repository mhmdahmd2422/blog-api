<?php

use App\Models\Comment;
use App\Models\Image;
use App\Models\Post;
use Illuminate\Http\UploadedFile;
use function Pest\Laravel\{delete};

it('can delete a post with all child models', function () {
   $post = Post::factory()->invisible()->create();
   $comments = Comment::factory()->count(3)->for($post)->create();
   $testImage = UploadedFile::fake()->image('testImage.png');
   $testImagePath = 'uploads/posts/'.$testImage->hashName();
   $image = Image::factory()->for($post, 'imageable')->create([
       'path' => $testImagePath
   ]);

   expect($post->image->path)
       ->toEqual($image->path)
       ->and($post->comments)
       ->toHaveCount(3);

   delete(route('admin.posts.destroy', $post))
       ->assertStatus(200)
       ->assertExactJson([
           'message' => __('posts.destroy')
       ]);

   $this->assertDatabaseMissing(Post::class, [
       'id' => $post->id,
       'user_id' => $post->user_id,
       'title' => $post->title,
       'description' => $post->description,
       'is_visible' => $post->is_visible
   ]);

    $this->assertDatabaseMissing(Image::class, [
        'imageable_type' => Post::class,
        'imageable_id' => $post->id,
        'path' => $testImagePath
    ]);

    foreach ($comments as $comment) {
        $this->assertDatabaseMissing(Comment::class, [
            'id' => $comment->id,
            'post_id' => $post->id,
            'body' => $comment->body,
        ]);
    }
});
