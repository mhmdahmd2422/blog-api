<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePostRequest;
use App\Http\Requests\Admin\UpdatePostRequest;
use App\Http\Resources\Admin\PostResource;
use App\Http\Resources\Admin\PostSimpleResource;
use App\Models\Post;
use Illuminate\Http\Response;

class PostController extends Controller
{
    public function index(): Response
    {
        $paginationLength = pagination_length('post');

        return response([
            'posts' => PostSimpleResource::collection(Post::paginate($paginationLength))

        ]);
    }

    public function store(StorePostRequest $request): Response
    {
        $post = $request->storePost();

        return response([
            'post' => PostResource::make($post->load('images', 'categories')),
            'message' => __('posts.store')
        ]);
    }

    public function show(Post $post): Response
    {
        return response([
            'post' => PostResource::make($post->load('images', 'user', 'categories'))
        ]);
    }

    public function update(UpdatePostRequest $request, Post $post): Response
    {
        $post = $request->updatePost();

        return response([
            'post' => PostResource::make($post->load('images', 'categories')),
            'message' => __('posts.update')
        ]);
    }

    public function destroy(Post $post): Response
    {
        if ($post->remove()) {
            return response([
                'message' => __('posts.destroy')
            ]);
        }

        return response([
            'message' => __('posts.cant_destroy'),
        ], 409);
    }
}
