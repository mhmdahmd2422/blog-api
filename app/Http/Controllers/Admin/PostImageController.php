<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePostImageRequest;
use App\Http\Requests\Admin\UpdatePostImageRequest;
use App\Http\Resources\Admin\PostResource;
use App\Models\Post;
use Illuminate\Http\Response;

class PostImageController extends Controller
{
    public function store(StorePostImageRequest $request, Post $post): Response
    {
        $post = $request->storeImages();

        return response([
            'post' => PostResource::make($post->load('images')),
            'message' => __('posts.image.store')
        ]);
    }

    public function update(UpdatePostImageRequest $request, Post $post, string $imageId): Response
    {
        $post = $request->updateImage();

        if (! $post) {
            return response('', 404);
        }

        return response([
            'post' => PostResource::make($post->load('images')),
            'message' => __('posts.image.update')
        ]);
    }

    public function destroy(Post $post, string $imageId): Response
    {
        if (! $post->destroyImage($imageId)) {
            return response('', 404);
        }

        return response([
            'message' => __('posts.image.destroy')
        ]);
    }
}
