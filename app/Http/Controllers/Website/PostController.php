<?php

namespace App\Http\Controllers\Website;

use App\Filters\Website\PostFilter;
use App\Http\Controllers\Controller;
use App\Http\Resources\Website\PostResource;
use App\Http\Resources\Website\PostSimpleResource;
use App\Models\Post;
use Illuminate\Http\Response;

class PostController extends Controller
{
    public function index(PostFilter $filters): Response
    {
        $paginationLength = pagination_length('post');

        return response([
            'posts' => PostSimpleResource::collection(Post::visible()->filter($filters)
                ->paginate($paginationLength))
        ]);
    }

    public function show(Post $post): Response
    {
        return response([
            'post' => PostResource::make($post->load('images', 'categories')),
        ]);
    }
}
