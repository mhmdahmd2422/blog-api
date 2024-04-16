<?php

use App\Http\Resources\Admin\CommentResource;
use App\Models\Post;
use function Pest\Laravel\{get};

beforeEach(function () {
    loginAsUser();
});

it('can get all comments for a post', function (){
   $post = Post::factory()->hasComments(10)->create();

   get(route('admin.posts.comments.index', $post))
       ->assertStatus(200)
       ->assertExactJson([
           'comments' => responsePaginatedData(
               CommentResource::collection($post->comments->load('user')
                   ->paginate(pagination_length('comment')))
           )
       ]
   );
});
