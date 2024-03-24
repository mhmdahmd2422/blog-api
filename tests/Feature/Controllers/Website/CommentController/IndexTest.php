<?php

use App\Http\Resources\Website\CommentResource;
use App\Models\Post;
use function Pest\Laravel\{get};

it('can get all comments for a post', function (){
   $post = Post::factory()->hasComments(10)->create();

   get(route('website.posts.comments.index', $post))
       ->assertStatus(200)
       ->assertExactJson([
           'comments' => responsePaginatedData(
               CommentResource::collection($post->comments->load('user')
                   ->paginate(pagination_length('comment')))
           )
       ]);
});

it('can not get comments for a invisible post', function (){
    $post = Post::factory()->invisible()->hasComments(10)->create();

    get(route('website.posts.comments.index', $post))
        ->assertStatus(404);
});
