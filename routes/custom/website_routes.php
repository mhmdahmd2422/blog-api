<?php

use App\Http\Controllers\Website\CategoryController;
use App\Http\Controllers\Website\CommentController;
use App\Http\Controllers\Website\PostController;
use Illuminate\Support\Facades\Route;

Route::group([
    'as' => 'website.',
], function () {
    Route::apiResource('/posts', PostController::class)
        ->only(['index', 'show']);
    Route::apiResource('posts.comments', CommentController::class)->scoped();
    Route::apiResource('categories', CategoryController::class)
        ->only(['index', 'show']);
});
