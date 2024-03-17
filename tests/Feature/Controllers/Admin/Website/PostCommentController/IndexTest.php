<?php

use App\Http\Resources\CommentResource;
use App\Models\Post;
use function Pest\Laravel\{get};

it('can get all comments for a post', function (){
   $post = Post::factory()->hasComments(10)->create();

   get(route('website.posts.comments.index', $post))
       ->assertStatus(200)
       ->assertExactJson([
           'comments' => responseData(CommentResource::collection($post->comments))
       ]);
});
