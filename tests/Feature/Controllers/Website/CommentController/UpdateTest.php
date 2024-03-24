<?php

use App\Http\Resources\Website\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use function Pest\Laravel\{put};

it('returns not found if comment do not exist for this post', function () {
    $firstPost = Post::factory()->hasComments(5)->create();
    $SecondPost = Post::factory()->hasComments(5)->create();

    put(route('website.posts.comments.update', [$firstPost, $SecondPost->comments->first()]))
        ->assertStatus(404);
});

it('returns not found if comment is for invisible post', function () {
    $post = Post::factory()->invisible()->hasComments(5)->create();

    put(route('website.posts.comments.update', [$post, $post->comments->first()]))
        ->assertStatus(404);
});

it('can update a comment for a post', function () {
   $post = Post::factory()->create();
   $comment = Comment::factory()->for($post)->create();
   $comment->body = 'updated comment body';

   put(route('website.posts.comments.update', [$post, $comment]), [
       'body' => $comment->body
   ])
       ->assertStatus(200)
       ->assertExactJson([
           'comment' => responseData(CommentResource::make($comment)),
           'message' => __('comments.update')
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

    put(route('website.posts.comments.update', [$comment->post, $comment]), [...$badData])
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
