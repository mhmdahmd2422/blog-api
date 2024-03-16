<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
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

    public function store(StoreCommentRequest $request, Post $post): Response
    {
        $comment = $request->storeComment();

        return response([
            'comment' => CommentResource::make($comment),
            'message' => __('postComments.store')
        ]);
    }

    public function show(Post $post, Comment $comment): Response
    {
        if ($comment->post->isNot($post)) {
            return response([
                'message' => __('postComments.error')
            ], 404);
        }

        return response([
            'comment' => CommentResource::make($comment),
        ]);
    }

    public function update(UpdateCommentRequest $request, Post $post, Comment $comment): Response
    {
        if ($comment->post()->isNot($post)) {
            return response([
                'message' => __('postComments.error')
            ], 404);
        }

        $request->updateComment();

        return response([
            'comment' => CommentResource::make($comment),
            'message' => __('postComments.update')
        ]);
    }

    public function destroy(Post $post, Comment $comment): Response
    {
        if ($comment->post()->isNot($post)) {
            return response([
                'message' => __('postComments.error')
            ], 404);
        }

        $comment->delete();

        return response([
            'message' => __('postComments.destroy')
        ]);
    }
}
