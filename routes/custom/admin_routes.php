<?php

use App\Http\Controllers\Admin\PostCommentController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::group([
    'as' => 'admin.',
    'prefix' => 'admin'
], function () {
    Route::apiResource('/users', UserController::class);
    Route::apiResource('/posts', PostController::class);
    Route::apiResource('posts.comments', PostCommentController::class)
        ->except(['store', 'update'])->scoped();
});
