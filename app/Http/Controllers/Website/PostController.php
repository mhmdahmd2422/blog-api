<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Http\Middleware\ResourceVisibility;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Response;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware(ResourceVisibility::class);
    }

    public function index(): Response
    {
        return response([
            'posts' => PostResource::collection(Post::visible()->get()),
        ]);
    }

    public function show(Post $post): Response
    {
        return response([
            'post' => PostResource::make($post),
        ]);
    }
}
