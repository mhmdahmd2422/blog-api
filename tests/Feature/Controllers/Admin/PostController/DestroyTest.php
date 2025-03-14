<?php

use App\Models\Comment;
use App\Models\Image;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;
use function Pest\Laravel\{delete};

it('can delete a post with all child models', function () {
   $post = Post::factory()->invisible()->hasImages(3)->hasComments(3)->create();

   expect($post->images)
       ->toHaveCount(3)
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

    foreach ($post->images as $image) {
        $this->assertDatabaseMissing(Image::class, [
            'imageable_type' => Post::class,
            'imageable_id' => $post->id,
            'path' => $image->path
        ]);

        Storage::assertMissing($image->path);
    }

    foreach ($post->comments as $comment) {
        $this->assertDatabaseMissing(Comment::class, [
            'id' => $comment->id,
            'post_id' => $post->id,
            'body' => $comment->body,
        ]);
    }
});
