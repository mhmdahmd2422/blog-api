<?php

use App\Models\Comment;
use App\Models\Post;
use function Pest\Laravel\{delete};

it('returns not found if comment do not exist for this post', function () {
    $firstPost = Post::factory()->hasComments(5)->create();
    $SecondPost = Post::factory()->hasComments(5)->create();

    delete(route('admin.posts.comments.destroy', [$firstPost, $SecondPost->comments->first()]))
        ->assertStatus(404)
        ->assertExactJson([
            'message' => __('postComments.error')
        ]);
});

it('can delete a comment for a post', function () {
    $post = Post::factory()->create();
    $comment = Comment::factory()->for($post)->create();

    delete(route('admin.posts.comments.destroy', [$post, $comment]))
        ->assertStatus(200)
        ->assertExactJson([
            'message' => __('postComments.destroy')
        ]);

    expect($post->comments)
        ->toHaveCount(0);

    $this->assertDatabaseMissing(Comment::class, [
        'id' => $comment->id,
        'post_id' => $post->id,
        'body' => $comment->body,
    ]);
});
