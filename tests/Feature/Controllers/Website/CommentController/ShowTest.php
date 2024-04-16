<?php

use App\Http\Resources\Website\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use function Pest\Laravel\{get};

beforeEach(function () {
    loginAsUser();
});

it('returns not found if comment do not exist for this post', function () {
    $firstPost = Post::factory()->hasComments(5)->create();
    $SecondPost = Post::factory()->hasComments(5)->create();

    get(route('website.posts.comments.show', [$firstPost, $SecondPost->comments->first()]))
        ->assertStatus(404);
});

it('returns not found if comment is for invisible post', function () {
    $post = Post::factory()->invisible()->hasComments(5)->create();

    get(route('website.posts.comments.show', [$post, $post->comments->first()]))
        ->assertStatus(404);
});

it('can show a comment for a post', function () {
    $post = Post::factory()->hasComments(5)->create();

    get(route('website.posts.comments.show', [$post, $post->comments->first()]))
        ->assertStatus(200)
        ->assertExactJson([
            'comment' => responseData(CommentResource::make($post->comments->first()->load('user')))
        ]);
});

it('can not show a banned comment for a post', function () {
    $post = Post::factory()->create();
    $comment = Comment::factory()->banned()->for($post)->create();

    get(route('website.posts.comments.show', [$post, $comment]))
        ->assertStatus(404);
});
