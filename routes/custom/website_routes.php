<?php

use App\Http\Controllers\Website\PostCommentController;
use App\Http\Controllers\Website\PostController;
use Illuminate\Support\Facades\Route;

Route::group([
    'as' => 'website.',
], function () {
    Route::apiResource('/posts', PostController::class);
    Route::apiResource('posts.comments', PostCommentController::class);
});
