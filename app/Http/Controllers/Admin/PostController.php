<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;

class PostController extends Controller
{
    public function index()
    {
        return response([
            'posts' => PostResource::collection(Post::all()),
        ]);
    }

    public function store(StorePostRequest $request)
    {
        $post = $request->storePost();
        if($request->has('image')) {
            $post->image = uploadImage($request->file('image'), 'uploads/posts/');
            $post->save();
        }

        return response([
            'post' => PostResource::make($post),
            'message' => 'Post Created.'
        ]);
    }

    public function show(Post $post)
    {
        return response([
            'post' => PostResource::make($post),
        ]);
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        $request->updatePost();
        if($request->has('image')) {
            $post->image = updateImage(
                $request->file('image'),
                $post->image,
                'uploads/posts/'
            );
            $post->save();
        }

        return response([
            'post' => PostResource::make($post),
            'message' => 'Post Updated.'
        ]);
    }

    public function destroy(Post $post)
    {
        $post->delete();

        return response([
            'message' => 'Post Deleted.'
        ]);
    }
}
