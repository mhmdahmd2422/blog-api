<?php

use App\Http\Resources\Website\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use function Pest\Laravel\{post};

beforeEach(function () {
    loginAsUser();
});

it('can store comment for post', function () {
    $post = Post::factory()->create();
    $comment = Comment::factory()->for(loginAsUser())->create();

    expect($post->comments)
        ->toHaveCount(0);

    $response = post(route('website.posts.comments.store', $post), [
        'body' => $comment->body
    ]);

    $comment->id = $post->fresh()->comments->first()->id;

    $response->assertStatus(200)
        ->assertExactJson([
            'comment' => responseData(CommentResource::make($comment->load('user'))),
            'message' => __('comments.store')
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

    post(route('website.posts.comments.store', $comment->post), [[
        'body' => $comment->body
    ], ...$badData])
        ->assertInvalid($errors);
})->with([
    [['body' => null], 'body'],
    [['body' => 1], 'body'],
    [['body' => 1.5], 'body'],
    [['body' => true], 'body'],
    [['body' => true], 'body'],
    [['body' => str_repeat('a', 1501)], 'body'],
]);

