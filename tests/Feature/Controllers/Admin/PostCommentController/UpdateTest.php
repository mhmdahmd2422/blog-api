<?php

use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use function Pest\Laravel\{put};

it('returns not found if comment do not exist for this post', function () {
    $firstPost = Post::factory()->hasComments(5)->create();
    $SecondPost = Post::factory()->hasComments(5)->create();

    put(route('admin.posts.comments.update', [$firstPost, $SecondPost->comments->first()]))
        ->assertStatus(404)
        ->assertExactJson([
            'message' => __('postComments.error')
        ]);
});

it('can update a comment for a post', function () {
   $post = Post::factory()->create();
   $comment = Comment::factory()->for($post)->create();
   $comment->body = 'updated comment body';

   put(route('admin.posts.comments.update', [$post, $comment]), [
       'body' => $comment->body
   ])
       ->assertStatus(200)
       ->assertExactJson([
           'comment' => getResponseData(CommentResource::make($comment)),
           'message' => __('postComments.update')
       ]);

   expect($post->comments)
       ->toHaveCount(1);

   $this->assertDatabaseHas(Comment::class, [
       'id' => $comment->id,
       'post_id' => $post->id,
       'body' => $comment->body
   ]);
});

it('requires a valid data when updating', function (array $badData, array|string $errors) {
    $comment = Comment::factory()->create();

    put(route('admin.posts.comments.update', [$comment->post, $comment]), [...$badData])
        ->assertInvalid($errors);
})->with([
    [['body' => null], 'body'],
    [['body' => 1], 'body'],
    [['body' => 1.5], 'body'],
    [['body' => true], 'body'],
    [['body' => true], 'body'],
    [['body' => str_repeat('a', 9)], 'body'],
    [['body' => str_repeat('a', 1501)], 'body'],
]);
