<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Response;

class PostCommentController extends Controller
{
    public function index(Post $post): Response
    {
        return response([
            'comments' => CommentResource::collection($post->comments),
        ]);
    }

    public function show(Post $post, Comment $comment): Response
    {
        return response([
            'comment' => CommentResource::make($comment),
        ]);
    }

    public function destroy(Post $post, Comment $comment): Response
    {
        $comment->delete();

        return response([
            'message' => __('postComments.destroy')
        ]);
    }
}
