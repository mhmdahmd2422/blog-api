<?php

use App\Models\Comment;
use App\Models\Post;
use function Pest\Laravel\{delete};

it('returns not found if comment do not exist for this post', function () {
    $firstPost = Post::factory()->hasComments(5)->create();
    $SecondPost = Post::factory()->hasComments(5)->create();

    delete(route('website.posts.comments.destroy', [$firstPost, $SecondPost->comments->first()]))
        ->assertStatus(404);
});

it('returns not found if comment is for invisible post', function () {
    $post = Post::factory()->invisible()->hasComments(5)->create();

    delete(route('website.posts.comments.destroy', [$post, $post->comments->first()]))
        ->assertStatus(404);
});

it('can not delete a banned comment', function () {
    $post = Post::factory()->create();
    $comment = Comment::factory()->banned()->for($post)->create();

    delete(route('website.posts.comments.destroy', [$post, $comment]))
        ->assertStatus(404);
});

it('can delete a comment for a post', function () {
    $post = Post::factory()->create();
    $comment = Comment::factory()->for($post)->create();

    delete(route('website.posts.comments.destroy', [$post, $comment]))
        ->assertStatus(200)
        ->assertExactJson([
            'message' => __('comments.destroy')
        ]);

    expect($post->comments)
        ->toHaveCount(0);

    $this->assertDatabaseMissing(Comment::class, [
        'id' => $comment->id,
        'post_id' => $post->id,
        'body' => $comment->body,
    ]);
});
