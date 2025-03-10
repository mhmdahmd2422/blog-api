<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Http\Requests\Website\StoreCommentRequest;
use App\Http\Requests\Website\UpdateCommentRequest;
use App\Http\Resources\Website\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Response;

class CommentController extends Controller
{
    public function index(Post $post): Response
    {
        $paginationLength = pagination_length('comment');

        return response([
            'comments' => CommentResource::collection($post->comments()->isBanned(false)->get())
                ->paginate($paginationLength),
        ]);
    }

    public function store(StoreCommentRequest $request, Post $post): Response
    {
        $comment = $request->storeComment();

        return response([
            'comment' => CommentResource::make($comment->load('user')),
            'message' => __('comments.store')
        ]);
    }

    public function show(Post $post, Comment $comment): Response
    {
        return response([
            'comment' => CommentResource::make($comment),
        ]);
    }

    public function update(UpdateCommentRequest $request, Post $post, Comment $comment): Response
    {
        $comment = $request->updateComment();

        return response([
            'comment' => CommentResource::make($comment),
            'message' => __('comments.update')
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
