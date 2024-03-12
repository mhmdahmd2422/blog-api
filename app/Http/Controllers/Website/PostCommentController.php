<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;

class PostCommentController extends Controller
{
    public function index(Post $post)
    {
        if ($post->is_visible !== true) {
            return response('', 403);
        }

        return response([
            'comments' => CommentResource::collection($post->comments),
        ]);
    }

    public function store(StoreCommentRequest $request, Post $post)
    {
        if ($post->is_visible !== true) {
            return response('', 403);
        }

        $comment = $request->storeComment();

        return response([
            'comment' => CommentResource::make($comment),
            'message' => 'Comment Created.'
        ]);
    }

    public function show(Comment $comment)
    {
        if ($comment->post->is_visible !== true) {
            return response('', 403);
        }

        return response([
            'comment' => CommentResource::make($comment),
        ]);
    }

    public function update(UpdateCommentRequest $request, Comment $comment)
    {
        if ($comment->post->is_visible !== true) {
            return response('', 403);
        }

        $request->updateComment();

        return response([
            'comment' => CommentResource::make($comment),
            'message' => 'Comment Updated.'
        ]);
    }

    public function destroy(Comment $comment)
    {
        if ($comment->post->is_visible !== true) {
            return response('', 403);
        }

        $comment->delete();

        return response([
            'message' => 'Comment Deleted.'
        ]);
    }
}
