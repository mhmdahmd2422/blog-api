<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateCommentRequest;
use App\Http\Resources\Admin\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Response;

class CommentController extends Controller
{
    public function index(Post $post): Response
    {
        $paginationLength = pagination_length('comment');

        return response([
            'comments' => CommentResource::collection($post->comments->load('user'))
                ->paginate($paginationLength),
        ]);
    }

    public function show(Post $post, Comment $comment): Response
    {
        return response([
            'comment' => CommentResource::make($comment->load('user')),
        ]);
    }

    public function update(UpdateCommentRequest $request, Post $post, Comment $comment): Response
    {
        $comment = $request->updateComment();

        return response([
            'comment' => CommentResource::make($comment->load('user')),
        ]);
    }

    public function destroy(Post $post, Comment $comment): Response
    {
        $comment->delete();

        return response([
            'message' => __('comments.destroy')
        ]);
    }
}
