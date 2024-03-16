<?php

use App\Http\Resources\CommentResource;
use App\Models\Post;
use function Pest\Laravel\{get};

it('returns not found if comment do not exist for this post', function () {
    $firstPost = Post::factory()->hasComments(5)->create();
    $SecondPost = Post::factory()->hasComments(5)->create();

    get(route('admin.posts.comments.show', [$firstPost, $SecondPost->comments->first()]))
        ->assertStatus(404)
        ->assertExactJson([
            'message' => __('postComments.error')
        ]);
});

it('can show a comment for a post', function () {
    $post = Post::factory()->hasComments(5)->create();

    get(route('admin.posts.comments.show', [$post, $post->comments->first()]))
        ->assertStatus(200)
        ->assertExactJson([
            'comment' => getResponseData(CommentResource::make($post->comments->first()->load('post')))
        ]);
});
