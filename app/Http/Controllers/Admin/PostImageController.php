<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePostImageRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;

class PostImageController extends Controller
{
    public function update(UpdatePostImageRequest $request, Post $post, string $imageId)
    {
        $post = $request->updateImage();

        if (! $post) {
            return response('', 404);
        }

        return response([
            'post' => PostResource::make($post),
            'message' => __('posts.image.update')
        ]);
    }

    public function destroy(Post $post, string $imageId)
    {
        $post = $post->destroyImage($imageId);

        if (! $post) {
            return response('', 404);
        }

        return response([
            'post' => PostResource::make($post),
            'message' => __('posts.image.destroy')
        ]);
    }
}
