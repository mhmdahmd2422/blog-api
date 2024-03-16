<?php

use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use function Pest\Laravel\{post};

it('can store comment for post', function () {
    $post = Post::factory()->create();
    $comment = Comment::factory()->create();

    expect($post->comments)
        ->toHaveCount(0);

    $response = post(route('admin.posts.comments.store', $post), [
        'user_id' => $comment->user->id,
        'body' => $comment->body
    ]);

    $comment->id = $post->fresh()->comments->first()->id;

    $response->assertStatus(200)
        ->assertExactJson([
            'comment' => getResponseData(CommentResource::make($comment)),
            'message' => __('postComments.store')
        ]);

    expect($post->fresh()->comments)
        ->toHaveCount(1);

    $this->assertDatabaseHas(Comment::class, [
        'id' => $comment->id,
        'post_id' => $post->id,
        'body' => $comment->body,
    ]);
});

it('requires a valid data when creating', function (array $badData, array|string $errors) {
    $comment = Comment::factory()->create();

    post(route('admin.posts.comments.store', $comment->post), [...$badData])
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

