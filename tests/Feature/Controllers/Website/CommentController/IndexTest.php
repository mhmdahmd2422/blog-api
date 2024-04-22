<?php

use App\Http\Resources\Website\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use function Pest\Laravel\{get};

beforeEach(function () {
    loginAsUser();
});

it('can get all comments for a post', function (){
   $post = Post::factory()->hasComments(2)
       ->has(Comment::factory()->count(2)->banned())->create();

   get(route('website.posts.comments.index', $post))
       ->assertStatus(200)
       ->assertExactJson([
           'comments' => responsePaginatedData(
               CommentResource::collection($post->comments()->isBanned(false)->with('user')
                   ->paginate(pagination_length('comment')))
           )
       ]);
});

it('can not get comments for a invisible post', function (){
    $post = Post::factory()->invisible()->hasComments(10)->create();

    get(route('website.posts.comments.index', $post))
        ->assertStatus(404);
});
