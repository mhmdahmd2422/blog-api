<?php

namespace App\Http\Controllers\Admin;

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
        return response([
            'comments' => CommentResource::collection($post->comments),
        ]);
    }

    public function store(StoreCommentRequest $request, Post $post)
    {
        $comment = $request->storeComment();

        return response([
            'comment' => CommentResource::make($comment),
            'message' => 'Comment Created.'
        ]);
    }

    public function show(Comment $comment)
    {
        return response([
            'comment' => CommentResource::make($comment),
        ]);
    }

    public function update(UpdateCommentRequest $request, Comment $comment)
    {
        $request->updateComment();

        return response([
            'comment' => CommentResource::make($comment),
            'message' => 'Comment Updated.'
        ]);
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();

        return response([
            'message' => 'Comment Deleted.'
        ]);
    }
}
