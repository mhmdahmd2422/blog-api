<?php

use App\Http\Resources\Admin\CommentResource;
use App\Models\Comment;
use function Pest\Laravel\{put};

it('it can ban a comment', function () {
   $comment = Comment::factory()->hasPost()->create();

   $response = put(route('admin.posts.comments.update', [$comment->post, $comment]),[
       'is_banned' => 1
   ]);

   expect($comment->is_banned)
       ->toBeFalse();

   $comment->is_banned = 1;

   $response
       ->assertStatus(200)
       ->assertExactJson([
           'comment' => responseData(CommentResource::make($comment->load('user'))),
           'message' => __('comments.update')
       ]);

   $this->assertDatabaseHas(Comment::class, [
       'id' => $comment->id,
       'user_id' => $comment->user->id,
       'post_id' => $comment->post->id,
       'body' => $comment->body,
       'is_banned' => 1
   ]);
});
