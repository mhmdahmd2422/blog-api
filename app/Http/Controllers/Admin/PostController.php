<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Response;

class PostController extends Controller
{
    public function index(): Response
    {
        return response([
            'posts' => PostResource::collection(Post::all()),
        ]);
    }

    public function store(StorePostRequest $request): Response
    {
        $post = $request->storePost();

        return response([
            'post' => PostResource::make($post),
            'message' => __('posts.store')
        ]);
    }

    public function show(Post $post): Response
    {
        return response([
            'post' => PostResource::make($post),
        ]);
    }

    public function update(UpdatePostRequest $request, Post $post): Response
    {
        $request->updatePost();

        return response([
            'post' => PostResource::make($post->fresh()),
            'message' => __('posts.update')
        ]);
    }

    public function destroy(Post $post): Response
    {
        $post->remove();

        return response([
            'message' => __('posts.destroy')
        ]);
    }
}
